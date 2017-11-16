<?php
session_start();
require 'language.php';
require 'db.php';
require 'config.php';

$steamid = htmlentities($_POST['steamid']);


if(isset($_SESSION['auth'])){
	$uid = $_SESSION['auth']->id;
}else{
	$uid = NULL;
}

if(isset($_SESSION['report'])){
	unset($_SESSION['report']);
}

if(isset($_POST['matchid'])){
	if(!empty($_POST['matchid'])){
		$matchid = htmlentities($_POST['matchid']);
	}else{
		$matchid = "8";
	}
}else{
	$matchid = "8";
}
if(!is_numeric($matchid)){
	// invalid matchid
	$_SESSION['flash']['danger'] = "Invalid MatchID. It can only contains numbers.";
	header("Location: ../report.php");
	exit();
}

$req = $pdo->query("SELECT * FROM rewards_config");
$ptsper_report = $req->fetch()->pointsper_report;

$token_custom = $_POST['token_custom'];
if(isset($_POST['token'])){
	$token = htmlentities($_POST['token']);
}else{
	$token = '';
}


if(isset($_POST['g-recaptcha-response']) AND !empty($_POST['g-recaptcha-response'])){
	

	$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']);
	$responseData = json_decode($verifyResponse);
	if($responseData->success){
		// CAPTCHA IS OK, WE DONT DO ANYTHING
	}else{
		// CAPTCHA NOT OK
		$_SESSION['flash']['danger'] = $language['error_invalid_captcha'];
		header("Location: ../report.php");
		exit();
	}
}else{

	// NO CAPTCHA
	$_SESSION['flash']['danger'] = $language['error_no_captcha'];
	header("Location: ../report.php");
	exit();
}


if(isset($_POST['steamid'])){
	$req = $pdo->prepare('SELECT * FROM whitelist WHERE steamid = ?');
	$req->execute([$_POST['steamid']]);
	if($req->rowCount() == 1){
		// whitelisted
		$_SESSION['flash']['danger'] = $language['error_steamid_whitelisted'];
		header("Location: ../report.php");
		exit();
	}
}

if($token_custom == ''){
	$token = htmlentities($_POST['token']);
}else{
	$token = htmlentities($_POST['token_custom']);
}

if(is_null($token)){
	$_SESSION['flash']['danger'] = $language['error_no_token'];
	header("Location: ../report.php");
	exit();
	
}

if(empty($_POST['steamid'])){
	
	$_SESSION['flash']['danger'] = $language['error_no_steamid'];
	header("Location: ../report.php");
	exit();
}



if(!is_numeric($steamid)){
	
	$_SESSION['flash']['danger'] = $language['error_steamid_numberonly']; // steamid isnt only numbers
	header("Location: ../commend.php");
	exit();
}

// checking if steamid is valid

$link = 'http://steamcommunity.com/profiles/'.$steamid.'?xml=1';
$xml = simplexml_load_file(rawurlencode($link));
if(isset($xml->{'error'})){
	$_SESSION['flash']['danger'] = $language['error_steamid_invalid'];
	header("Location: ../report.php");
	exit();
}

if(strlen($_POST['steamid']) != 17){
	
	$_SESSION['flash']['danger'] = $language['error_steamid_invalid'];
	header("Location: ../report.php");
	exit();
}

// Check if token is valid
$req = $pdo->prepare('SELECT * FROM tokens WHERE token = ? AND token_type = "report"');
$req->execute([$token]);
$check_token = $req->rowCount();

if($check_token == 0){
	$_SESSION['flash']['danger'] = $language['error_token_invalid'];
	header("Location: ../report.php");
	exit();
}

// Check if token have use left
$req = $pdo->prepare('SELECT * FROM tokens WHERE (token = ? AND token_use > 0)');
$req->execute([$token]);
$check_token = $req->rowCount();

if($check_token == 0){
	$_SESSION['flash']['danger'] = $language['error_token_no_use_left'];
	header("Location: ../report.php");
	exit();
}

if($check_token > 0){
	
	// WE CAN START THE REPORT CODE


	$link = 'http://steamcommunity.com/profiles/'.$steamid.'?xml=1';
	$xml = simplexml_load_file(rawurlencode($link));
	$name = preg_replace("/[^a-zA-Z0-9\s]/", "", $xml->{'steamID'});

	
	if(ctype_space($name)){
		$name = 'Unknown Name'; // If name is empty
	}

	if($name == ''){
		$name = 'Unknown Name'; // If name is empty
	}

	if(is_null($name)){
		$name = 'Unknown Name'; // if we can't get the name
	}

	// NOW WE NEED TO CHECK IF ALL REPORT BOTS ARE OK
	for($i = 1; $i <= $reportbot_number; $i++){
		$req = $pdo->prepare('SELECT * FROM reported_list WHERE (reportbot_id = ? AND NOW() < datum + interval ? hour)');
		$req->execute([$i, $report_timer]);
		${'reportbot' . $i} = $req->rowCount();
		

		if(${'reportbot'.$i} == '0'){
			echo 'reportbot to use: ' .$i; // debug stuff
			$ready_report = '1';
			$reportbot_id = $i; 
			break;
		}else{
			if($i == $reportbot_number){
				$_SESSION['flash']['danger'] = $language['error_no_reportbot_available'];
				header("Location: ../report.php");
				exit();
			}
		}
	} // end for

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
		
		$req = $pdo->prepare('INSERT INTO tokens_activation SET token = ?, type = "report", date = CURRENT_TIMESTAMP, uid = ?, uip = ?');
		$req->execute([$token, $uid, $ip]);

		// We get user inventory value
		$json = file_get_contents("http://csgobackpack.net/api/GetInventoryValue/?id=".$steamid);
		$data = json_decode($json);

		if(isset($data->{'value'})){
			$inventory_value = $data->{'value'};
			$currency = "$";
		}else{
			$inventory_value = '0';
			$currency = "$";
		}

		if(is_null($inventory_value)){
			$inventory_value = '0';
			$currency = "$";
		}

		if($inventory_value == '0'){
			$inventory = 'Private';
		}else{
			$inventory = $inventory_value . ' ' . $currency;
		}

		// add values into db

		$req = $pdo->prepare('INSERT INTO reported_list SET reportbot_id = ?, datum = CURRENT_TIMESTAMP, steamid = ?, reportedby_userid = ?, inventory_value = ?');
		$req->execute([$reportbot_id, $steamid, $uid, $inventory]);

		// report steamid

		exec("cd $report_path && node report.js $steamid $reportbot_id \"$log_prefix\" $matchid > $report_log_path$steamid.txt &");

		if(isset($_SESSION['auth']->steamid)){
			$sql = $pdo->prepare('UPDATE rewards_users SET points = points + ? WHERE uid = ?');
			$sql->execute([$ptsper_report, $uid]);
		}

		// report done
		$_SESSION['flash']['success'] = $language['report_successfully_done'];
		header("Location: ../report.php?l=$steamid");
		exit();

	}else{
		$_SESSION['flash']['danger'] = $language['error_no_reportbot_available'];
		header("Location: ../report.php");
		exit();
	}

	
}