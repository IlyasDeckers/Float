#!/bin/python

import time
import subprocess  
from watchdog.events import PatternMatchingEventHandler

class SslHandler(PatternMatchingEventHandler):
  patterns = ["*.pem", "*.crt", ".key"]

  def process(self, event):
      logging.info('PROXY-LISTENER: SSL certificate updated, reloading nginx')
      subprocess.call(['nginx', '-s', 'reload'])

  def on_modified(self, event):
    self.process(event)

  # def on_created(self, event):
  #     self.process(event)

  def on_deleted(self,event):
    self.process(event)