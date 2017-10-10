<?php
namespace App\Float\Consul\Entities\Health;

use App\Float\Consul\Entities\IEntity;

class ServiceEntity implements IEntity{
	public $_name;
	public $_address;
	public $_port;
	public $_tags;
	public $_id;

	public static function fromArray($array){	
		return new ServiceEntity($array->Service, $array->Address, $array->ID, $array->Tags[0], $array->Port);
	}

	private function __construct($name, $address, $id, $tags, $port){
		$this->_name = $name;
		$this->_address = $address;
		$this->_port = $port;
		$this->_id = $id;
	}

	public function getName(){
		return $this->_name;
	}

	public function getAddress(){
		return $this->_address;
	}

	public function getPort(){
		return $this->_port;
	}
}
