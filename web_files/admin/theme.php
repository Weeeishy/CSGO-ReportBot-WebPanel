<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/db.php';
is_admin();

if(isset($_POST['theme'])){
	// first, we check if theme exist
	$theme = htmlentities($_POST['theme']);

	$req = $pdo->prepare('SELECT * FROM themes WHERE theme_name = ?');
	$req->execute([$theme]);

	if($req->rowCount() == 0){
		// doesnt exist
		$_SESSION['flash']['danger'] = "Theme doesnt exist.";
		header("Location: theme.php");
		exit();
	}

	$req = $pdo->prepare('UPDATE themes SET use_this_theme = 0');
	$req->execute();

	$req = $pdo->prepare('UPDATE themes SET use_this_theme = 1 WHERE theme_name = ?');
	$req->execute([$theme]);

	$_SESSION['flash']['success'] = "Theme changed successfully";
	header("Location: theme.php");
	exit();

}

require_once 'inc/header.php';
	 ?>

<h1>Theme selector</h1>

<div class="col-md-12">
	<form method="post" action="">
			<div class="form-group">
				<label>Select a theme</label>
				<select class="form-control" name="theme">
					<?php
					$req = $pdo->prepare('SELECT * FROM themes ORDER BY use_this_theme DESC');
					$req->execute();

					while($row = $req->fetch()){
						echo '<option>' . $row->theme_name . '</option>';
					}

					?>	
				</select>
		</div>
		<button type="submit" class="btn btn-default">Submit</button>
	</form>
</div>

<?php include 'inc/footer.php'; ?>