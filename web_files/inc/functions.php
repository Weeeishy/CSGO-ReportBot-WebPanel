<?php
function debug($variable){

	echo '<pre>' . print_r($variable, true) . '</pre>';

}


function is_logged(){
	if(session_status() != PHP_SESSION_ACTIVE ){
		session_start();
	}
	if(!isset($_SESSION['auth'])){
		$_SESSION['flash']['danger'] = "You must be logged to see this page";
		header("Location: login.php");
		exit();
	}
	
	if(empty($_SESSION['auth'])){
		unset($_SESSION['auth']);
		header("Location: login.php");
		exit();
	}

}

function logged_check(){
	
	if(session_status() != PHP_SESSION_ACTIVE ){
		session_start();
	}
	if(isset($_SESSION['auth'])){
		$_SESSION['flash']['info'] = "You are already logged in";
		header("Location: account.php");
		exit();
	}
}

function is_admin(){
	
	if(session_status() != PHP_SESSION_ACTIVE ){
		session_start();
	}
	
	if(isset($_SESSION['auth']) AND $_SESSION['auth']->is_admin == 1){
		
	}else{
		$_SESSION['flash']['danger'] = "You are not allowed to enter the admin area.";
		header("Location: ../index.php");
		exit();
	}

}

function CheckUserInSteamGroup($steamurl, $steamid)
{
	$groupData = simplexml_load_file($steamurl,'SimpleXMLElement',LIBXML_NOWARNING);
	if (!$groupData) { die('steamerror'); }
	$groupMembers = $groupData->members;
	$found = false;
	foreach($groupMembers->steamID64 as $member)
	{
		if ($member == $steamid) { $found = true; break; }
	}
	if ($found == true) { return true; }
	if($groupData->nextPageLink)
	{	
		$url = (string)$groupData->nextPageLink;
		$found = CheckUserInSteamGroup($url, $steamid, $ran);
	}
	return $found;
}

function getLastVersion(){
	$url = "http://api.cs-report.me/version.php";
	$file = file_get_contents($url);
	$api = json_decode($file);

	$version = $api->{'version'};
	return $version;

}


function getLastDate(){
	$url = "http://api.cs-report.me/version.php";
	$file = file_get_contents($url);
	$api = json_decode($file);

	$date = $api->{'date'};
	return $date;

}


function showVersionAlert($version, $date){
	if(isset($_SESSION['auth']) AND $_SESSION['auth']->is_admin == 1){
		$_SESSION['flash_admin']['warning'] = "<a style='color: white;' href='https://cs-report.me/auth/updates.php' target='_blank'>[UPDATE] $version is available. Release date: $date.</a>";
	}
}
