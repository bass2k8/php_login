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
		// Initialising the variables.
		$this->_dbName = $dbName;
		$this->_query=false;
		$this->_logging=$logging;

		// Create a log object, if logging is enabled.
		if($this->_logging) $this->_log = new Log(get_class($this));

		$this->_connect(); // Connect to the database.
	}

	// Close the connection to the Database Server.
	public function __destruct(){
		if($this->_query) $this->_query->closeCursor();
		$this->_db=null;
		if($this->_logging) $this->_log->addToLog("Closed connection to <strong>".DB_SERVER."</strong> successfully.");
	}

	// Connecting to the Database Server.
	private function _connect(){
		// Create a PDO object.
		$this->_db = new PDO("mysql:host=".DB_SERVER.";dbname=".$this->_dbName, DB_USER, DB_PASS);
		$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode.
		if(!$this->_PDOErrors()){
			if($this->_logging) $this->_log->addToLog("Connected to <strong>".DB_SERVER."</strong> successfully.");
			if($this->_logging) $this->_log->addToLog("Selected the database <strong>".$this->_dbName."</strong> successfully.");
			return true;
		} else {
			if($this->_logging) $this->_log->addToLog("Could not connect to the Database server.");
			return false;
		}
		
	}
	
	// Check for any errors with PDO.
	private function _PDOErrors(){
		$error=$this->_db->errorInfo();
		if(!$error[2]) return false;
		else return true;
	}

	// Querying the Database.
	public function query($sql){
		$error_status=false;

		// If there already is a query, close it.
		if($this->_query) $this->_query->closeCursor();
		
		try {
			// Attempt to query the SQL.
			$this->_query = $this->_db->query($sql);
		} catch(PDOException $e){
			// If an error was caught, add error to log.
			$error_status=true;
			if($this->_logging) $this->_log->addToLog("<strong>Error:</strong> ".$e->getMessage());
			return false;
		}

		if(!$error_status){
			// If there were no errors.
			if($this->_logging) $this->_log->addToLog("Queried <strong>".$sql."</strong> successfully.");
			return true;
		}
	}

	// Fetch association.
	public function fetchAssociation(){
		if($this->_query){
			if($this->_logging) $this->_log->addToLog("Fetched the association successfully.");
			return $this->_query->fetch(PDO::FETCH_ASSOC);
		} else {
			if($this->_logging) $this->_log->addToLog("Cannot fetch association; there is no query.");
			return false;
		}
	}

	// Select a Table.
	protected function selectTable($table){
		$this->query("SELECT * FROM `$table`");
		if(!$this->_PDOErrors()){
			if($this->_logging) $this->_log->addToLog("Selected <strong>".$table."</strong> successfully.");
			return true;
		} else {
			if($this->_logging) $this->_log->addToLog("Could not select <strong>".$table."</strong>.");
			return false;
		}
	}

}

?>