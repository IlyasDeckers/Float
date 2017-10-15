#!/bin/python

from vhosthandler import VhostHandler
from sslhandler import SslHandler
from watchdog.observers import Observer

class Watcher():
  def start(self):
    logging.info('PROXY-LISTENER: Starting Proxy Listener')
    observer = Observer()
    observer.schedule(VhostHandler(), path='/etc/nginx/sites-enabled/')
    observer.schedule(SslHandler(), path='/etc/nginx/ssl/', recursive=True)
    observer.start()
    logging.info('PROXY-LISTENER: Nginx vhost watcher started')
    logging.info('PROXY-LISTENER: Nginx certificate watcher started')
