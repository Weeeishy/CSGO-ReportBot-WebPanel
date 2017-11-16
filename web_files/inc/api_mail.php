<?php

$ip = $_SERVER['REMOTE_ADDR'];

if($ip == gethostbyname("cs-report.me")){
	include 'config.php';
	include 'db.php';

	// sql 
	// ALTER TABLE `users` ADD `email` VARCHAR(255) NULL AFTER `steamid`, ADD `email_subscribed` BOOLEAN NOT NULL AFTER `email`;

	$req = $pdo->query("SELECT * FROM users WHERE (email IS NOT NULL AND email_subscribed = 1)");
	$count = $req->rowCount();
	//echo "Count: $count <br/>";
	$emails = array();
	while($row = $req->fetch()){
		array_push($emails, $row->email);
	}


	$mails = "";
	$count = count($emails);
	foreach ($emails as $key => $value) {
		if($count == 1){
			$mails = $value;
			break;
		}
		if($key == 0){
			$mails = $value . ";";
		}elseif($key < $count -1){
			$mails .= $value . ";";
		}else{
			$mails .= $value;
		}
	}


	echo $mails;
}else{
	echo "no";
}