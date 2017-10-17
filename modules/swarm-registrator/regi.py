#!/usr/bin/env python
# -*- coding: utf-8 -*-
# vim:fenc=utf-8
#
# Copyright © 2017 zc <zc@www>
#
# Distributed under terms of the MIT license.

"""
Service registrator agent
"""

from collections import namedtuple
from threading import Thread
from time import sleep

import docker
import consul
import re
import os

import json

# logging config
import logging
logging.basicConfig(format='%(asctime)s : %(levelname)s : %(message)s',
                    level=logging.INFO)


# some classes
Service = namedtuple('Service', ['name', 'port', 'labels', 'tags'])


# get some environment variables
CONSUL_HOST = os.getenv('CONSUL_HOST', '127.0.0.1')
CONSUL_PORT = int(os.getenv('CONSUL_PORT', 8500))
UPDATE_INTERVAL = int(os.getenv('UPDATE_INTERVAL', 5))

CLUSTER_ID = os.getenv('CLUSTER_ID', 'Noname')


def get_exposed_services(
     sv_id, sv_name, spec_labels):

    client = docker.from_env()
    service = client.services.get(sv_id)


    """
    Gets all exposed services corresponding to a service
    """
    id_tag = 'id={}'.format(sv_id)
    # spec_labels = sv.attrs['Spec']['Labels']

    global_port = None
    global_names = None

    if 'com.pms.port' in spec_labels:
        global_port = int(spec_labels['com.pms.port'])
    else:
        global_port = int(service.attrs['Endpoint']['Ports'][0]['PublishedPort'])
    if 'com.pms.name' in spec_labels:
        global_names = spec_labels['com.pms.name'].split(',')
    else:
        global_names = [sv_name]

    if 'com.pms.tags' in spec_labels:
        global_tags = spec_labels['com.pms.tags'].split(',')
    else:
        global_tags = []

    global_tags.append(id_tag)
    global_tags.append('cluster={}'.format(CLUSTER_ID))

    global_labels = {}
    labels = {}
    names = {}
    tags = {}
    labels = {}
    for (k, v) in spec_labels.items():
        if k.startswith('com.pms.labels'):
            m = re.match('com.pms.labels.([^.]+)', k)
            if m:
                label = m.group(1)
                global_labels[label] = v

        elif k.startswith('com.pms.services.name'):
            match = re.match('com\.pms\.services\.name\.(\d+)', k)
            if match:
                # add exposed service
                port = int(match.group(1))
                names[port] = v.split(',')

        elif k.startswith('com.pms.services.labels'):
            m = re.match(
                'com\.pms\.services\.labels\.(\d+)\.([^.]+)', k)
            if m:
                port = int(m.group(1))
                label = m.group(2)
                if port not in labels:
                    labels[port] = {}
                labels[port][label] = v
        else:
            m = re.match(
                'com\.pms\.services\.tags\.(\d+)', k)
            if m:
                port = m.group(1)
                sv_tags = v.split(',')
                tags[port] = sv_tags

    # build final service data
    services = []
    if len(names) > 0:
        for (port, names) in names.items():
            sv_tags = []
            if port in tags:
                sv_tags += tags[port]
            if global_tags:
                sv_tags += global_tags

            sv_labels = {}
            if port in labels:
                sv_labels.update(labels[port])
            if global_tags:
                sv_labels.update(global_labels)

            sv_tags += ['{}={}'.format(k, v)
                        for (k, v) in sv_labels.items()]

            for name in names:
                services.append(Service(
                    name=name, port=port,
                    labels=sv_labels, tags=sv_tags))

    if len(services) == 0 and \
            global_names and global_port:
        global_tags = global_tags + ['{}={}'.format(k, v)
                                     for (k, v) in global_labels.items()]
        for global_name in global_names:
            services.append(Service(
                name=global_name,
                port=global_port,
                labels=global_labels,
                tags=global_tags
            ))
    return services


