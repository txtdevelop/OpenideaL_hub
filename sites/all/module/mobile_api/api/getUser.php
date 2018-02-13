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
	
	$query = "SELECT u.uid, u.name, u.pass, u.mail,";
	$query .= " fdfua.field_user_address_country, fdfua.field_user_address_administrative_area, fdfua.field_user_address_locality, ";
	$query .= " fdfua.field_user_address_first_name as first_name, fdfua.field_user_address_first_name as last_name,";
	$query .= " fdfudb.field_user_date_birth_value as date_birth,";
	$query .= " fm.filename, fm.uri, fm.filemime";
	$query .= " FROM {users} u";
	$query .= " LEFT JOIN {field_data_field_user_address} fdfua ON fdfua.entity_id = u.uid AND fdfua.entity_type = 'user'";
	$query .= " LEFT JOIN {field_data_field_user_date_birth} fdfudb ON fdfudb.entity_id = u.uid AND fdfudb.entity_type = 'user'";
	$query .= " LEFT JOIN {file_managed} fm ON fm.fid = u.picture AND fdfudb.entity_type = 'user'";
	$query .= " WHERE u.uid = :uid and u.status = 1";

	$user = db_query($query, array(':uid' => $uid))->fetchObject();
	
	$query = "SELECT count(nid) as n_ideas";
    $query .= " FROM {node}";
    $query .= " WHERE ";
	$query .= " status = 1";
	$query .= " AND uid = :uid";
	$query .= " AND type = :type";
	
	$ideas = db_query($query, array(':uid' => $uid, ':type' => 'idea'))->fetchObject();
	
	$totalPoints =  userpoints_get_current_points($uid);

	if ($user) {
		$user->pass = null;
		$user->ideas_number = (int) $ideas->n_ideas;
		$user->points_number = (int) $totalPoints;
		header('Content-Type: application/json');
		echo json_encode($user);
		return;
	} else {
		throw new Exception('Result is null');
	}
	
} catch (Exception $e) {
	echo $e;
	header('HTTP/1.1 500 Internal Server Error');
	return;
}

?>