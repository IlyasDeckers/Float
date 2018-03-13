<?php

namespace App\Http\Controllers\Docker;

use Alert;
use Redirect;
use Illuminate\Http\Request;
use Docker\API\Model\Container;
use Docker\API\Model\HostConfig;
use Docker\API\Model\PortBinding;
use Docker\API\Model\ResourceUpdate;
use Docker\API\Model\ContainerConfig;
/*use App\Http\Controllers\Controller;*/

class ContainerController extends DockerController
{
    public function containersPage()
    {
        return view('containers', [ 'containers' => $this->getContainers() ]);
    }

    public function containerPage($id)
    {
        return $this->getContainer($id);
    }

    public function createPage()
    {
        return view('createcontainer');
    }

    public function getContainers()
    {
        $containerManager = $this->docker->getContainerManager();
        $containers = $containerManager->findAll(['all' => true]);

        $data = array();
        $x=0;
        foreach ($containers as $container) {
            $x++;
            $ports = array();
            foreach ($container->getPorts() as $port) {
                $privatePort = $port->getPrivatePort();
                $publicPort = $port->getPublicPort();
                $ports[$x] = array(
                    'privatePort' => $privatePort,
                    'publicPort'  => $publicPort
                );
            }
            
            $data[$x] = array(
                'id'     => $container->getId(),
                'state'  => $container->getState(),
                'names'  => $container->getNames(),
                'image'  => $container->getImage(),
                'status' => $container->getStatus(),
                'ports'  => $ports
            );
        }

        $str = json_encode($data);

        return json_decode($str);
    }

    public function getContainer($id)
    {
        $containerManager = $this->docker->getContainerManager();
        $container = $containerManager->find($id);

        dd($container);
    }

    public function createContainer(Request $r)
    {
        $containerManager = $this->docker->getContainerManager();

        $containerConfig = new ContainerConfig();
        /*$container = new Container();

        $container->setName($r->domain);*/
        $containerConfig->setImage($r->image);
        $containerConfig->setHostname($r->domain);
        $containerConfig->setDomainname($r->domain);
        $containerConfig->setEnv(['SERVICE_TAGS=' . $r->domain]);

        $hostConfig = new HostConfig();
        $mapPorts = new \ArrayObject();

        $hostPortBinding = new PortBinding();
        $hostPortBinding->setHostPort();
        $hostPortBinding->setHostIp('0.0.0.0');

        $memory = new ResourceUpdate();
        $memory->setMemory(128);

        $mapPorts['80/tcp'] = [$hostPortBinding];

        $hostConfig->setPortBindings($mapPorts);
        /*$hostConfig->setCpusetCpus('0,5');*/
        $hostConfig->setMemory(128000000);
        $containerConfig->setHostConfig($hostConfig);

        try {
            $containerCreateResult = $containerManager->create($containerConfig, ['name' => $r->domain]);
            Alert::success('Container created');
        } catch (\Http\Client\Common\Exception\ClientErrorException $e) {
            if ($e->getMessage() == "Conflict") {
                Alert::warning('The domain <b>' . $r->domain . '</b> already exists in our configuration', 'Whoops!');
            } else {
                Alert::error('Whoops!');
            }
        }

        return redirect()->route('containers');
    }

    public function deleteContainer()
    {

    }

    public function startContainer(Request $r)
    {
        $containerManager = $this->docker->getContainerManager();
        $status = $containerManager->find($r->id);
        $status = $status->getState();

        if ($status->getPaused() == true) {
            $this->unpause($r->id);
        }

        $webSocketStream =  $containerManager->attachWebsocket($r->id, [
            'stream' => true,
            'stdin' => true,
            'stdout' => true,
            'stderr' => false
        ]);

        $container = $containerManager->start($r->id);
        sleep(1);
        $status = $containerManager->find($r->id);
        $status = $status->getState();

        if ( $status->getRunning() != true )
        {
            $stderr = $webSocketStream->read();
            Alert::warning('Container not started <pre>' . $stderr .'</pre>')->persistent('Close');
        } else {
            Alert::success('Container started');
        }

        return Redirect::back();
    }

    public function stopContainer(Request $r)
    {
        $containerManager = $this->docker->getContainerManager();
        $container = $containerManager->stop($r->id);
        sleep(1);
        $status = $containerManager->find($r->id);
        $status = $status->getState();

        if ( $status->getRunning() == false ) {
            Alert::success('Container stopped');
        }

        return Redirect::back();
    }

    public function pauseContainer(Request $r)
    {
        $containerManager = $this->docker->getContainerManager();
        $container = $containerManager->pause($r->id);
        sleep(1);
        $status = $containerManager->find($r->id);
        $status = $status->getState();

        if ( $status->getRunning() == false ) {
            Alert::success('Container Pauzed');
        }

        return Redirect::back();
    }

    private function unpause($id)
    {
        $containerManager = $this->docker->getContainerManager();
        $containerManager->unpause($id);
    }
}