def update_service_discovery(
     consul_client, services, instances):
    """
    Update service instances to consul agent
    """
    catalog = consul_client.catalog
    agent = consul_client.agent
    cluster_tag = 'cluster={}'.format(CLUSTER_ID)
    for service in services:
        # fetch old instances from consul
        # keep only instances that in same swarm
        # and from same service
        sv_tags = frozenset(service.tags)
        old_instances = [(i['ServiceAddress'],
                          frozenset(i['ServiceTags']), i['ServicePort'])
                         for i in catalog.service(service.name)[1]
                         if cluster_tag in i['ServiceTags']]
        old_instances = set(old_instances)
        # remove instances that currently has no running instance
        remove_instances = [x[0] for x in old_instances
                            if x[0] not in instances]

        # update instances that tags change
        new_instances = [x for x in instances
                         if (x, sv_tags, service.port) not in old_instances]
        # update instances
        if len(new_instances) > 0:
            logging.info("Registering new instances %s for service %s",
                         new_instances, service)
            for instance in new_instances:
                agent.service.register(
                    service.name,
                    service_id='{}_{}'.format(service.name, instance),
                    address=instance,
                    port=service.port,
                    tags=service.tags
                )

        if len(remove_instances):
            logging.info('Deregistering instances %s from service %s',
                         old_instances, service)
            for instance in remove_instances:
                print '{}_{}'.format(service.name, instance)
                agent.service.deregister(
                    '{}_{}'.format(service.name, instance)
                )


def remove_consul_services(
     consul_client,
     docker_sv=None,
     sv_name=None):
    all_services = consul_client.agent.services()
    service_instances = []
    docker_sv_tag = 'id={}'.format(docker_sv)
    cluster_tag = 'cluster={}'.format(CLUSTER_ID)
    for (consul_sv_id, desc) in all_services.items():
        if sv_name and desc['Service'] != sv_name:
            continue
        tags = desc['Tags']
        if not (cluster_tag in tags and docker_sv_tag in tags):
            continue

        service_instances.append(consul_sv_id)

    logging.info("Deregistering service instances %s", service_instances)
    for service in service_instances:
        print service
        print consul_client.agent.service.deregister(service)
    logging.info("Services deregistered!")


def update_removed_services(client, consul_client):
    events = client.events(
        filters={'type': 'service'},
        decode=True)
    for e in events:
        action = e['Action']
        docker_sv = e['Actor']['ID']
        if action == 'remove':
            # check_tag = 'id={}'.format(service_id)
            remove_consul_services(consul_client, docker_sv)
        elif action == 'update':
            sv = client.services.get(docker_sv)
            if 'PreviousSpec' not in sv.attrs:
                continue
            sv_name = sv.name
            prev_labels = sv.attrs['PreviousSpec']['Labels']
            new_labels = sv.attrs['Spec']['Labels']
            old_services = set([s.name for s in
                                get_exposed_services(
                                    docker_sv, sv_name, prev_labels)])
            new_services = set([s.name for s in
                                get_exposed_services(
                                    docker_sv, sv_name, new_labels)])
            remove_services = old_services - new_services
            for consul_sv in remove_services:
                remove_consul_services(consul_client, docker_sv, consul_sv)


def update_running_services(
     client, consul_client):
    while True:
        try:
            # update id2addr maps
            nodes = client.nodes.list()
            id2addr = {}
            for n in nodes:
                node_id = n.id
                node_addr = n.attrs['Status']['Addr']
                id2addr[node_id] = node_addr

            # get all running tasks
            services = client.services.list()
            for sv in services:
                labels = sv.attrs['Spec']['Labels']
                exposed_services = get_exposed_services(sv.id, sv.name, labels)
                if len(exposed_services) > 0:
                    instances = set([])
                    tasks = sv.tasks(filters={
                        'desired-state': 'running'})
                    for t in tasks:
                        node_id = t['NodeID']
                        if node_id in id2addr:
                            node_addr = id2addr[node_id]
                            instances.add(node_addr)

                    # update service discovery
                    update_service_discovery(consul_client,
                                             exposed_services,
                                             instances)

        except Exception, e:
            logging.error("Exception: %s", e)

        # sleep for 10s
        sleep(UPDATE_INTERVAL)


def main():
    logging.info("Starting agent")
    client = docker.from_env()
    consul_client = consul.Consul(CONSUL_HOST, CONSUL_PORT)

    # create thread to update remove service events
    remove_thread = Thread(target=update_removed_services,
                           args=(client, consul_client))
    remove_thread.daemon = True
    remove_thread.start()

    # create thread to periodically update service address
    main_thread = Thread(target=update_running_services,
                         args=(client, consul_client))
    main_thread.daemon = True
    main_thread.start()

    # join
    while True:
        sleep(1)


if __name__ == '__main__':
    main()