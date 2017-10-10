#!/bin/python

import uuid
from app import Mysql, Docker

username = 'ilydhdhkjhhh5ww'
database = username + '_t'
domain = 'ilyasdeckers.be'
app_id = uuid.uuid4().hex
vhost_server_name = app_id + '.ilyas.phasehosting.io'
server_name = vhost_server_name + ' ' + domain

# password=os.urandom(12)
password=uuid.uuid4().hex

# # Create a database for the new application
Mysql().create_database(database, username, password)

# # Create a new docker container
Docker().run('wordpress', server_name, vhost_server_name, username, password, database)

print 'You can now visit your application via:'
print '    - http://' + app_id + '.ilyas.phasehosting.io'
print '    - https://' + app_id + '.ilyas.phasehosting.io'