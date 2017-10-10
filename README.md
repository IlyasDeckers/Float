## Setup

    - Consul
    - Standalone Nginx proxies
    - Docker and/or Proxmox backends
    - DNS Load Balancing

## Consul
For service discovery we use consul.

```bash
mkdir /etc/consul
cat << EOF > /etc/consul/config.json
{                                             
  "datacenter": "prox3",                                           
  "data_dir": "/tmp/consul",
  "log_level": "DEBUG",                                             
  "node_name": "consul02",
  "server": true,                                               
  "bootstrap_expect": 3,                                               
  "ui_dir": "/opt/consul-ui",                                        
  "bind_addr": "10.100.3.4",                                          
  "leave_on_terminate": true,
  "start_join": [                                                   
    "10.100.3.2,                                                   
    "10.100.3.3,                                                    
    "10.100.3.4"
  ]                                               
}
EOF
```

### Nginx

Install a fresh server with Ubuntu 16.04

Download and run the install script

```bash
cd && git pull https://github.com/IlyasDeckers/relay.git
cd relay/Nginx
chmod +x install.sh
sh install
```

Repeat this step accross x amount of proxies you want to use

### Docker

Install docker like you normally would

```bash
docker run  -d \
    --name float-proxmox \
    --net host \
    --restart always
    phasehosting/float-proxmox
```

