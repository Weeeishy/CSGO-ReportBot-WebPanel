<?php
session_start();
require 'language.php';
require 'db.php';
require 'config.php';

if(isset($_SESSION['auth'])){
	$uid = $_SESSION['auth']->id;
}else{
	$uid = NULL;
}

$token_custom = htmlentities($_POST['token_custom']);
if(isset($_POST['token'])){
	$token = htmlentities($_POST['token']);
}else{
	$token = '';
}

if($token_custom == ''){
	$token = htmlentities($_POST['token']);
}else{
	$token = htmlentities($_POST['token_custom']);
}

if(isset($_POST['g-recaptcha-response']) AND !empty($_POST['g-recaptcha-response'])){
	

	$verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret_key.'&response='.$_POST['g-recaptcha-response']);
	$responseData = json_decode($verifyResponse);
	if($responseData->success){
		// CAPTCHA IS OK, WE DONT DO ANYTHING
	}else{
		// CAPTCHA NOT OK
		$_SESSION['flash']['danger'] = "CAPTCHA ERROR";
		header("Location: ../whitelist.php");
		exit();
	}
}else{

	// NO CAPTCHA
	$_SESSION['flash']['danger'] = "NO CAPTCHA";
	header("Location: ../whitelist.php");
	exit();
}

if(is_null($token)){
	$_SESSION['flash']['danger'] = $language['error_no_token']; // no token
	header("Location: ../whitelist.php");
	exit();
	
}

if(empty($_POST['steamid'])){
	
	$_SESSION['flash']['danger'] = $language['error_no_steamid']; // no steamid 
	header("Location: ../whitelist.php");
	exit();
}

if(!is_numeric($_POST['steamid'])){
	
	$_SESSION['flash']['danger'] = $language['error_steamid_numberonly']; // steamid isnt only numbers
	header("Location: ../whitelist.php");
	exit();
}

if(strlen($_POST['steamid']) != 17){
	
	$_SESSION['flash']['danger'] = $language['error_steamid_invalid']; // invalid steamid
	header("Location: ../whitelist.php");
	exit();
}

// Check if token is valid
$req = $pdo->prepare('SELECT * FROM tokens WHERE token = ? AND token_type = "whitelist"');
$req->execute([$token]);
$check_token = $req->rowCount();

if($check_token == 0){
	$_SESSION['flash']['danger'] = $language['error_token_invalid'];
	header("Location: ../whitelist.php");
	exit();
}

// Check if token have use left
$req = $pdo->prepare('SELECT * FROM tokens WHERE (token = ? AND token_use > 0)');
$req->execute([$token]);
$check_token = $req->rowCount();

if($check_token == 0){
	$_SESSION['flash']['danger'] = $language['error_token_no_use_left'];
	header("Location: ../whitelist.php");
	exit();
}

if($check_token > 0){
	

	$steamid = htmlentities($_POST['steamid']);

	$req = $pdo->prepare("SELECT * FROM whitelist WHERE steamid = ?");
	$req->execute([$steamid]);
	if($req->rowCount() > 0){
		$_SESSION['flash']['danger'] = "$steamid already Whitelisted.";
		header("Location: ../whitelist.php");
		exit();
	}

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
	
	$req = $pdo->prepare('INSERT INTO tokens_activation SET token = ?, type = "whitelist", date = CURRENT_TIMESTAMP, uid = ?, uip = ?');
	$req->execute([$token, $uid, $ip]);

	// add values into db

	$req = $pdo->prepare('INSERT INTO whitelist SET steamid = ?, added_date = CURRENT_TIMESTAMP, comment = "Added with a token"');
	$req->execute(["$steamid"]);

	// whitelist done
	$_SESSION['flash']['success'] = "$steamid Whitelisted!";
	header("Location: ../whitelist.php");
	exit();



	
}