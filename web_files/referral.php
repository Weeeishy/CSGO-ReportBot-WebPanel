<?php $page = "Referral"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/db.php'; 
require 'inc/language.php';
session_start();

if(!isset($_SESSION['auth'])){
	header("Location: login.php");
	exit();
}





require 'inc/header.php';
$req = $pdo->query("SELECT * FROM tokens_activation WHERE uid = '$uid'");
if($req->rowCount() == 0){
	echo '<div class="alert alert-danger">You need to use at least 1 token to view this page.</div>';
	include 'inc/footer.php';
	die();

}

if(isset($_POST['referral'])){
	// referral code added/edited
	$referral_code = htmlentities($_POST['referral']);

	// first, we check if user already have a referral code
	$req = $pdo->prepare("SELECT * FROM referral_codes WHERE uid = ?");
	$req->execute([$_SESSION['auth']->id]);

	if($req->rowCount() == 1){
		//user already have a referral code
		$req = $pdo->prepare("UPDATE referral_codes SET code = ?, date_created = CURRENT_TIMESTAMP WHERE uid = ?");
		$req->execute([$referral_code, $_SESSION['auth']->id]);
		$_SESSION['flash']['success'] = "Referral code successfully created.";
		header("Location: referral.php");
		exit();
	}else{
		// user doesnt have a referral code
		$req = $pdo->prepare("INSERT INTO referral_codes SET uid = ?, code = ?");
		$req->execute([$_SESSION['auth']->id, $referral_code]);
		$_SESSION['flash']['success'] = "Referral code successfully created.";
		header("Location: referral.php");
		exit();
	}

}

$req = $pdo->prepare("SELECT * FROM referral_codes WHERE uid = ?");
$req->execute([$_SESSION['auth']->id]);

if($req->rowCount() == 1){
	while($row = $req->fetch()){
		$code = $row->code;
		$btn_text = "Edit";
	}	
}else{
	$btn_text = "Create";
	$code = "";
}

?>

<div class="row">
	<h1>Referral</h1>
	<div class="col-md-6 col-xs-12">
		<h2>Referral Code</h2>

		<form method="post">
			<div class="form-group">
				<label>Your Code</label>
				<input type="text" name="referral" value="<?=$code?>" class="form-control">
			</div>

			<button class="btn btn-success"><?=$btn_text?></button>

	</div>

	<div class="col-md-6 col-xs-12">
		<h2>Referral List</h2>

		<table class="table table-responsive table-bordered">
			<thead>
				<tr>
					<th>ID</th>
					<th>Username</th>
					<th>Purchased tokens</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$req = $pdo->query("SELECT * FROM referral_users WHERE owner_userid = '".$_SESSION['auth']->id."'");
				if($req->rowCount() == 0){
					echo "No referral.";
				}else{
					while($row = $req->fetch()) :
					$r = $pdo->query("SELECT * FROM tokens_activation WHERE uid = '$row->user_id'");
					$count = $r->rowCount();

					$r = $pdo->query("SELECT * FROM users WHERE id = '$row->user_id'");
					$username = $r->fetch()->username;
					?>
					<tr>
						<td><?=$row->id ?></td>
						<td><?=$username ?></td>
						<td><?=$count ?></td>
					<?php endwhile;
				}?>
			</tbody>
		</table>
	</div>
</div>



<?php 
require 'inc/footer.php' ?>
