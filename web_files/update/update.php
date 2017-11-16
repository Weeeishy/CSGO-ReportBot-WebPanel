<?php
require '../inc/config.php';
require '../inc/db.php';
session_start();
$error = "";
$sql_error = "";
if(isset($_SESSION['update-auth'])){
	if($_SESSION['update-auth'] == 'OK'){

	}else{
		header("Location: index.php");
		exit();
	}
}else{
	header("Location: index.php");
	exit();
}

if(isset($_FILES['sql'])){
	$file_name = $_FILES['sql']['name'];
	$file_format = substr($file_name, -4);
	if($file_format == ".sql" OR $file_format == ".SQL"){
		$sql_request = file_get_contents($_FILES['sql']['tmp_name']);

		try {
			$req = $pdo->prepare($sql_request);
			$req->execute();

			$sql_error = "<font color='green'>Successfully imported <code>$file_name</code>.</font>";

		} catch (PDOException $e) {
  			$sql_error = "<pre>SQL Error:<br>".$e->getMessage()."</pre>";
		}


	}else{
		$error = '<font color="red">Error: Invalid file.</font>';
	}
}

if(isset($_GET['logout'])){
	unset($_SESSION['update-auth']);
	header("Location: index.php");
	exit();
}
?>

<head>
<style>

.col-centered{
float: none;
margin: 0 auto;
}

</style>
<link href="../css/readable/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<?= $sql_error ?>
<br />
<a href="?logout">Logout</a>
	
	<h1>SQL Import <?= $error ?></h1>
		<form method="post" action="" enctype="multipart/form-data">
			<div class="form-group">
				<label>Select the SQL File to import</label>
    <input type="file" name="sql">
			</div>

			<hr />

			<button class="btn btn-primary" type="submit">Import</button>
		</form>
	

</body>