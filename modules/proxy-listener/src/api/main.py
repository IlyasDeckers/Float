#!/bin/python

import json
import logging
from recources import *
from flask import Flask
from routes import routes
from functools import wraps
from flask_restful import Resource, Api

class Config():
  def __init__(self):
    self.config_file = '/etc/float/config.json'

  def read(self):
    o = open(self.config_file)
    with o as json_data_file:
        data = json.load(json_data_file)
    return data
    o.close()

config = Config().read()
logging.basicConfig(filename=config['settings']['log_file'],
                    filemode='a',
                    format='%(asctime)s [ %(levelname)s ] - %(message)s',
                    datefmt='%m/%d/%Y  %H:%M:%S',
                    level=config['settings']['log_level'])
logging.info('PROXY-LISTENER: Starting proxy API.')

def routes(api):
    api.add_resource(Vhosts, '/vhosts/')
    api.add_resource(Vhost, '/vhost/')

class ProxyApi():
    def __init__(self):
        self.app = Flask(__name__)
        self.api = Api(self.app)

    def start(self):
        logging.info('PROXY-LISTENER: Starting proxy listener API.')
        routes(self.api)
        self.app.run(debug=False)

if __name__ == '__main__':
    ProxyApi().start()
