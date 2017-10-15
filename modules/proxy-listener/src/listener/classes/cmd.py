#!/bin/python

import subprocess

class Cmd():
    def execute(self, cmd):
        response = subprocess.check_output(commands, shell=True)
        try:
            for command in commands:
                response = subprocess.check_output(command, shell=True)
                # result = [x for x in response.split("\n") if x]
        except subprocess.CalledProcessError as e:
            print 'error'
            logging.info('PROXY-LISTENER: ')
            return False