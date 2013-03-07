<?php

require_once("Log.class.php");

Class Login {

	private $_log; // Log object.
	private $_logging=false; // Define whether to output logging or not.

	protected $_userID; // User ID.

	public function __construct($userID=false, $logging=false){

		$this->_logging=$logging;
		if($this->_logging) $this->_log = new Log(get_class($this));
		
		$this->_startSession();
		if($userID!==false) $this->_userID=$userID;

	}

	private function _startSession(){

		if(!isset($_SESSION) || empty($_SESSION)){
			session_start();
			return true;
		} else return false;

	}

	public function status(){

		if(isset($_SESSION["userID"]) && !empty($_SESSION["userID"])) return true;
		else return false;

	}

	public function login(){

		if($this->_check()){
			$_SESSION["userID"]=$this->_userID;
			if($this->_logging) $this->_log->addToLog("The User with ID <strong>".$this->_uid."</strong> has successfully logged in.");
			return true;
		}
		else return false;

	}

	public function logout(){

		if($this->status()){
			unset($_SESSION["userID"]);
			if($this->_logging) $this->_log->addToLog("The User with ID <strong>".$this->_uid."</strong> has successfully logged out.");
			return true;
		} else return false;

	}

}