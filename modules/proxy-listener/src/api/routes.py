#!/bin/python

from recources import *

def routes(api):
    api.add_resource(Vhosts, '/vhosts/')
    api.add_resource(Vhost, '/vhost/')