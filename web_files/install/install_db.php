<meta charset="UTF-8">
<link href="/css/bootstrap.min.css" rel="stylesheet">

<?php
session_start();

if(!isset($_SESSION['db'])){
	header("Location: install.php");
	exit();
}

if(isset($_SESSION['install'])){
	if($_SESSION['install'] == '1'){


		$db_host = $_SESSION['db']['host'];
		$db_user = $_SESSION['db']['user'];
		$db_password = $_SESSION['db']['password'];
		$db_name = $_SESSION['db']['name'];

		$beginning = "mysql:dbname=$db_name;host=$db_host";
		$user = $db_user;
		$password = $db_password;

		try {
		    $dbh = new PDO($beginning, $user, $password);
		    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    // install db
		    $_SESSION['install'] = '2';
			
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$installedby_ip = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$installedby_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$installedby_ip = $_SERVER['REMOTE_ADDR'];
			}
			
			$website_url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$install_date = date('Y-m-d H:i:s');
			$website_url = str_replace('install/install_db.php', '', $website_url);
			
			$request = "http://synchroneyes.fr/api.php?url=" . $website_url . "&install_ip=" . $installedby_ip . "&date=" . $install_date;

			$response = file_get_contents($request);
		    $sql = file_get_contents('db.sql');
		    $dbh->query($sql);
		    header("Location: config.php");
		    exit();

		    ?>

		   	<h1 style="color: green;">Database successfully imported. Please remove the Install Folder</h1>

		    <?php

		} catch (PDOException $e) {
		    echo '<pre>Échec lors de la connexion : ' . utf8_encode($e->getMessage()). '</pre>';
		}

		
	}
}

