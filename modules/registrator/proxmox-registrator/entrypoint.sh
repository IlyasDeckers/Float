#!/bin/sh

function populate_config() { 
  jq 'map(if .ParameterKey == \"$1\" 
          then . + {\"ParameterValue\":\"$2"} 
          else . 
          end)'
}

PROX_USER=${PROX_USER}
PROX_PASS=${PROX_PASS}
PROX_HOST=${PROX_HOST}
PROX_PORT=${PROX_PORT}
CONSUL_ADDRESS=${CONSUL_ADDRESS:=127.0.0.1}
CONSUL_PORT=${CONSUL_PORT:=8500}

cat /etc/float-proxmox/config.json | 
  populate-config Project jess-project |
  populate-config DockerInstanceType t2.micro > populated_config.json