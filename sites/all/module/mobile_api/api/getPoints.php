<?php 

define('DRUPAL_ROOT',  str_replace("sites\all\modules\mobile_api\api", "", getcwd()));
require_once DRUPAL_ROOT . 'includes\bootstrap.inc';
require_once DRUPAL_ROOT . 'profiles\idea\modules\contrib\userpoints\userpoints.module';

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
	
	$totalPoints =  userpoints_get_current_points($uid);
	$query = "SELECT txn_id, uid, points, time_stamp, operation, description, tid";
    $query .= " FROM {userpoints_txn}";
    $query .= " WHERE status = 0 AND expired = 0";
	$query .= " AND uid = :uid";
	$result = db_query($query, array(':uid' => $uid))->fetchAll();
	
	foreach ($result as $value) {
		if ($value->time_stamp != null) {
			$value->created_at = date(DATE_ISO8601, $value->time_stamp);
		}
	}
	
	header('Content-Type: application/json');
	echo json_encode($result);
	exit;

} catch (Exception $e) {
	echo $e;
	header('HTTP/1.1 500 Internal Server Error');
	exit;
}

?>