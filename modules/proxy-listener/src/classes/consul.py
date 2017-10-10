#!/usr/bin/python

import sys
import logging
import requests
from config import Config
from requests.exceptions import ConnectionError

class Consul():
    def __init__(self):
        config = Config().read()['consul']
        self.api_url = 'http://' + config['host'] + ':' + config['port']
        
    def connect(self, path):
        try:
            return requests.get(self.api_url + path)
        except ConnectionError:
            logging.basicConfig(filename='/var/log/float/consul.log',
                                filemode='a',
                                format='%(asctime)s [ %(levelname)s ] - %(message)s',
                                datefmt='%m/%d/%Y  %H:%M:%S',
                                level=logging.DEBUG)
            logging.info('Can not connect to Consul backend using ' + self.api_url + '; Exiting.')
            sys.exit(1)

    def get_vhosts(self):
        response = self.connect("/v1/catalog/service/nginx").json() + self.connect("/v1/catalog/service/wordpress").json() + self.connect("/v1/catalog/service/proxmox-lxc").json()
        return response