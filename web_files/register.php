<?php $page = "Register"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php';
logged_check(); ?>
<?php
if(session_status() != PHP_SESSION_ACTIVE ){
	session_start();
}

if(isset($_POST['username'])){
	
	
	if(empty($_POST['username'])){
		$_SESSION['flash']['danger'] = "Username can't be empty";
	}

	if(empty($_POST['password'])){
		$_SESSION['flash']['danger'] = "Password can't be empty";
	}
	
	
	if(empty($_POST['password_confirm'])){
		$_SESSION['flash']['danger'] = "Please, confirm your password";
	}

	if(empty($_POST['username']) AND empty($_POST['password'])){
		$_SESSION['flash']['danger'] = "Username and Password can't be empty";
	}

	$username = htmlentities(htmlspecialchars($_POST['username']));
	$password = htmlentities(htmlspecialchars($_POST['password']));
	$email = htmlentities(htmlspecialchars($_POST['email']));
	$subscribe = htmlentities(htmlspecialchars($_POST['subscribe']));
	$password_confirm = htmlentities(htmlspecialchars($_POST['password_confirm']));
	
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		//invalid email
		$_SESSION['flash']['danger'] = "Invalid email address.";
		header("Location: register.php");
		exit();
	}
	
	if($subscribe == "on"){
		$subscribe = "1";
	}else{
		$subscribe = "0";
	}

	if(!empty($username)){
		if(!empty($password) AND !empty($password_confirm)){
			if($password == $password_confirm){

				include 'inc/db.php';
				$req = $pdo->prepare('SELECT id FROM users WHERE username = ?');
				$req->execute([htmlentities(htmlspecialchars($_POST['username']))]);
				$user_exist = $req->fetch();
				if($user_exist){
					$_SESSION['flash']['danger'] = "User already exist";
					header("Location: register.php");
					exit();

				}else{
					if(ctype_alnum(htmlentities(htmlspecialchars($_POST['username'])))){ 

						if(isset($_POST['steamid64'])){
							// user registered with steam
							if(ctype_digit($_POST['steamid64'])){
								// steamid is number only
								// valid steamid
								// we need to check if a user is already registered with this steamid
								$req = $pdo->prepare('SELECT * FROM users WHERE steamid = ?');
								$req->execute([htmlentities(htmlspecialchars($_POST['steamid64']))]);

								if($req->rowCount() == 0){
									// we can register
								}else{
									// there is already a user with this SteamID
									$_SESSION['flash']['danger'] = "SteamID already registered.";
									header("Location: login.php");
									exit();
								}
								$steamid = htmlentities(htmlspecialchars($_POST['steamid64']));
							}
						}else{
							$steamid = NULL;
						}
						
						// we need to check if email is not already used
						$req = $pdo->prepare("SELECT * FROM users WHERE email = ?");
						$req->execute([$email]);
						if($req->rowCount() == 1){
							//email already used
							$_SESSION['flash']['danger'] = "Email already registered.";
							header("Location: login.php");
							exit();
						}
						
						$password = password_hash($password, PASSWORD_BCRYPT );
						$req = $pdo->prepare('INSERT INTO users SET username = ?, password = ?, steamid = ?, email = ?, email_subscribed = ?');
						$req->execute([$username, $password, $steamid, $email, $subscribe]);

						$req = $pdo->prepare('SELECT * FROM users WHERE username = ?');
						$req->execute([$username]);

						$_SESSION['auth'] = $req->fetch();

						if(isset($_SESSION['auth']->steamid)){
							$req = $pdo->query('SELECT * FROM rewards_users WHERE uid = '.$_SESSION['auth']->id);
							if($req->rowCount() == 0 ){
								$pdo->query("INSERT INTO rewards_users SET uid = '".$_SESSION['auth']->id."', tag_datebegin = CURDATE()");
							}
						}
						
						$_SESSION['flash']['info'] = "Successfully logged-in.";
						header("Location: account.php");
						exit();
					}else{
						$_SESSION['flash']['danger'] = "Invalid Username.";
					}
				}
			

			}else{
				$_SESSION['flash']['danger'] = "Your password doesn't match. Please re-enter your password";
			}
		}

	}



	

}

?>

<?php require 'inc/header.php'; ?>
<?php require 'inc/language.php'; ?>

<div class="page-header">
<h1><?= $language['register']; ?></h1> 
<small><a href="login.php"><?= $language['already_have_account'] ?></a></small>
</div>
	<form action="" method="POST">
		<div class="form-group">
			<label><?= $language['username'] ?></label>
			<input type="text" class="form-control" name="username" placeholder="<?= $language['username'] ?>">
		</div>
		
		<div class="form-group">
			<label>Email address</label>
			<input type="text" class="form-control" name="email" value="">
			
			<label>
			<input type="checkbox" checked name="subscribe"> I want to receive email from administrators </label>
		</div>
		
		<hr />
		
		<?php if(isset($_GET['steamid'])){ ?>
		<div class="form-group">
			<label>SteamID</label>
			<input type="text" class="form-control" name="steamid" value="<?= $_GET['steamid'] ?>" disabled>
			<input type="hidden" name="steamid64" value="<?= $_GET['steamid'] ?>">
		</div>
		<?php } ?>

		<div class="form-group">
			<label><?= $language['password'] ?></label>
			<input type="password" class="form-control" name="password" placeholder="<?= $language['password'] ?>">
		</div>

		<div class="form-group">
			<label><?= $language['confirm_password'] ?></label>
			<input type="password" class="form-control" name="password_confirm" placeholder="<?= $language['confirm_password'] ?>">
		</div>

		<button type="submit" class="btn btn-primary"><?= $language['register']; ?></button>

	</form>




<?php require 'inc/footer.php'; ?>