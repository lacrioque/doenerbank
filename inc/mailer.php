<?php
    include('Mail.php');
function doenermail($empfaenger, $betreff, $message){
	$headers['From']= 'doenermann@linmaonline.de'; 
	$headers['To']= $empfaenger; 
	$headers['Subject'] = $betreff;
	$body = $message;

	$params['host'] = 'localhost';
	$params['port'] = '25';
	$params['auth'] = 'PLAIN';
	$params['username'] = 'doenermann'; //CHANGE
	$params['password'] = 'doenermann_info'; //CHANGE
	$mail = Mail::factory('smtp', $params);
	$mail->send($empfaenger, $headers, $body);
}

?>