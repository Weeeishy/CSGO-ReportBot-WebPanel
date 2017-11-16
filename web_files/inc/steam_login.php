<?php
require 'steamauth/steamauth.php';
require 'config.php';
require 'db.php';
require 'language.php';

if(isset($_SESSION['auth'])){
	// user already logged in
	require_once 'steamauth/userInfo.php';
	$steamid = $steamprofile['steamid'];

	// first, we check if there is already a user witht his steamid
	$req = $pdo->prepare('SELECT * FROM users WHERE steamid = ?');
	$req->execute([$steamid]);

	if($req->rowCount() == 1){
		// there is already an user with this steamid
		$_SESSION['flash']['danger'] = "An user with this SteamID already exist.";
		header("Location: ../account.php");
		exit();
	}
	
	$req = $pdo->prepare('UPDATE users SET steamid = :steamid WHERE id = :id');
	$req->bindParam(':steamid', $steamid);
	$req->bindParam(':id', $_SESSION['auth']->id);

	$req->execute();

	$id = $_SESSION['auth']->id;
	unset($_SESSION['auth']);

	$req = $pdo->prepare('SELECT * FROM users WHERE id = ?');
	$req->execute([$id]);

	$_SESSION['auth'] = $req->fetch();
	if(isset($_SESSION['auth']->steamid)){
		$req = $pdo->query('SELECT * FROM rewards_users WHERE uid = '.$_SESSION['auth']->id);
		if($req->rowCount() == 0 ){
			$pdo->query("INSERT INTO rewards_users SET uid = '".$_SESSION['auth']->id."', tag_datebegin = CURDATE()");
		}
	}

	$_SESSION['flash']['success'] = "Successfully linked your account to steam";
	header("Location: ../account.php");
	exit();

}

if(isset($_SESSION['steamid'])){
	// user logged in with steam
	require_once 'steamauth/userInfo.php';

	$steamid = $steamprofile['steamid'];

	// we check if there is an user with this steamID in database
	$req = $pdo->prepare('SELECT * FROM users WHERE steamid = ?');
	$req->execute([$steamid]);

	if($req->rowCount() == 0){
		// there is no account with this steamID
		// we need to redirect to registration form
		header("Location: ../register.php?steamid=$steamid");
		exit();
	}else{
		// There is an account, we'll get the info and login the user
		$req = $pdo->prepare('SELECT * FROM users WHERE steamid = ?');
		$req->execute([$steamid]);
		$_SESSION['auth'] = $req->fetch();

		if(isset($_SESSION['auth']->steamid)){
			$req = $pdo->query('SELECT * FROM rewards_users WHERE uid = '.$_SESSION['auth']->id);
			if($req->rowCount() == 0 ){
				$pdo->query("INSERT INTO rewards_users SET uid = '".$_SESSION['auth']->id."', tag_datebegin = CURDATE()");
			}
		}

		$_SESSION['flash']['success'] = $language['logged_in'];
		header("Location: ../account.php");
		exit();
	}

}
