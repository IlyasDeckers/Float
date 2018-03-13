#!/bin/python

from json import dumps
from .base import Base
import logging

class Monitor(Base):
    def run(self):
        if self.options['all']:
            logging.info('Monitoring all')
        if self.options['proxy']:
            logging.info('Monitoring proxy')
        if self.options['osd']:
            logging.info('Monitoring osd')