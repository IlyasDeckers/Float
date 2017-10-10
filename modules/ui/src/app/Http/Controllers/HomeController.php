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
        $consul = Consul::getHealthClient()->getHealthyServicesInstances('proxmox');
        dd($consul);
        return view('home', ['consul' => $consul ]);
    }
}
