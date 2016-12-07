<?php

namespace App\Form\Validation;

/**
* Validator class
*
* @author Moderator <pitsolu@gmail.com>
*/
class Validator{

	/**
	* Value to undergo validation
	*
	* @var string
	*/
	private $val;

	/**
	* Failure or success messages for each condition
	*
	* @var array
	*/
	private $message;

	/**
	* Constructor get validation value
	*/
	public function __construct($val=null){

		if(!is_null($val))
			$this->setVal($val);
	}

	/**
	* Setter for validation value
	*
	* @param string $val
	*
	* @return App\Form\Validation\Validator
	*/
	public function setVal($val){

		$this->val = $val;

		return $this;
	}

	/**
	* Getter for validation value
	*
	* @return string
	*/
	public function getVal(){

		return $this->val;
	}

	/**
	* Check is value is alpha
	*
	* @return App\Form\Validation\Validator
	*/
	public function isAlpha(){

		$this->message["is_alpha"] = false;
		if(ctype_alpha(str_replace(" ", "", $this->getVal())))
			$this->message["is_alpha"] = true;

		return $this;
	}

	/**
	* Check is value is alphanumeric
	*
	* @return App\Form\Validation\Validator
	*/
	public function isAlphaNum(){

		$this->message["is_alphanum"] = false;
		if(ctype_alnum(str_replace(" ", "", $this->getVal())))
			$this->message["is_alphanum"] = true;

		return $this;
	}

	/**
	* Check is value is numeric
	*
	* @return App\Form\Validation\Validator
	*/
	public function isNumeric(){

		$this->message["is_num"] = false;
		if(is_numeric($this->getVal()))
			$this->message["is_num"] = true;

		return $this;
	}

	/**
	* Check is value is email
	*
	* @return App\Form\Validation\Validator
	*/
	public function isEmail(){

		$this->message["is_email"] = false;
		if(filter_var($this->getVal(), FILTER_VALIDATE_EMAIL))
			$this->message["is_email"] = true;

		return $this;
	}

	/**
	* Check is value is date
	*
	* @return App\Form\Validation\Validator
	*/
	public function isDate($format="Y-m-d"){

		$date = \DateTime::createFromFormat($format, $this->getVal());
		$err = \DateTime::getLastErrors();

		$this->message["is_date"] = false;
		if($err['warning_count'] == 0 && $err['error_count'] == 0)
			$this->message["is_date"] = true;

		return $this;
	}

	/**
	* Check is value is not empty
	*
	* @return App\Form\Validation\Validator
	*/
	public function isNotEmpty(){

		$this->message["is_not_empty"] = true;
		if(empty($this->getVal()))
			$this->message["is_not_empty"] = false;

		return $this;
	}

	/**
	* Check is value is in enumerator
	*
	* @return App\Form\Validation\Validator
	*/
	public function isIn($enum){

		if(!is_array($enum))
			throw new \Exception("App\Form\Validation\Validator::isIn only takes array!");

		$this->message["in_enum"] = false;
		if(in_array($this->getVal(), $enum))
			$this->message["in_enum"] = true;

		return $this;
	}

	/**
	* Check values are equal
	*
	* @return App\Form\Validation\Validator
	*/
	public function equalTo($val){

		$this->message["equal_to"] = true;
		if($val !== $this->getVal())
			$this->message["equal_to"] = false;

		return $this;
	}

	/**
	* Check length
	*
	* @return App\Form\Validation\Validator
	*/
	public function isLen($len){

		$this->message["is_valid_length"] = false;
		if(strlen($this->getVal()) == $len)
			$this->message["is_valid_length"] = true;

		return $this;
	}

	/**
	* Getter for messages
	*
	* @return array
	*/
	public function getMessage(){

		return $this->message;
	}
}