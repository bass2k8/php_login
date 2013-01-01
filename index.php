<?php

require_once("inc/db.inc.php");

$db = new Database("php_login", true);
$db->selectTable("personal");

//$into = array("user_id", "con_email_address");
//$values = array("1", "bassatcollege@gmail.com");
//$db->insertInto("contact", $into, $values);

while($row = $db->fetchAssociation()) {
	print_r($row);
}

echo "Hello World";

?>