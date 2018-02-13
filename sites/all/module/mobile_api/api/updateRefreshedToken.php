<?php 

define('DRUPAL_ROOT',  str_replace("sites\all\modules\mobile_api\api", "", getcwd()));
require_once DRUPAL_ROOT . 'includes\bootstrap.inc';

drupal_bootstrap(DRUPAL_BOOTSTRAP_DATABASE);

try {
	$body = json_decode($HTTP_RAW_POST_DATA);
	$uid = $body->uid;
	$refreshedToken = $body->refreshedToken;
	if ($uid == null || $refreshedToken == null) {
		header('HTTP/1.1 500 Internal Server Error');
		return;
	}
	
	$num_updated = db_update('users')
	  ->fields(array(
		'signature' => $refreshedToken,
	  ))
	  ->condition('uid', $uid, '=')
	  ->execute();

	if ($num_updated != null && $num_updated > 0) {
		header('Content-Type: application/json');
		echo json_encode(true);
		return;
	} else {
		header('Content-Type: application/json');
		echo json_encode(false);
		return;
	}
	
} catch (Exception $e) {
	echo $e;
	header('HTTP/1.1 500 Internal Server Error');
	return;
}

?>