<?php

include("constants.inc.php");

Class Database {

	private $_dbName; // Name of the Database.
	private $_connection; // Connection object.
	private $_logging=false; // Define whether to output logging or not.
	private $_log = array(); // Log Array.

	public function __construct($dbName, $logging){
		$this->_dbName = $dbName;
		$this->_logging=$logging;
		$this->_connect();
		$this->_selectDatabase();
	}

	// Close the connection to the Database Server.
	public function __destruct(){
		mysql_close($this->_connection);
		if($this->_logging){
			$this->_log("Closed connection to ".DB_SERVER." successfully.");
			$this->outputLog();
		}
	}

	// Add to the log.
	private function _log($text){
		array_push($this->_log, $text);
	}

	// Output the log in an un-ordered list.
	public function outputLog(){
		echo "<ul id=\"database_log\">\n";
		foreach ($this->_log as $log) {
			echo "\t<li>".$log."</li>\n";
		}
		echo "</ul>\n";
	}

	// Connecting to the Database Server.
	private function _connect(){
		$this->_connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die("Could not connect to server: ".mysql_error());
		if($this->_logging) $this->_log("Connected to <strong>".DB_SERVER."</strong> successfully.");
	}

	// Selecting the Database.
	private function _selectDatabase(){
		mysql_select_db($this->_dbName, $this->_connection) or die("Could not select db: ".mysql_error());
		if($this->_logging) $this->_log("Selected <strong>".DB_DATABASE."</strong> successfully.");
	}

	// Querying the Database.
	public function query($sql){
		mysql_query($sql, $this->_connection) or die("Could not select db: ".mysql_error());
		if($this->_logging) $this->_log("Queried <strong>".$sql."</strong> successfully.");
	}

	// Select a Table.
	public function selectTable($table, $parameters){
		if(!$parameters) $this->query("SELECT * FROM ".$table);
		else $this->query("SELECT * FROM ".$table." ".$parameters);
		if($this->_logging) $this->_log("Selected <strong>".$table."</strong> successfully.");
	}

}

?>