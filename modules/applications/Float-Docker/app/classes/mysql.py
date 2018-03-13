#!/bin/python

import uuid
import shlex
import os
import logging
import subprocess
import MySQLdb

class Mysql():
    def __init__(self):
        logging.basicConfig(filename='/var/log/relay/relay_connector.log',
                            filemode='a',
                            format='%(asctime)s [ %(levelname)s ] - %(message)s',
                            datefmt='%m/%d/%Y  %H:%M:%S',
                            level=logging.DEBUG)

        self.db = MySQLdb.connect(host="localhost", user="root", passwd="xxx")

    def create_database(self, database, database_user, password):
        query = "create database if not exists " + database + ";"
        query_ = "grant all on " + database + ".* to '" + database_user + "'@'%';"

        try:
            c = self.db.cursor()
            c.execute("create database if not exists " + database + ";")
            c.execute("create user if not exists '" + database_user + "'@'%' identified by '" + password + "';")
            c.execute("grant all on " + database + ".* to '" + database_user + "'@'%';")
        except (MySQLdb.Error, MySQLdb.Warning) as e:
            logging.warning(e) 
        finally:
            c.close()

    # def delete_database(self, database):
    #     pass
