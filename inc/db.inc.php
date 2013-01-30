<?php

require_once("constants.inc.php");
require_once("log.inc.php");

Class Database {

	private $_log; // Log object.
	private $_logging=false; // Define whether to output logging or not.

	private $_db; // PDO database object.
	private $_query; // SQL Query.
	
	private $_tableSelected; // Whether a table is selected or not.

	public function __construct($logging=false){
		// Initialising the variables.
		$this->_query=$this->_tableSelected=false;
		$this->_logging=$logging;

		// Create a log object, if logging is enabled.
		if($this->_logging) $this->_log = new Log(get_class($this));

		$this->_connect(); // Connect to the database.
	}

	// Close the connection to the Database Server.
	public function __destruct(){
		// If there already is a query, close it.
		if($this->_query) $this->_query->closeCursor();
		$this->_db=null;

		if($this->_logging) $this->_log->addToLog("Closed connection to <strong>".DB_SERVER."</strong> successfully.");
	}

	// Connecting to the Database Server.
	private function _connect(){
		// Create a PDO object.
		$this->_db = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_DATABASE, DB_USER, DB_PASS);
		$this->_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode.

		// If there aren't any PDO errors, return true.
		if(!$this->_PDOErrors()){
			if($this->_logging) $this->_log->addToLog("Connected to <strong>".DB_SERVER."</strong> successfully.");
			if($this->_logging) $this->_log->addToLog("Selected the database <strong>".DB_DATABASE."</strong> successfully.");
			return true;
		} else {
			if($this->_logging) $this->_log->addToLog("Could not connect to the Database server.");
			return false;
		}
		
	}
	
	// Check for any errors with PDO.
	private function _PDOErrors(){
		$error=$this->_db->errorInfo();

		// If there are PDO errors, return true;
		if($error[2]) return true;
		else return false;
	}

	// Querying the Database.
	public function query($sql="", $params=array()){
		$error_status=false;

		// If there already is a query, close it.
		if($this->_query) $this->_query->closeCursor();
		
		// Attempt to query the SQL.
		try {
			if(count($params, COUNT_RECURSIVE)>0){
				$this->_query=$this->_db->prepare($sql);
				foreach($params as $p){
					$this->_query->bindParam($p[0], $p[1]);
					if($this->_logging) $this->_log->addToLog("Param <strong>".$p[1].":</strong> was binded to <strong>".$p[0]."</strong>.");
				}
				$this->_query->execute();
			} else $this->_query = $this->_db->query($sql);
		} catch(PDOException $e){
			// If an error was caught, set error_status to true and return false.
			$error_status=true;
			if($this->_logging) $this->_log->addToLog("<strong>Error:</strong> ".$e->getMessage());
			return false;
		}

		// If there were no errors, return true.
		if(!$error_status){
			if($this->_logging) $this->_log->addToLog("Queried <strong>".$sql."</strong> successfully.");
			return true;
		}
	}

	// Return the number of rows affected by the last SQL statement.
	public function numberOfRows() {
		// If a query has been executed, continue.
		if($this->_query){
			// If table is selected, fetch the association.
			if($this->_tableSelected){
				$numberOfRows=$this->_query->rowCount();

				// If there aren't any PDO errors, return the association.
				if(!$this->_PDOErrors()){
					if($this->_logging) $this->_log->addToLog("Returned number of rows successfully.");
					return $numberOfRows;
				} else {
					if($this->_logging) $this->_log->addToLog("Cannot return number of rows; an error occured.");
					return false;
				}
			} else {
				if($this->_logging) $this->_log->addToLog("Cannot return number of rows; no table is selected.");
				return false;
			}
			
		} else {
			if($this->_logging) $this->_log->addToLog("Cannot return number of rows; there is no query.");
			return false;
		}
	}

	// Fetch association.
	public function fetchAssociation(){
		// If a query has been executed, continue.
		if($this->_query){
			// If table is selected, fetch the association.
			if($this->_tableSelected){
				$assoc = $this->_query->fetch(PDO::FETCH_ASSOC);

				// If there aren't any PDO errors, return the association.
				if(!$this->_PDOErrors()){
					if($this->_logging) $this->_log->addToLog("Fetched the association successfully.");
					return $assoc;
				} else {
					if($this->_logging) $this->_log->addToLog("Cannot fetch association; an error occured.");
					return false;
				}
			}
			else {
				if($this->_logging) $this->_log->addToLog("Cannot fetch association; no table is selected.");
				return false;
			}
			
		} else {
			if($this->_logging) $this->_log->addToLog("Cannot fetch association; there is no query.");
			return false;
		}
	}

	// Select a Table.
	public function selectTable($table="", $options_arr=array()){
		$options_sql="";
		$params=array();

		// If supplied options is an array.
		if(is_array($options_arr)){
			// If options array isn't empty.
			if(count($options_arr, COUNT_RECURSIVE)!=0){
				// If WHERE option is in array.
				if(array_key_exists('WHERE', $options_arr)){
					// If WHERE arguments are supplied.
					if(count($options_arr["WHERE"], COUNT_RECURSIVE)!=0){
						$options_sql.=" WHERE ";
						// Go through where_arr array.
						foreach($options_arr["WHERE"] as $where){
							if(isset($where[2])) $operator=" ".$where[2]." ";
							else $operator="=";
							array_push($params, array(":".$where[0], $where[1]));
							$options_sql .= "`".$where[0]."`".$operator.":".$where[0]." AND ";
						}
						$options_sql=substr($options_sql, 0, -5); // Remove "AND" and white space.
					}
				}

				// If ORDER BY option is in array.
				if(array_key_exists('ORDER BY', $options_arr)){
					// If ORDER BY arguments are supplied.
					if(count($options_arr["ORDER BY"], COUNT_RECURSIVE)!=0){
						$options_sql.=" ORDER BY `".$options_arr["ORDER BY"][0]."` ".$options_arr["ORDER BY"][1];
					}
				}

			}

			// SQL statement.
			$this->query("SELECT * FROM `$table`".$options_sql, $params);

			// If there aren't any PDO errors, return true.
			if(!$this->_PDOErrors()){
				if($this->_logging) $this->_log->addToLog("Selected <strong>".$table."</strong> successfully.");
				$this->_tableSelected=true;
				return true;
			} else {
				if($this->_logging) $this->_log->addToLog("Could not select <strong>".$table."</strong>.");
				return false;
			}
		} else {
			if($this->_logging) $this->_log->addToLog("An array was not supplied.");
			return false;
		}
	}

	// Insert a row into the specified table.
	public function insertInto($table="", $into_arr=array()){
		$this->_tableSelected=false; // To prevent future errors with fetching Association.
		$into_sql=$values_sql="";
		$params=array();

		// If variables are arrays, continue.
		if(is_array($into_arr)){
			// Go through into_arr array.
			foreach($into_arr as $ia){
				array_push($params, array(":".$ia[0], $ia[1]));
				$into_sql .= "`".$ia[0]."`, ";
				$values_sql .= ":".$ia[0].", ";
			}
			$into_sql=substr($into_sql, 0, -2); // Remove comma and white space.
			$values_sql=substr($values_sql, 0, -2); // Remove comma and white space.

			// SQL statement.
			$this->query("INSERT INTO `$table` ($into_sql) VALUES ($values_sql)", $params);

			// If there aren't any PDO errors, return true.
			if(!$this->_PDOErrors()){
				if($this->_logging) $this->_log->addToLog("Inserted into <strong>".$table."</strong> successfully.");
				return true;
			} else {
				if($this->_logging) $this->_log->addToLog("Could not insert into <strong>".$table."</strong>.");
				return false;
			}
		} else {
			if($this->_logging) $this->_log->addToLog("An array was not supplied.");
			return false;
		}
	}

	// Delete a row from the specified table.
	public function deleteFrom($table="", $where_arr=array()){
		$this->_tableSelected=false; // To prevent future errors with fetching Association.
		$where_sql="";

		// If variables are arrays, continue.
		if(is_array($where_arr)){
			// Go through where_arr array.
			foreach($where_arr as $wa){
				$where_sql .= "`".$wa[0]."`='".$wa[1]."' AND ";
			}
			$where_sql=substr($where_sql, 0, -5); // Remove "AND" and white space.

			// SQL statement.
			$this->query("DELETE FROM `$table` WHERE $where_sql");

			// If there aren't any PDO errors, return true.
			if(!$this->_PDOErrors()){
				if($this->_logging) $this->_log->addToLog("Deleted row from <strong>".$table."</strong> successfully.");
				return true;
			} else {
				if($this->_logging) $this->_log->addToLog("Could not delete from <strong>".$table."</strong>.");
				return false;
			}
		} else {
			if($this->_logging) $this->_log->addToLog("An array was not supplied.");
			return false;
		}
	}

	// Update a row in the specified table.
	public function updateTable($table="", $set_arr=array(), $where_arr=array()){
		$this->_tableSelected=false; // To prevent future errors with fetching Association.
		$set_sql=$where_sql="";

		// If variables are arrays, continue.
		if(is_array($set_arr) && is_array($where_arr)){
			// Go through set_arr array.
			foreach($set_arr as $sa){
				$set_sql .= "`".$sa[0]."`='".$sa[1]."', ";
			}
			$set_sql=substr($set_sql, 0, -2); // Remove comma and white space.

			// Go through where_arr array.
			foreach($where_arr as $wa){
				$where_sql .= "`".$wa[0]."`='".$wa[1]."' AND ";
			}
			$where_sql=substr($where_sql, 0, -5); // Remove "AND" and white space.

			// SQL statement.
			$this->query("UPDATE `$table` SET $set_sql WHERE $where_sql");

			// If there aren't any PDO errors, return true.
			if(!$this->_PDOErrors()){
				if($this->_logging) $this->_log->addToLog("Updated row in <strong>".$table."</strong> successfully.");
				return true;
			} else {
				if($this->_logging) $this->_log->addToLog("Could not update row in <strong>".$table."</strong>.");
				return false;
			}
		} else {
			if($this->_logging) $this->_log->addToLog("An array was not supplied.");
			return false;
		}
	}
}

?>