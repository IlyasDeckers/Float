#!/bin/python

import time
import logging
import subprocess
from watchdog.events import PatternMatchingEventHandler 

class VhostHandler(PatternMatchingEventHandler):
  patterns = ["*.conf", "*.cnf"]

  def process(self, event):
    logging.info('PROXY-LISTENER: Vhost configuration has changed reloading Nginx')
    time.sleep(1)
    subprocess.call(['nginx', '-s', 'reload'])

  def on_modified(self, event):
      self.process(event)

  # def on_created(self, event):
  #     self.process(event)

  def on_deleted(self,event):
      self.process(event)