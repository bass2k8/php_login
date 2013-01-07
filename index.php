<?php

require_once("inc/db.inc.php");

$db = new Database("php_login", true);

$into = array("user_name", "user_password");
$values = array("bass2k8", md5("password"));
$db->insertInto("user", $into, $values);

$db->selectTable("user");
while($row = $db->fetchAssociation()) {
	print_r($row);
}

$where = array(array("user_name", "bass2k8"));
$db->deleteFrom("user", $where);

$db->selectTable("user");
while($row = $db->fetchAssociation()) {
	print_r($row);
}

$set = array(array("user_password", md5("asdmkasdmlkdm")));
$where = array(array("user_name", "bass2k8"));
$db->updateTable("user", $set, $where);

$db->selectTable("user");
while($row = $db->fetchAssociation()) {
	print_r($row);
}

echo "Hello World";

?>