WORK IN PROGRESS
# Float
Shared web hosting using Docker Swarm as a backend. 

## Components
All the components are intended to run on seperate hosts.
  - Docker Swarm
  - Consul
  - Nginx (Proxy)
  - Proxmox LXC containers (optional)

# Installation
## Minimal setup
All services can run on one host for testing purposes:
```
$ sudo float --install standalone 
```
## Full setup
The minimal setup contains one Nginx instance with a consul client installed, a working consul cluster and a docker (or Proxmox) backend.
