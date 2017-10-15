#!/usr/bin/python

import os
import logging
import subprocess
from cmd import Cmd
from certificates import Certificates

class Nginx():
    def __init__(self):
        self.sites_enabled = '/etc/nginx/sites-enabled/'

    def create_vhost(self, vhost, vhostConf):
        template = '/etc/nginx/proxy-templates/vhost.conf'
        domain = vhost['ServiceTags'][0].split(' ',1)[0]
        
        # Create vhost if it does not exist in nginx
        if domain + '.conf' not in vhostConf:
            logging.info('PROXY-LISTENER: Creating vhost ' + domain + '.conf')
            Certificates(domain).add_temp_cert()
            # self.letsencrypt(domain)
            template = open(template,'r').read()

            config = template.format(ip = '0.0.0.0', 
                                     domain = domain, 
                                     backend = '192.168.0.101', 
                                     port = vhost['ServicePort'])

            file = open(self.sites_enabled + domain + '.conf', 'w+')
            file.write(config)
            file.close()

    def delete_vhost(self, vhosts):
        for vhost in vhosts:
            logging.info('PROXY-LISTENER: Removing ' + vhost)
            os.remove(self.sites_enabled + vhost)
            Certificates(vhost.replace('.conf', '')).delete_cert()
            

    def get_sites_enabled(self):
        '''
        Return a list of available vhost files in nginx
        '''
        return [x for x in os.listdir(self.sites_enabled) if x.endswith(".conf")]