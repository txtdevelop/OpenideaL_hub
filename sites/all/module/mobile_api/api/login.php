<?php 

define('DRUPAL_ROOT',  str_replace("sites\all\modules\mobile_api\api", "", getcwd()));
require_once DRUPAL_ROOT . 'includes\bootstrap.inc';
require_once DRUPAL_ROOT . 'includes\password.inc';

drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

try {
	$body = json_decode($HTTP_RAW_POST_DATA);
	$username = $body->username;
	$password = $body->password;
	if ($username == null || $password == null) {
		header('HTTP/1.1 500 Internal Server Error');
		return;
	}
	
	$query = "SELECT u.uid, u.name, u.pass, u.mail,";
	$query .= " fdfua.field_user_address_country, fdfua.field_user_address_administrative_area, fdfua.field_user_address_locality, ";
	$query .= " fdfua.field_user_address_first_name as first_name, fdfua.field_user_address_first_name as last_name,";
	$query .= " fdfudb.field_user_date_birth_value as date_birth,";
	$query .= " fm.filename, fm.uri, fm.filemime";
	$query .= " FROM {users} u";
	$query .= " LEFT JOIN {field_data_field_user_address} fdfua ON fdfua.entity_id = u.uid AND fdfua.entity_type = 'user'";
	$query .= " LEFT JOIN {field_data_field_user_date_birth} fdfudb ON fdfudb.entity_id = u.uid AND fdfudb.entity_type = 'user'";
	$query .= " LEFT JOIN {file_managed} fm ON fm.fid = u.picture AND fdfudb.entity_type = 'user'";
	$query .= " WHERE (u.name = :name OR u.mail = :mail) and u.status = 1";

	$user = db_query($query, array(':name' => $username, ':mail' => $username))->fetchObject();
	$result = user_check_password($password, $user);

	if ($result) {
		$user->pass = null;
		header('Content-Type: application/json');
		echo json_encode($user);
		return;
	} else {
		header('HTTP/1.1 500 Internal Server Error');
		return;
	}
	
} catch (Exception $e) {
	echo $e;
	header('HTTP/1.1 500 Internal Server Error');
	return;
}

?>