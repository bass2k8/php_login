<?php

require_once("constants.inc.php");
require_once("log.inc.php");

Class Database {

	private $_log; // Log object.
	private $_logging=false; // Define whether to output logging or not.

	private $_dbName; // Name of the Database.
	private $_connection; // Connection object.

	public function __construct($dbName, $logging){
		$this->_dbName = $dbName;
		
		$this->_logging=$logging;
		if($this->_logging) $this->_log = new Log(get_class($this));

		$this->_connect();
		$this->_selectDatabase();
	}

	// Close the connection to the Database Server.
	public function __destruct(){
		mysql_close($this->_connection);
		if($this->_logging) $this->_log->addToLog("Closed connection to ".DB_SERVER." successfully.");
	}

	// Connecting to the Database Server.
	private function _connect(){
		$this->_connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die("Could not connect to server: ".mysql_error());
		if($this->_logging) $this->_log->addToLog("Connected to <strong>".DB_SERVER."</strong> successfully.");
	}

	// Selecting the Database.
	private function _selectDatabase(){
		mysql_select_db($this->_dbName, $this->_connection) or die("Could not select db: ".mysql_error());
		if($this->_logging) $this->_log->addToLog("Selected <strong>".$this->_dbName."</strong> successfully.");
	}

	// Querying the Database.
	public function query($sql){
		mysql_query($sql, $this->_connection) or die("Could not select db: ".mysql_error());
		if($this->_logging) $this->_log->addToLog("Queried <strong>".$sql."</strong> successfully.");
	}

	// Select a Table.
	public function selectTable($table, $parameters){
		if(!$parameters) $this->query("SELECT * FROM ".$table);
		else $this->query("SELECT * FROM ".$table." ".$parameters);
		if($this->_logging) $this->_log->addToLog("Selected <strong>".$table."</strong> successfully.");
	}

}

?>