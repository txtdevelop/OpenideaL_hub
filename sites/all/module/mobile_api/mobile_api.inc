<?php 

function sendNotification($to, $title, $body, $toUser) {

	try {
		
		if ($to == null) {
			$to = '/topics/All';
		}
		if ($title == null) {
			$title = 'New Notification';
		}
		if ($body == null) {
			$body = 'There is a new notification for you';
		}

		define( 'API_ACCESS_KEY', 'AAAAD1PTRPY:APA91bGa1AUE95uF19h3eqf2uern6IeeNGWF2qshcxK18kERl1R3xbMxFIQBKVKSx1zcTzRGiCFuhJFIXWBJJht_svmut2Woevxw4VZ3HAdKioWOBri9rVl7ZVwyM7Vy2HI1XRUafZ9r' );

		$msg = array
				(
					'title'	=> $title,
					'body' 	=> $body,
				);

		$fields = array
				(
					'to'		=> $to,
					'notification'	=> $msg,
					'data'          => array('' => ''),
					'priority' => 'high'
				);
				
		$headers = array
				(
					'Authorization: key=' . API_ACCESS_KEY,
					'Content-Type: application/json'
				);
				
		print_r(json_encode( $fields ));echo"\n";

		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch);
		print_r( curl_getinfo($ch));
		curl_close( $ch );

		echo $result;
		
	} catch (Exception $e) {
		echo $e;
		header('HTTP/1.1 500 Internal Server Error');
		exit;
	}
}