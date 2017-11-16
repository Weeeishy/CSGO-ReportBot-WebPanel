<?php $page = "Tracking"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/header.php'; ?>
<?php require 'inc/db.php'; ?>
<?php require 'inc/language.php';

if(isset($_POST['username_select'])){
	// first, check if valid username
	$username = $_POST['username_select'];

	$req = $pdo->prepare('SELECT * FROM users WHERE username = ?');
	$req->execute([$username]);
	
	if($req->rowCount() == 0){
		// user doesnt exist
		$_SESSION['flash']['danger'] = $language['tracking_invalid_username'];
		header("Location: tracking.php");
		exit();
	}

	// Now, we need to get the userID
	$req = $pdo->prepare('SELECT id FROM users WHERE username = ?');
	$req->execute([$username]);
	$user_id = $req->fetch()->id;

	// Now, we display reports about this user
	$req = $pdo->prepare("SELECT * FROM reported_list WHERE reportedby_userid = ?");
	$req->execute([$user_id]);

	if($req->rowCount() == 0){
		// Nothing found
		$_SESSION['flash']['danger'] = $language['tracking_no_records'];
		header("Location: tracking.php");
		exit();

	}else{
		$report_counts = $req->rowCount();
		echo "<h2>".$language['tracking_statsfor']." $username | $report_counts report(s) </h2>";
		echo '
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>SteamID</th>
					<th>Reportbot ID</th>
					<th>Report logs</th>
					<th>OW</th>
					<th>VAC</th>
				</tr>
			</thead>
			<tbody>';
				
			while($row = $req->fetch()){

				if($row->vac == "true"){
					$vac = "<font style='color: red;'>VAC BANNED</font>";
				}else{
					$vac = "<font style='color: green;'>Clean</font>";
				}

				if($row->ow == "true"){
					$ow = "<font style='color: red;'>OW BANNED</font>";
				}else{
					$ow = "<font style='color: green;'>Clean</font>";
				}
				echo "
				<tr>
					<td>$row->id</td>
					<td>$row->steamid</td>
					<td>$row->reportbot_id</td>
				 	<td><a href='./report.php?l=$row->steamid' target='_blank'>Report log</a></td>
					<td>$ow</td>
					<td>$vac</td>
				</tr>";
			}

		echo '
		</table>';
	}

	$req = $pdo->prepare("SELECT * FROM commended_list WHERE commendedby_userid = ?");
	$req->execute([$user_id]);

	if($req->rowCount() == 0){
		// Nothing found

	}else{
		$report_counts = $req->rowCount();
		echo "<h2>".$language['tracking_statsfor']." $code | $report_counts commend(s) </h2>";
		echo '
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>SteamID</th>
					<th>Commendbot ID</th>
					<th>Commend logs</th>
				</tr>
			</thead>
			<tbody>';
				
			while($row = $req->fetch()){

				
				echo "
				<tr>
					<td>$row->id</td>
					<td>$row->steamid</td>
					<td>$row->commendbot_id</td>
				 	<td><a href='./commend.php?l=$row->steamid' target='_blank'>Commend log</a></td>
				</tr>";
			}

		echo '
		</table>';
	}


} 

if(isset($_POST['code'])){
	$code = htmlentities($_POST['code']);
	// lets get report for this token
	$req = $pdo->prepare("SELECT * FROM reported_list WHERE reportedby_token = ?");
	$req->execute([$code]);

	if($req->rowCount() == 0){
		// Nothing found

	}else{
		$report_counts = $req->rowCount();
		echo "<h2>".$language['tracking_statsfor']." $code | $report_counts report(s) </h2>";
		echo '
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>SteamID</th>
					<th>Reportbot ID</th>
					<th>Report logs</th>
					<th>OW</th>
					<th>VAC</th>
				</tr>
			</thead>
			<tbody>';
				
			while($row = $req->fetch()){

				if($row->vac == "true"){
					$vac = "<font style='color: red;'>VAC BANNED</font>";
				}else{
					$vac = "<font style='color: green;'>Clean</font>";
				}

				if($row->ow == "true"){
					$ow = "<font style='color: red;'>OW BANNED</font>";
				}else{
					$ow = "<font style='color: green;'>Clean</font>";
				}
				echo "
				<tr>
					<td>$row->id</td>
					<td>$row->steamid</td>
					<td>$row->reportbot_id</td>
				 	<td><a href='./report.php?l=$row->steamid' target='_blank'>Report log</a></td>
					<td>$ow</td>
					<td>$vac</td>
				</tr>";
			}

		echo '
		</table>';
	}

	$req = $pdo->prepare("SELECT * FROM commended_list WHERE commendedby_token = ?");
	$req->execute([$code]);

	if($req->rowCount() == 0){
		// Nothing found


	}else{
		$report_counts = $req->rowCount();
		echo "<h2>".$language['tracking_statsfor']." $code | $report_counts commend(s) </h2>";
		echo '
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>SteamID</th>
					<th>Commendbot ID</th>
					<th>Commend logs</th>
				</tr>
			</thead>
			<tbody>';
				
			while($row = $req->fetch()){

				
				echo "
				<tr>
					<td>$row->id</td>
					<td>$row->steamid</td>
					<td>$row->commendbot_id</td>
				 	<td><a href='./commend.php?l=$row->steamid' target='_blank'>Commend log</a></td>
				</tr>";
			}

		echo '
		</table>';
	}
}
?>	

<h1><?= $language['tracking']?></h1>

<h2><?= $language['tracking_user']?></h2>
<form action="" method="post">
	<div class="form-group">
		<select class="form-control" name="username_select">
			<?php

			$req = $pdo->prepare('SELECT * FROM users');
			$req->execute();
			while($row = $req->fetch()){
				echo '<option>'.$row->username.'</option>';
			}?>
		</select>
	</div>

	<button type="submit" class="btn btn-primary"><?= $language['tracking_submit'] ?></button>
</form>

<h2>Code tracking</h2>
<form method="post">
	<div class="form-group">
		<input type="text" class="form-control" name="code">
	</div>
	<button class="btn btn-primary" type="submit"><?= $language['tracking_submit'] ?></button>
</form>



<?php require 'inc/footer.php'; ?>