<?php
session_start();
require 'language.php';
require 'db.php';
require 'config.php';

$req = $pdo->query("SELECT * FROM rewards_config");
$ptsper_commend = $req->fetch()->pointsper_commend;

if(isset($_POST['commendbot_selected'])){
	$commendbot_selected = $_POST['commendbot_selected'];
	$commendbot_selected = str_replace("commendbot_","", $commendbot_selected);
}

if(isset($_SESSION['auth'])){
	$uid = $_SESSION['auth']->id;
}else{
	$uid = NULL;
}

$steamid = htmlentities($_POST['steamid']);
if(isset($_POST['token'])){
	$token = htmlentities($_POST['token']);
}


if(isset($_POST['token_custom']) AND strlen($_POST['token_custom']) <= 0){
	unset($_POST['token_custom']);

}

if(isset($_POST['token_custom'])){
	if(!strlen($_POST['token_custom']) <= 0){
		$token = htmlentities($_POST['token_custom']);
	}
}

if(isset($_POST['g-recaptcha-response']) AND !empty($_POST['g-recaptcha-response'])){
	

	$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']);
	$responseData = json_decode($verifyResponse);
	if($responseData->success){
		// CAPTCHA IS OK, WE DONT DO ANYTHING
	}else{
		// CAPTCHA NOT OK

		header("Location: ../commend.php");
		exit();
	}
}else{

	// NO CAPTCHA
	header("Location: ../commend.php");
	exit();
}

if(is_null($token)){
	$_SESSION['flash']['danger'] = $language['error_no_token']; // no token
	header("Location: ../commend.php");
	exit();
	
}

if(empty($_POST['steamid'])){
	
	$_SESSION['flash']['danger'] = $language['error_no_steamid']; // no steamid 
	header("Location: ../commend.php");
	exit();
}

if(!is_numeric($_POST['steamid'])){
	
	$_SESSION['flash']['danger'] = $language['error_steamid_numberonly']; // steamid isnt only numbers
	header("Location: ../commend.php");
	exit();
}

// checking if steamid is valid
$steamid_link = "http://steamcommunity.com/profiles/" + $steamid + "/?xml=1";
$xml = simplexml_load_file(rawurlencode($steam_link));
var_dump($xml);
die();


if(strlen($_POST['steamid']) != 17){
	
	$_SESSION['flash']['danger'] = $language['error_steamid_invalid']; // invalid steamid
	header("Location: ../commend.php");
	exit();
}

// Check if token is valid
$req = $pdo->prepare('SELECT * FROM tokens WHERE token = ? AND token_type = "commend"');
$req->execute([$token]);
$check_token = $req->rowCount();

if($check_token == 0){
	$_SESSION['flash']['danger'] = $language['error_token_invalid'];
	header("Location: ../commend.php");
	exit();
}

// Check if token have use left
$req = $pdo->prepare('SELECT * FROM tokens WHERE (token = ? AND token_use > 0)');
$req->execute([$token]);
$check_token = $req->rowCount();

if($check_token == 0){
	$_SESSION['flash']['danger'] = $language['error_token_no_use_left'];
	header("Location: ../commend.php");
	exit();
}

if($check_token > 0){
	
	// WE CAN START THE REPORT CODE

	$steamid = $_POST['steamid'];

	
	// If you want to get the Steam Name from the reported user
	$steam_link = 'http://steamcommunity.com/profiles/'.$steamid.'?xml=1';
	$xml = simplexml_load_file(rawurlencode($steam_link));
	$name = preg_replace("/[^a-zA-Z0-9\s]/", "", $xml->{'steamID'});


	
	if(ctype_space($name)){
		$name = 'Unknown Name'; // If name is empty
	}

	if($name == ''){
		$name = 'Unknown Name'; // If name is empty
	}

	if(is_null($name)){
		$name = 'Unknown Name'; // If we can't get the name
	}

	if(isset($commendbot_selected)){
		$req = $pdo->prepare('SELECT * FROM commended_list WHERE (commendbot_id = ? AND steamid = ?)');
		$req->execute([$commendbot_selected, $steamid]);

		if($req->rowCount() == 0){
			$r = $pdo->prepare('SELECT * FROM commended_list WHERE (commendbot_id = ? AND NOW() < datum + interval ? hour)');
			$r->execute([$commendbot_selected, $commend_timer]);
			if($r->rowCount() == 0){
				$ready_report = '1';
				$commendbot_id = $commendbot_selected;
			}
		}

	}else{

		// NOW WE NEED TO CHECK IF ALL REPORT BOTS ARE OK
		for($i = 1; $i <= $commendbot_number; $i++){
			$req = $pdo->prepare('SELECT * FROM commended_list WHERE (commendbot_id = ? AND steamid = ?)');
			$req->execute([$i, $steamid]);
			${'reportbot' . $i} = $req->rowCount();
			

			if(${'reportbot'.$i} == '0'){
				$r = $pdo->prepare('SELECT * FROM commended_list WHERE (commendbot_id = ? AND NOW() < datum + interval ? hour)');
				$r->execute([$i, $commend_timer]);
				if($r->rowCount() == 0){
					echo 'reportbot to use: ' .$i;
					$ready_report = '1';
					$commendbot_id = $i; 
					break;
				}
				
			}else{
				if($i == $commendbot_number){
					$_SESSION['flash']['danger'] = $language['error_no_commendbot_available'];
					header("Location: ../commend.php");
					exit();
				}
			}
		} // end for
	}

	

	if($ready_report == '1'){
		// TOKEN -1
		$req = $pdo->prepare('UPDATE tokens SET token_use = token_use - 1 WHERE token = ?');
		$req->execute([$token]);

		// we log the token activation
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
		
		$ip = htmlentities($ip);
		
		$req = $pdo->prepare('INSERT INTO tokens_activation SET token = ?, type = "commend", date = CURRENT_TIMESTAMP, uid = ?, uip = ?');
		$req->execute([$token, $uid, $ip]);

		// add values into db

		$req = $pdo->prepare('INSERT INTO commended_list SET commendbot_id = ?, datum = CURRENT_TIMESTAMP, steamid = ?, commendedby_token = ?, commendedby_userid = ?');
		$req->execute(["$commendbot_id", "$steamid", "$token", "$uid"]);

		// report steamid
		$commend = "cd $commend_path && node commend.js $steamid $commendbot_id \"$log_prefix\" > $commend_log_path$steamid.txt &";
		//echo $commend;
		exec("cd $commend_path && node commend.js $steamid $commendbot_id \"$log_prefix\" > $commend_log_path$steamid.txt &");

		if(isset($_SESSION['auth']->steamid)){
			$req = $pdo->prepare('SELECT * FROM rewards_users WHERE uid = ?');
			$req->execute([$uid]);
			if($req->rowCount() == 1){
				$sql = $pdo->prepare('UPDATE rewards_users SET points = points + ? WHERE uid = ?');
				$sql->execute([$ptsper_commend, $uid]);
			}
		}

		// report done
		$_SESSION['flash']['success'] = $language['commend_successfully_done'];
		header("Location: ../commend.php?l=$steamid");
		exit();

	}else{
		$_SESSION['flash']['danger'] = $language['error_no_commendbot_available'];
		header("Location: ../commend.php");
		exit();
	}

	
}