#!/bin/bash

echo "Installing docker"
cd ~ && curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add - && \
add-apt-repository "deb [arch=amd64] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" && \
apt-get update && apt-get install -y docker-ce

echo "Creating user float"
$password = `mkpasswd "$pass"`
useradd -p "" -d /home/float -m -g users -s /bin/bash "float"
usermod -aG docker float
echo "\n Success: user created  "
echo "\n  user: float"
echo "  password: " $password

# Query consul to look for existing swarm cluster, if not init swarm

if $query == true
    if $manager == true
        docker swarm join --token $token_manager
    else
        docker swarm join --token $token_worker
else
    docker swarm init

    $token_manager = docker swarm join-token manager | grep token | awk '{ print $5 " " $6 }'
    # register manager with consul

    $token_manager = docker swarm join-token | grep token | awk '{ print $5 " " $6 }'
    # register worker with consul


echo "\n Installing & starting float-registrator"
ln -s /home/float/float/usr/bin/float-registrator /usr/bin/
cp /home/float/float/etc/init.d/float-registrator  /etc/init.d/float-registrator 
systemctl daemon-reload && service float-registrator start
