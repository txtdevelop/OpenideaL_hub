<?php 

define('DRUPAL_ROOT',  str_replace("sites\all\modules\mobile_api\api", "", getcwd()));
require_once DRUPAL_ROOT . 'includes\bootstrap.inc';

drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

$uid = null;
if (isset($_GET["uid"])) {
	$uid = htmlspecialchars($_GET["uid"]);
}
if ($uid == null) {
	echo "uid is null";
	header('HTTP/1.1 500 Internal Server Error');
	return;
}

try {
	
	$query = "SELECT n.*, u.name";
    $query .= " FROM {node} n";
	$query .= " LEFT JOIN {users} u ON u.uid = n.uid";
    $query .= " WHERE ";
	$query .= " n.status = 1";
	$query .= " AND n.type = :type";
	$query .= " ORDER BY n.created DESC";
	$result = db_query($query, array(':type' => 'idea'))->fetchAll();
	
	
	header('Content-Type: application/json');
	echo json_encode($result);
	exit;

} catch (Exception $e) {
	echo $e;
	header('HTTP/1.1 500 Internal Server Error');
	exit;
}

?>