#!/bin/python

import os
from flask_restful import Resource
from stat import ST_CTIME, ST_MTIME

class Vhosts(Resource):
    def __init__(self):
        self.path = '/etc/nginx/sites-enabled/'

    def stat(self, vhost):
        try:
            return os.stat(self.path + vhost)
        except IOError:
            print "failed to get information about", vhost

    def get(self):
        vhosts = os.listdir(self.path)
        response = []

        for vhost in vhosts:
            stat = self.stat(vhost)
            vhost = {
                'name': vhost,
                'path': self.path,
                'created_on': stat[ST_CTIME],
                'modified_on': stat[ST_MTIME]
            }
            response.append(vhost)

        return response