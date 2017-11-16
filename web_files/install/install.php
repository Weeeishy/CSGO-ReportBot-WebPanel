<?php 
session_start();
?>
<meta charset="UTF-8">
<link href="../css/default/bootstrap.min.css" rel="stylesheet">
<style>

form { 
margin: 0 auto; 
width:250px;
}
<?php 
$error = '';

if(isset($_POST['db_host'])){
	if(!empty(isset($_POST['db_user']))){
		if(!empty(isset($_POST['db_password']))){
			if(!empty(isset($_POST['db_name']))){
				$db_user = htmlentities($_POST['db_user']);
				$db_host = htmlentities($_POST['db_host']);
				$db_password = htmlentities($_POST['db_password']);
				$db_name = htmlentities($_POST['db_name']);

				$beginning = "mysql:dbname=$db_name;host=$db_host";
				$user = $db_user;
				$password = $db_password;
				echo $beginning;

				try {
				    $dbh = new PDO($beginning, $user, $password);
				    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				    // success
				    // store value into session

				    $_SESSION['install'] = '1';

				   	$_SESSION['db']['host'] = $db_host;
				   	$_SESSION['db']['user'] = $db_user;
				   	$_SESSION['db']['password'] = $db_password;
				   	$_SESSION['db']['name'] = $db_name;
				   	header("Location: install_db.php");
				   	exit();

				} catch (PDOException $e) {
				    $error = '<pre>Échec lors de la connexion : ' . utf8_encode($e->getMessage()). '</pre>';
				}

			}else{
				// empty or non existing db name
				echo 'no db';
			}
		}else{
			// empty or non existing pw
			echo 'no pw';
		}
	}else{
		// Empty or non existing user
		echo 'no user';
	}
}

?> 
</style>
<body>
<center><h1>Install - Setup Database - Step 1</h1>
<small><?= $error ?></center>
	<center>
		<form method="post" action="">
			<div class="form-group">
				<label>Database Host</label>
				<input type="text" class="form-control" name="db_host" required>
			</div>

			<div class="form-group">
				<label>Database User</label>
				<input type="text" class="form-control" name="db_user" required>
			</div>

			<div class="form-group">
				<label>Database Password</label>
				<input type="password" class="form-control" name="db_password">
			</div>

			<div class="form-group">
				<label>Database Name</label>
				<input type="text" class="form-control" name="db_name" required>
			</div>

			<hr />

			<button type="submit" class="btn btn-primary">Install</button>
		</form>
	</center>
</body>
