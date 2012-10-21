<?php

include("db.inc.php");

Class Login {

	private $_db; // Database object.

	public function __construct(){
		session_start();
		$this->_initDB();
	}

	private function _initDB(){
		$this->_db = new Database("bass2k8_login", true);
	}

}

?>