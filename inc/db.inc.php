<?php

require_once("constants.inc.php");
require_once("log.inc.php");

Class Database {

	private $_log; // Log object.
	private $_logging=false; // Define whether to output logging or not.

	private $_dbName; // Name of the Database.
	private $_query; // SQL Query.
	private $_db; // PDO database object.

	public function __construct($dbName, $logging){
		$this->_dbName = $dbName;
		$this->_query=false;

		$this->_logging=$logging;
		if($this->_logging) $this->_log = new Log(get_class($this));

		$this->_connect();
	}

	// Close the connection to the Database Server.
	public function __destruct(){
		if($this->_query) $this->_query->closeCursor();
		$this->_db=null;
		if($this->_logging) $this->_log->addToLog("Closed connection to <strong>".DB_SERVER."</strong> successfully.");
	}

	// Connecting to the Database Server.
	private function _connect(){
		$this->_db = new PDO("mysql:host=".DB_SERVER.";dbname=".$this->_dbName, DB_USER, DB_PASS);
		if($this->_logging) $this->_log->addToLog("Connected to <strong>".DB_SERVER."</strong> and selected the database <strong>".$this->_dbName."</strong> successfully.");
	}

	// Querying the Database.
	public function query($sql){
		$this->_query = $this->_db->query($sql);
		if($this->_logging) $this->_log->addToLog("Queried <strong>".$sql."</strong> successfully.");
	}

	// Fetch association.
	public function fetchAssociation(){
		if($this->_query){
			if($this->_logging) $this->_log->addToLog("Fetched the association successfully.");
			return $this->_query->fetch(PDO::FETCH_ASSOC);
		}
	}

	// Select a Table.
	public function selectTable($table, $parameters){
		if(!$parameters) $this->query("SELECT * FROM ".$table);
		else $this->query("SELECT * FROM ".$table." ".$parameters);
		if($this->_logging) $this->_log->addToLog("Selected <strong>".$table."</strong> successfully.");
	}

}

?>