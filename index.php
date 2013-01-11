<?php

require_once("inc/db.inc.php");

$db = new Database("php_login", true);

/* TESTING insertInto() method. */
$into = array(array("user_name", "bass2k8"),
			  array("user_password", md5("ksdfmsdlkfmsdlkfm")));
$db->insertInto("user", $into);

$where = array(array("user_name", "bass2k8"));
$order = array("user_id", "ASC");
$db->selectTable("user", $where, $order);
while($row = $db->fetchAssociation()) {
	print_r($row);
}
echo "<br />Number of Rows: ".$db->numberOfRows()."<br />";

/* TESTING updateTable() method. */
$set = array(array("user_password", md5("asdmkasdmlkdm")));
$where = array(array("user_name", "bass2k8"));
$db->updateTable("user", $set, $where);

$db->selectTable("user");
while($row = $db->fetchAssociation()) {
	print_r($row);
}

/* TESTING deleteFrom() method. */
$where = array(array("user_name", "bass2k8"));
$db->deleteFrom("user", $where);

$db->selectTable("user");
while($row = $db->fetchAssociation()) {
	print_r($row);
}

echo "Hello World";

?>