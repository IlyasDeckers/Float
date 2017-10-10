<?php
namespace App\Float\Consul;

use App\Float\Consul\Clients\HealthClient;

class Consul{
	private $_healthClient;

	public function getHealthClient(){
		if(null == $this->_healthClient){
			$this->_healthClient = new HealthClient();
		}

		return $this->_healthClient;
	}
} 