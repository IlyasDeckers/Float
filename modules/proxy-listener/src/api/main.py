#!/bin/python

import json
import logging
from routes import routes
from auth.auth import requires_auth
from functools import wraps
from flask import Flask
from flask_restful import Resource, Api

class ProxyApi():
    def __init__(self):
        self.app = Flask(__name__)
        self.api = Api(self.app)

    def start(self):
        logging.info('PROXY-LISTENER: Starting proxy listener API.')
        routes(self.api)
        self.app.run(debug=False)