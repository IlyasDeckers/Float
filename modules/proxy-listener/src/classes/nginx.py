#!/usr/bin/python

import os
import logging
import subprocess

class Nginx():
    def __init__(self):
        self.sites_enabled = '/etc/nginx/sites-enabled/'

    def create_vhost(self, vhost, vhostConf):
        template = '/etc/nginx/proxy-templates/vhost.conf'
        domain = vhost['ServiceTags'][0].split(' ',1)[0]
        
        # Create vhost if it does not exist in nginx
        if domain + '.conf' not in vhostConf:
            self.add_temp_cert(vhost)
            template = open(template,'r').read()

            config = template.format(ip = '0.0.0.0', 
                                     domain = domain, 
                                     backend = '192.168.0.101', 
                                     port = vhost['ServicePort'])

            file = open(self.sites_enabled + domain + '.conf', 'w+')
            file.write(config)
            file.close()
            logging.info('PROXY-LISTENER: Creating vhost ' + domain + '.conf domain')

    def delete_vhost(self, vhosts):
        for vhost in vhosts:
            os.remove(self.sites_enabled + vhost)
            logging.info('PROXY-LISTENER: Removing ' + vhost)

    def get_sites_enabled(self):
        '''
        Return a list of available vhost files in nginx
        '''
        return [x for x in os.listdir(self.sites_enabled) if x.endswith(".conf")]

    def add_temp_cert(self, vhost):
        '''
        Create a symbolic link to provide a temporary ssl certificate 
        for the new vhost untill a valid one has been installed
        '''
        domain = vhost['ServiceTags'][0].split(' ',1)[0]
        if not os.path.isfile('/etc/nginx/ssl/' + domain + '/cert.pem') and not os.path.isfile('/etc/nginx/ssl/' + domain + '/privkey.pem'):
            subprocess.call(['mkdir', '-p', '/etc/nginx/ssl/' + domain])
            subprocess.call(['ln', '-s', '/etc/nginx/ssl/nginx.crt', '/etc/nginx/ssl/' + domain + '/cert.pem'])
            subprocess.call(['ln', '-s', '/etc/nginx/ssl/nginx.key', '/etc/nginx/ssl/' + domain + '/privkey.pem'])

    def add__letsencrypt_cert(self, vhost):
        '''
        Create a symbolic link to /etc/nginx/ssl for the obtained ssl certificate
        '''
        domain = vhost['ServiceTags'][0].split(' ',1)[0]
        if not os.path.isfile('/etc/letsencrypt/live/' + domain + '/cert.pem') and os.path.isfile('/etc/letsencrypt/live/' + domain + '/privkey.pem'):
            subprocess.call(['rm', '-f', '/etc/nginx/ssl/' + domain + '/*'])
            subprocess.call(['ln', '-s', '/etc/letsencrypt/live/' + domain + '/cert.pem', '/etc/nginx/ssl/' + domain + '/cert.pem'])
            subprocess.call(['ln', '-s', '/etc/letsencrypt/live/' + domain + '/privkey.pem', '/etc/nginx/ssl/' + domain + '/privkey.pem'])
