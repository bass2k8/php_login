<?php

require_once("inc/db.inc.php");

$db = new Database(true);

/* TESTING selectFrom() method */
$options=array(
	"WHERE" => array(
			array("user_name", "%bass%", "LIKE")
		),
	"ORDER BY" => array("user_name", "ASC")
	);
$db->selectTable("user", $options);

/* TESTING insertInto() method. */
$into = array(array("user_name", "bass2k8", PDO::PARAM_STR),
			  array("user_password", md5("ksdfmsdlkfmsdlkfm"), PDO::PARAM_STR));
$db->insertInto("user", $into);

$options=array(
	"WHERE" => array(
			array("user_name", "bass2k8")
		),
	"ORDER BY" => array("user_id", "ASC")
	);
$db->selectTable("user", $options);
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