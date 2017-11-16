<?php $page = "Login"; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/language.php'; ?>
<?php require 'inc/steamauth/steamauth.php'; ?>
<?
logged_check();
?>

<?php
if(session_status() != PHP_SESSION_ACTIVE ){
	session_start();
}
if(isset($_POST['username'])){
	
	
	if(empty($_POST['username'])){
		$_SESSION['flash']['danger'] = $language['empty_username'];
		header("Location: login.php");
		exit();
	}

	if(empty($_POST['password'])){ 
		$_SESSION['flash']['danger'] = $language['empty_password'];
		header("Location: login.php");
		exit();
	}

	if(empty($_POST['username']) AND empty($_POST['password'])){
		$_SESSION['flash']['danger'] = $language['empty_username_password'];
		header("Location: login.php");
		exit();
	}

	if(!empty($_POST['username'])){
		if(!empty($_POST['password'])){
			include 'inc/db.php';
			$req = $pdo->prepare('SELECT * FROM users WHERE username = ?');
			$req->execute([$_POST['username']]);
			$user = $req->fetch();

			if(password_verify($_POST['password'], $user->password)){
					require_once 'inc/language.php';
					$_SESSION['flash']['success'] = $language['logged_in'];

					$req = $pdo->prepare('SELECT * FROM users WHERE username = ?');
					$req->execute([$_POST['username']]);
					$_SESSION['auth'] = $req->fetch();

					if(isset($_SESSION['auth']->steamid)){
						$req = $pdo->query('SELECT * FROM rewards_users WHERE uid = '.$_SESSION['auth']->id);
						if($req->rowCount() == 0 ){
							$pdo->query("INSERT INTO rewards_users SET uid = '".$_SESSION['auth']->id."', tag_datebegin = CURDATE()");
						}
					}

					$req = $pdo->prepare('SELECT * FROM users WHERE username = ?');
					$req->execute([$_POST['username']]);
					$ip = $req->fetch()->ip;

					if(is_null($ip)){
						if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			                $ip = $_SERVER['HTTP_CLIENT_IP'];
			            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			            } else {
			                $ip = $_SERVER['REMOTE_ADDR'];
			            }
					}
					
					$ip = htmlentities($ip);

					$req = $pdo->prepare('SELECT * FROM users WHERE (username = ? AND ip IS NOT NULL)');
					$req->execute([$_POST['username']]);
					if($req->rowCount() == 0){
						$req = $pdo->prepare('UPDATE users SET ip = ? WHERE username = ?');
						$req->execute([$ip, $_POST['username']]);
					}

					$req = $pdo->prepare('SELECT token FROM tokens WHERE token_ownerid = ?');
					$req->execute([$_SESSION['auth']->id]);
					$_SESSION['token_list'] = $req->fetchAll();

					$req = $pdo->prepare('SELECT * FROM tokens WHERE (token_ownerid = ? AND token_use > 0)');
					$req->execute([$_SESSION['auth']->id]);
					$_SESSION['token_number'] = $req->rowCount();

					header("Location: account.php");
					exit();

			}else{
				$_SESSION['flash']['danger'] = $language['error_invalid_login'];
				header("Location: login.php");
				exit();

			}
			

		}
	}

	

}

require 'inc/header.php';
?>

<?php require 'inc/language.php'; ?>
<div class="page-header">
<h1><?= $language['login'] ?></h1> 
<small><a href="register.php"><?= $language['dont_have_account']; ?></a></small>
</div>

<div class="col-md-6">
	<?= loginbutton(); ?>
</div>

<div class="col-md-6">
	<form action="" method="POST">
		<div class="form-group">
			<label><?= $language['username'] ?></label>
			<input type="text" class="form-control" name="username" placeholder="<?= $language['login'] ?>">
		</div>
		<div class="form-group">
			<label><?= $language['password'] ?></label>
			<input type="password" class="form-control" name="password" placeholder="<?= $language['password'] ?>">
		</div>

		<button type="submit" class="btn btn-primary"><?= $language['login_form'] ?></button>

	</form>
	</div>





<?php require 'inc/footer.php'; ?>