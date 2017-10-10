#!/usr/bin/python

import json

class Config():
    def read(self):
        with open('/etc/float/config.json') as json_data_file:
            data = json.load(json_data_file)
        return data