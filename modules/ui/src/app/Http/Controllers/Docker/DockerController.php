<?php

namespace App\Http\Controllers\Docker;

use Docker\Docker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DockerController extends Controller
{
    protected $docker;

    public function __construct()
    {
        $this->docker = new Docker();
    }
}
