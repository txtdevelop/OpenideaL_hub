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
	
	$query = "SELECT vv.*, n.*";
	$query .= " FROM {votingapi_vote} vv";
	$query .= " JOIN {node} n ON vv.entity_id = n.nid";
	$query .= " WHERE";
	$query .= " vv.entity_type = :entity_type";
	$query .= " AND vv.value_type = :value_type";
	$query .= " AND n.uid = :uid";
	$result = db_query($query, array(':entity_type' => 'node', ':value_type' => 'points', ':uid' => $uid))->fetchAll();
	
	foreach ($result as $value) {
		$value->idea = (object) array(
			"nid" => $value->nid,
			"title" => $value->title,
		);
		if ($value->created != null) {
			$value->created_at = date(DATE_ISO8601, $value->created);
		}
		if ($value->changed != null) {
			$value->changed_at = date(DATE_ISO8601, $value->changed);
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($result);
	exit;

} catch (Exception $e) {
	echo $e;
	header('HTTP/1.1 500 Internal Server Error');
	echo $e;
	exit;
}

?>