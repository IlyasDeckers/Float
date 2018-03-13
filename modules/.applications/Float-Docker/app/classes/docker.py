#!/bin/python

import shlex
import subprocess

class Docker():
    def run(self, app, server_name, domain, username, password, database):
        subprocess.Popen(shlex.split("docker run -d -P --net sites --name " + domain + " -e 'SERVICE_TAGS=" + server_name +"' -e 'DB_HOST=192.168.0.101:3306' -e 'DB_USER=" + username + "' -e 'DB_PASSWORD=" + password + "' -e 'DB_NAME=" + database + "' phasehosting/" + app ))

    # def start():
    #     pass

    # def stop():
    #     pass

    # def delete():
    #     pass