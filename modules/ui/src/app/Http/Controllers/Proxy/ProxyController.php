<?php

namespace App\Http\Controllers\Proxy;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProxyController extends Controller
{
    public function index() 
    {
        $client = new Client();
        $res = $client->get('http://127.0.0.1:5000/vhosts/');

        return view('proxy', ['vhosts' => json_decode($res->getBody())]);
    }

    public function getVhost(Request $r)
    {
        $client = new Client();
        $res = $client->post('http://127.0.0.1:5000/vhost/', [
            'json' => [
                'vhost' => 'ilyasdeckers.be.conf'
            ]
        ]);

        return view('vhost', ['vhost' => json_decode($res->getBody())]);
    }
}
