<?php

namespace App\Http\Controllers;

use Alert;
use Redirect;
use Illuminate\Http\Request;

class DockerController extends Controller
{
    private $docker;

    public function __construct()
    {
        $this->docker = new \Docker\Docker();
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
        $data = json_decode($str);

        return view('containers', [ 'containers' => $data ]);
    }

    public function getContainer($id)
    {
        $containerManager = $this->docker->getContainerManager();
        $container = $containerManager->find($id);

        dd($container);
    }

    public function createContainerPage()
    {   
        dd('create_container');
    }

    public function createContainer()
    {
        
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
