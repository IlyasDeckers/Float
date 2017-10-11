<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Consul;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function consul()
    {
        $consul = Consul::getHealthClient()->getHealthyServicesInstances('proxmox-80');
        return view('consul', ['consul' => $consul ]);
    }

    public function logs()
    {
        $file='/var/log/float/float.log';
        $lastpos = 0;
        $logs = file($file, FILE_IGNORE_NEW_LINES);

        /*dd($logs);*/

        return view('logs', ['logs' => array_slice($logs, '-30') ]);
    }
}
