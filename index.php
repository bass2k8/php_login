<?php

require_once("inc/db.inc.php");

$db = new Database("php_login", true);

while($row = $db->fetchAssociation()) {
	print_r($row);
}

echo "Hello World";

?>