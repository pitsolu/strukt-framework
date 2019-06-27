<?php

namespace App\Contract;

/**
* Abstract Registry class
*
* Uses:
*
* 	App\Data\Controller
* 	App\Data\Transformer
*
* @author Moderator <pitsolu@gmail.com>
*/
abstract class Registry{
	
	/**
	* Getter for singleton registry instance
	*
	* @return \Strukt\Framework\Registry
	*/
	protected function getInstance(){

		return \Strukt\Core\Registry::getInstance();
	}

	/**
	* Getter for registry value
	*
	* @param string $key can use dot notation
	*
	* @return mixed
	*/
	protected function get($key){

		return self::getInstance()->get($key);
	}
}