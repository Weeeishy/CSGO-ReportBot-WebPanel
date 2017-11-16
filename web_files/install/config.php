<meta charset="UTF-8">
<link href="../css/default/bootstrap.min.css" rel="stylesheet">

<?php
session_start();

if(isset($_SESSION['install'])){
if($_SESSION['install'] == '2'){

if(isset($_POST['username'])){
	if(!empty($_POST['password'])){
		if(!empty($_POST['password_confirm'])){
			if($_POST['password'] == $_POST['password_confirm']){

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
				    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
				    // create account

				    $user_name = $_POST['username'];
				    $user_password = $_POST['password'];
				    if(ctype_alnum($user_name)){
				    	$user_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
				    	$req = $dbh->prepare('INSERT INTO users SET username = ?, password = ?, is_admin = "1", email_subscribed = "1"');
				    	//$dbh->bindParam(':username', $user_name);
				    	//$dbh->bindParam(':password', $user_password);
				    	$req->execute([$user_name, $user_password]);

				    	$req = $dbh->prepare('SELECT * FROM users WHERE username = ?');
				    	$req->execute([$user_name]);
				    	// create login session
				    	$_SESSION['auth'] = $req->fetch();
				    	// destroy sesson
				    	unset($_SESSION['install']);

				    	$_SESSION['do_config'] = "ok";
				    	header("Location: config_2.php"); // We redirect intp
				    	exit();
				    }else{
				    	// username is not valid
				    	echo "<pre>Invalid Username (Only letters and numbers)</pre>";
				    }


				} catch (PDOException $e) {
				    echo '<pre>Connection error : ' . utf8_encode($e->getMessage()). '</pre>';
				}
			}else{
				// password doesnt match
				echo "<pre>Password and Password Confirm doesn't match</pre>";
			}
		}else{
			// empty confirm
			echo "<pre>You must confirm your password</pre>";
		}
	}else{
		// empty password
		echo "<pre>Password can't be empty</pre>";
	}
}

?>
<style>
form { 
margin: 0 auto; 
width:250px;
}
</style>
<center>
	<h1>Install - Create your account - Step 2</h1>
	<form method="post">
		<div class="form-group">
			<label>Username</label>
			<input type="text" name="username" class="form-control" required>
		</div>

		<hr />

		<div class="form-group">
			<label>Password</label>
			<input type="password" name="password" class="form-control" required>
		</div>

		<div class="form-group">
			<label>Confirm your password</label>
			<input type="password" name="password_confirm" class="form-control" required>
		</div>

		<hr />
		<button class="btn btn-primary" type="submit">Create an account</button>

</center>
<?php 
}
}