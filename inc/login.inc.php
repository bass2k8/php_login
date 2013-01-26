<?php

session_start();
require_once("log.inc.php");
require_once("db.inc.php");

Class Login {

	private $_log; // Log object.
	private $_logging=false; // Define whether to output logging or not.

	private $_db; // Database object.
	private $_uid; // User ID.

	public function __construct($logging){
		$this->_logging=$logging;
		if($this->_logging) $this->_log = new Log(get_class($this));

		$this->_initDB();
		if($this->loginStatus()) $this->_uid = $_SESSION["uid"];
	}

	private function _initDB(){
		$this->_db = new Database("bass2k8_login", true);
	}

	public function loginStatus(){
		if($_SESSION["uid"] && !empty($_SESSION["uid"])) return true;
		else return false;
	}

	public function login($uid){
		unset($_SESSION["uid"]);
		$_SESSION["uid"] = $uid;
		$this->_uid = $uid;
		if($this->_logging) $this->_log->addToLog("The User with ID <strong>".$this->_uid."</strong> has successfully logged in.");
	}

	public function logout(){
		unset($_SESSION["uid"]);
		if($this->_logging) $this->_log->addToLog("The User with ID <strong>".$this->_uid."</strong> has successfully logged out.");
		unset($this->_uid);
	}

}

?>