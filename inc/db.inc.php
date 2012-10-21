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

	private function _log($text){
		array_push($this->_log, $text);
	}

	public function outputLog(){
		echo "<ul id=\"database_log\">\n";
		foreach ($this->_log as $log) {
			echo "<li>".$log."</li>\n";
		}
		echo "</ul>\n";
	}

	// Connecting to the Database Server.
	private function _connect(){
		$this->_connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die("Could not connect to server: ".mysql_error());
		if($this->_logging) $this->_log("Connected to ".DB_SERVER." successfully.");
	}

	// Selecting the Database.
	private function _selectDatabase(){
		mysql_select_db($this->_dbName, $this->_connection) or die("Could not select db: ".mysql_error());
		if($this->_logging) $this->_log("Selected ".DB_DATABASE." successfully.");
	}

	// Querying the Database.
	public function query($sql){
		mysql_query($sql, $this->_connection) or die("Could not select db: ".mysql_error());
	}

	// Select a Table.
	public function selectTable($table, $parameters){
		if(!$parameters) $this->query("SELECT * FROM ".$table);
		else $this->query("SELECT * FROM ".$table.$parametes);
	}

}

?>