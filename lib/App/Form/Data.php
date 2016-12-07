<?php

namespace App\Form;

/**
* Data class to be inherited in Form
*
* @author Moderator <pitsolu@gmail.com>
*/
abstract class Data{

	/**
	* Messages from validators
	*
	* @return array
	*/
	private $message;

	/**
	* Raw request values
	*
	* @return array
	*/
	private $rawVals;

	/**
	* Getter for validator factory
	*
	* @return App\Form\Validation\Factory
	*/
	protected function getValidationFactory(){

		return \App\Form\Validation\Factory::getInstance();
	}

	/**
	* Getter for request parameters
	*
	* @param string $val
	*
	* @throws \Exception
	* @return string
	*/
	protected function getParam($val){

		if(class_exists("Strukt\Rest\Request"))
			return \Strukt\Rest\Request::getParam($val);
	
		throw new \Exception("Unable to locate [Strukt\Rest\Request]!");
	}

	/**
	* Message setter
	*
	* @param string $key request parameter name
	* @param App\Form\Validation\Validator $validator
	*
	* @return void
	*/
	protected function setMessage($key, Validation\Validator $validator){

		$this->message[$key] = $validator->getMessage();
		$this->rawVals[$key] = $validator->getVal();
	}

	/**
	* Getter raw validator values
	*
	* @param string $key
	*
	* @return string
	*/
	public function get($key){

		return $this->rawVals[$key];
	}

	/**
	* Validation method to be overriden
	*
	* @return void
	*/
	protected function validation(){

		//do validation
	}

	/**
	* Execute validator and return compiled messages
	*
	* @return array
	*/
	public function validate(){

		$this->validation();

		foreach($this->message as $field=>$props)
      		foreach($props as $prop)
        		if(!$prop)
          			return array("is_valid"=>false, "messages"=>$this->message);

    	return array("is_valid"=>true, "messages"=>"None");
	}
}