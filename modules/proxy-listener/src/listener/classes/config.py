#!/usr/bin/python

import json

class Config():
  def __init__(self):
    self.config_file = '/etc/float/config.json'

  def read(self):
    o = open(self.config_file)
    with o as json_data_file:
        data = json.load(json_data_file)
    return data
    o.close()