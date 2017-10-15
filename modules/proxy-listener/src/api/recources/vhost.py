#!/bin/python

import os
from flask_restful import Resource, reqparse
from stat import ST_CTIME, ST_MTIME

class Vhost(Resource):
    def __init__(self):
        self.path = '/etc/nginx/sites-enabled/'

    def stat(self, vhost):
        try:
            return os.stat(self.path + vhost)
        except IOError:
            print "failed to get information about", vhost

    def post(self):
        parser = reqparse.RequestParser()
        parser.add_argument('vhost', type=str, required=True, location='json')

        args = parser.parse_args(strict=True)
        stat = self.stat(args['vhost'])
        response = []

        r = [{
            "name": args['vhost'],
            "file_contents": open(self.path + args['vhost'],'r').read(),
            "created_at": stat[ST_CTIME],
            "modified_at": stat[ST_MTIME]
        }]

        return r

    def put(self):
        parser = reqparse.RequestParser()
        parser.add_argument('vhost', type=str, required=True, location='json')
        parser.add_argument('config', type=str, required=True, location='json')

        args = parser.parse_args(strict=True)

        file = open(self.path + args['vhost'], 'w')
        file.write(args['config'])
        file.close()