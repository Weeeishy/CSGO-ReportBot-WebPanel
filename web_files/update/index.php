<?php
require '../inc/config.php';
require '../inc/db.php';
session_start();

if(isset($_POST['username'])){
	// login
	$username = $_POST['username'];
	$password = $_POST['password'];

	$req = $pdo->prepare('SELECT * FROM users WHERE username = ?');
	$req->execute([$username]);
	if($req->rowCount() == 1){
		$is_admin = $req->fetch()->is_admin;
	}else{
		$is_admin = NULL;
	}
	$req->execute([$username]);
	if($req->rowCount() == 1){
		$password_db = $req->fetch()->password;
	}else{
		$password_db = NULL;
	}

	if(password_verify($password, $password_db) AND $is_admin == 1){
		$_SESSION['update-auth'] = "OK";
		header("Location: update.php");
		exit();
	}else{
		echo '<div class="alert alert-danger">Invalid login</div>';
	}
}
?>
<head>
<link href="../css/readable/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<h1>Please, log-in.</h1>
<div class="col-lg-3">
<form method="post" action="">
	<div class="form-group">
		<label>Username</label>
		<input type="text" class="form-control" name="username">
	</div>

	<hr />

	<div class="form-group">
		<label>Password</label>
		<input type="password" class="form-control" name="password">
	</div>

	<hr />

	<button class="btn btn-primary" type="submit">Login</button>
</div>

</form>
</div>

</center>
</body>












