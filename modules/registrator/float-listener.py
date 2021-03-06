#!/bin/python

from threading import Thread, Event

import logging
from logging import handlers
from logging.handlers import RotatingFileHandler

import requests
import docker
import consul

import json
import time
import sys

LOGFILE = '/var/log/float/float-listener.log'
log = logging.getLogger('')
log.setLevel(logging.INFO)
format = logging.Formatter('%(asctime)s [ %(levelname)s ] - %(message)s',)

ch = logging.StreamHandler(sys.stdout)
ch.setFormatter(format)
log.addHandler(ch)

fh = handlers.RotatingFileHandler(LOGFILE, maxBytes=(1048576*5), backupCount=3)
fh.setFormatter(format)
log.addHandler(fh)
 
logging.getLogger("requests").setLevel(logging.WARNING)

class Consul():
    def __init__(self, address="127.0.0.1", port="8500"):
        self.port = port
        self.address = address
        self.c = self.connect()
        self.api_url = 'http://' + address + ':' + port

    def connect(self):
        return consul.Consul(self.address, self.port)

    def test_connection(self):
        self.c.agent.self()

    def get_services(self):
        return self.c.catalog.services()

    def get_service(self, service):
        return self.c.catalog.service(service)

    def deregister_service(self,service_id):
        path = "/v1/agent/service/deregister/" + service_id
        requests.put(self.api_url + path)

    def register_service(
       self, service_name, service_id, port, address, tags):
        # register(name, service_id=None, address=None, port=None, tags=None, check=None, token=None, script=None, interval=None, ttl=None, http=None, timeout=None, enable_tag_override=False)
        self.c.agent.service.register(service_name, service_id=service_id,  port=port, address=address, tags=tags)

class Docker():
    def __init__(self):
        self.client = docker.from_env()

    def events(self):
        try:
            for block in self.client.events():
                for line in block.splitlines():
                    line = json.loads(line)
                    self.filter(line)
        except Exception, e:
            logging.error("Exception: %s", e)

    def service(self, service_id):
        return self.client.services.get(service_id)

    def services(self):
        return self.client.services.list()

    def filter(self, event):
        service_name = event['Actor']['Attributes']['name']
        if event['Action'] == 'create':
            logging.info('Docker event (create) on %s', service_name)
        if event['Action'] == 'update':
            logging.info('Docker event (update) on %s', service_name)
            Registrator().create(event)
        if event['Action'] == 'remove':
            logging.info('Docker event (remove) on %s', service_name)
            Registrator().delete(event['Actor']['ID'])

class Registrator():
    def start(self):
        run_event = Event()
        run_event.set()

        t_events = Thread(target=Docker().events)
        t_events.daemon = True
        t_events.start()

    def create(self, event):
        service = Docker().service(event['Actor']['ID'])
        labels = service.attrs['Spec']['Labels']

        if 'com.float.ignore' in labels:
            if labels['com.float.ignore'] == 'true':
                logging.info('Ignored service %s in.', service)
                return False

        logging.info('Registering service %s in.', service)

        if 'com.float.tags' in labels:
            tags = labels['com.float.tags'].split(',')
        else:
            tags = []
        if 'com.float.app' in labels:
            app = labels['com.float.app'].split(',')[0]
        else:
            app = []
        if 'com.float.port' in labels:
            port = labels['com.float.port'].split(',')[0]
        else:
            for p in service.attrs['Endpoint']['Ports']:
                if p['TargetPort'] == 80:
                    port = p['PublishedPort']

        addr = service.attrs['Endpoint']['VirtualIPs'][0]['Addr']

        Consul().register_service(app, service.id, port, addr, tags)

    def check_dangling_services(self):
        d_services = []
        for s in Docker().services():
            d_services.append(Docker().service(s.id).id)

        c_services = []
        for s in Consul().get_services()[1:]:
            for x in s:
                if x != 'consul':
                    service = Consul().get_service(x)
                    for i in service[1:][0]:
                        c_services.append(i['ServiceID'])

        for service in sorted(set(c_services) - set(d_services)):
            logging.info('Found a dangling service %s in consul. Deleting', service)
            self.delete(service)

    def delete(self, service):
        Consul().deregister_service(service)

def main():
    logging.info('Starting float-listener')
    Registrator().start()
    Registrator().check_dangling_services()

    try:    
        while 1:
            time.sleep(10)
    except (KeyboardInterrupt, SystemExit):
        logging.info('Stopping float-listener')

if __name__ == '__main__':
    main()