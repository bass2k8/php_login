<?php

include("constants.inc.php");

Class Database {

	private $_dbName; // Name of the Database.
	private $_connection; // Connection object.

	public function __construct($dbName){
		$this->_dbName = $dbName;
		$this->connect();
		$this->selectDatabase();
	}

	// Close the connection to the Database Server.
	public function __destruct(){
		mysql_close($this->_connection);
	}

	// Connecting to the Database Server.
	private function connect(){
		$this->_connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die("Could not connect to server: ".mysql_error());
		//echo "Connected to ".DB_SERVER." successfully.\n";
	}

	// Selecting the Database.
	private function selectDatabase(){
		mysql_select_db(DB_DATABASE, $this->_connection) or die("Could not select db: ".mysql_error());
		//echo "Selected ".DB_DATABASE." successfully.\n";
	}
	
}

?>