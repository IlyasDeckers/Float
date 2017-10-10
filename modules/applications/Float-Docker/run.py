#!/bin/bash

docker run -d \
    -p 8400:8400 \
    -p 8500:8500 \
    -p 8600:53/udp \
    --name consul \
    --net=host \
    -h node1 \
    progrium/consul -server -bootstrap

docker run -d \
    --name=registrator \
    --net=host \
    --volume=/var/run/docker.sock:/tmp/docker.sock \
    gliderlabs/registrator:latest \
    consul://192.168.0.101:8500
