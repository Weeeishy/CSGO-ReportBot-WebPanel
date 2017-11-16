<?php $page = "Account"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/db.php'; ?>
<?php is_logged(); ?>
<?php require 'inc/language.php'; ?>
<?php require 'inc/steamauth/steamauth.php'; ?>
<?php

if(isset($_POST['token'])){
	// We have a token
	if(!empty($_POST['token'])){
		$token = $_POST['token'];
		$req = $pdo->prepare('SELECT * FROM tokens WHERE token = ?');
		$req->execute([$token]);
		if($req->rowCount() == 1){
			$req = $pdo->prepare('SELECT * FROM tokens WHERE token = ? AND token_ownerid != ?');
			$req->execute([$token, $_SESSION['auth']->id]);
			if($req->rowCount() == 0){
				
				// we log the token activation
				if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			        $ip = $_SERVER['HTTP_CLIENT_IP'];
			    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			    } else {
			        $ip = $_SERVER['REMOTE_ADDR'];
			    }
				
				$ip = htmlentities($ip);
				
				$req = $pdo->prepare('INSERT INTO tokens_activation SET token = ?, type = "account", date = CURRENT_TIMESTAMP, uid = ?, uip = ?');
				$req->execute([$token, $uid, $ip]);
				
				$req = $pdo->prepare('UPDATE tokens SET token_ownerid = :owner_id WHERE token = :token');
				$req->bindParam(':owner_id' , $_SESSION['auth']->id);
				$req->bindParam(':token', $token);
				$req->execute();
				
				$_SESSION['flash']['success'] = $language['account_token_added'];
				header("Location: account.php");
				exit();
			}else{
				// token already owned
				$_SESSION['flash']['danger'] = $language['account_token_already_owned'];
				header("Location: account.php");
				exit();
				
			}
		}else{
			// token doesnt exist
			$_SESSION['flash']['danger'] = $language['account_token_doesnt_exist'];
			header("Location: account.php");
			exit();
		}
	}else{
		//empty token
		$_SESSION['flash']['danger'] = $language['account_token_empty'];
		header("Location: account.php");
		exit();
	}
}

if(isset($_POST['referral_code'])){
	//user add referral code
	$referral_code = htmlentities($_POST['referral_code']);

	$req = $pdo->prepare("SELECT * FROM referral_codes WHERE code = ?");
	$req->execute([$referral_code]);

	if($req->rowCount() == 0){
		// code doesnt exist
		$_SESSION['flash']['danger'] = "This code doesn't exist.";
		header("Location: account.php");
		exit();
	}

	//get user id
	$owner_userid = $req->fetch()->uid;

	if($_SESSION['auth']->id == $owner_userid){
		$_SESSION['flash']['danger'] = "You can't use your own referral code.";
		header("Location: account.php");
		exit();
	}

	// now check if user is already a referral from someone
	$req = $pdo->prepare("SELECT * FROM referral_users WHERE user_id = ?");
	$req->execute([$_SESSION['auth']->id]);

	if($req->rowCount() > 0){
		// already a referral
		$_SESSION['flash']['danger'] = "You already have a referral.";
		header("Location: account.php");
		exit();

	}else{
		//not a referral
		$req = $pdo->prepare("INSERT INTO referral_users SET owner_userid = ?, user_id = ?, date = CURRENT_TIMESTAMP");
		$req->execute([$owner_userid, $_SESSION['auth']->id]);
		$_SESSION['flash']['success'] = "Referral code used.";
		header("Location: account.php");
		exit();

	}
}


if(isset($_POST['edit_password'])){
	// user want to change his password
	if(!empty($_POST['edit_password'])){
		if(!empty($_POST['edit_password_confirm'])){
			if($_POST['edit_password'] == $_POST['edit_password_confirm']){
				$req = $pdo->prepare('SELECT * FROM users WHERE username = ?');
				$req->execute([$_SESSION['auth']->username]);
				$user = $req->fetch();
				
				if(password_verify($_POST['password'], $user->password)){
					// password valid
					$password = password_hash($_POST['edit_password'], PASSWORD_BCRYPT ); 
					$req = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
					$req->bindParam(':password', $password);
					$req->bindParam(':id', $_SESSION['auth']->id);
					$req->execute();
					$_SESSION['flash']['warning'] = $language['account_password_changed'];
					header("Location: account.php");
					exit();

				}else{
					// invalid password
					$_SESSION['flash']['danger'] = $language['account_password_invalid'];
					header("Location: account.php");
					exit();
				}
			}else{
				//password & password confirm doesnt match
				$_SESSION['flash']['danger'] = $language['account_password_doesnt_match'];
				header("Location: account.php");
				exit();
			}
		}else{
			// empty password confirm
			$_SESSION['flash']['danger'] = $language['account_password_empty_confirm'];
			header("Location: account.php");
			exit();
		}
	}else{
		// empty password
		$_SESSION['flash']['danger'] = $language['account_password_empty'];
		header("Location: account.php");
		exit();
	}
}

if(isset($_POST['email'])){
	$email = $_POST['email'];
	$subscribed = $_POST['subscribe'];
	
	$uid = $_SESSION['auth']->id;
	
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		//invalid email
		$_SESSION['flash']['danger'] = "Invalid email address.";
		header("Location: account.php");
		exit();
	}
	
	$req = $pdo->prepare("SELECT * FROM users WHERE email = ?");
	$req->execute([$email]);
	if($req->rowCount() == 1){
		//email already used
		$_SESSION['flash']['danger'] = "Email already registered.";
		header("Location: account.php");
		exit();
	}
	
	if($subscribed == "on"){
		$subscribed = "1";
	}else{
		$subscribed = "0";
	}
	
	$req = $pdo->prepare("UPDATE users SET email = ?, email_subscribed = ? WHERE id = ?");
	$req->execute([$email, $subscribed, $_SESSION['auth']->id]);
	
	unset($_SESSION['auth']);
	
	$req = $pdo->query("SELECT * FROM users WHERE id = '$uid'");
	$_SESSION['auth'] = $req->fetch();
	
	header("Location: account.php");
	exit();
}

require 'inc/header.php';

$uid = $_SESSION['auth']->id;

$req = $pdo->query("SELECT * FROM reported_list WHERE reportedby_userid = '$uid' ORDER BY id DESC");
$reported = $req->fetchAll();

$req = $pdo->query("SELECT * FROM commended_list WHERE commendedby_userid = '$uid' ORDER BY id DESC");
$commended = $req->fetchAll();

$list = array_merge($reported, $commended);
$count = count($list);
$count = "4";
if($count < 4){
	$md = "12";
}else{
	$md = "6";
}
usort($list, function($reported, $commended){
	$v1 = strtotime($reported->datum);
	$v2 = strtotime($commended->datum);
	return $v2 - $v1;
});
//debug($list);


?>
<h1><?= $language['your_account']; ?></h1>
<button type="button" class="btn btn-warning pull-right" data-toggle="modal" data-target="#email_option">Email option</button>
<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#edit_account"><?= $language['edit_account']?></button>
<button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#add_referral">Referral</button>


<div class="row">

	<div class="col-md-<?=$md?> col-xs-12">
		<h3>Recent action</h3>
		<table class="table table-responsive table-bordered">
			<thead>
				<tr>
					<th>SteamID</th>
					<th>Type</th>
					<th>OW/VAC</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($list as $key => $value) : ?>
					<?php if($key < 10) : ?>
						<?php 
						if(isset($value->reportbot_id)){
							// report
							$log = "<a href='report.php?l=$value->steamid'>Report</a>";
							$status = $value->ow . "|" . $value->vac;
						}else{
							// commend
							$log = "<a href='commend.php?l=$value->steamid'>Commend</a>";
							$status = "none";
						}

						switch($status){
							case "none":
								$status = "<center>-</center>";
								break;

							default:
								$status_explode = explode("|", $status);
								if($status_explode[0] == "true"){
									$status = '<center><b><font color="red">OW BAN</b></font> - ';
								}else{
									$status = '<center><font color="green">OW Clean</font> - ';
								}

								if($status_explode[1] == "true"){
									$status .= '<font color="red"><b>VAC BAN</font></b></center>';
								}else{
									$status .= '<font color="green">VAC Clean</font></center>';
								}
								break;

						}
						?>
						<tr>
							<td><a href="http://steamcommunity.com/profiles/<?=$value->steamid?>" target="_blank"><?=$value->steamid?></a></td>
							<td><?=$log?></td>
							<td><?=$status?></td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<div class="col-md-6 col-xs-12">
	
		<?php if(!isset($_SESSION['auth']->steamid)){
			echo '<h4>'.$language['link_to_steam'].'</h4>' . loginbutton();
		} ?>
		
		<h3><?= $language['about_your_account']; ?></h3>

		<table class="table table-bordered ">
			<thead>
				<tr>
					<center><th><?= $language['token']; ?></th></center>
					<center><th><?= $language['token_use_left']; ?></th></center>
				</tr>
			</thead>

			<tbody>
			<?php 
			$req = $pdo->query('SELECT * FROM tokens WHERE token_ownerid = "'.$_SESSION['auth']->id.'"');


			while($row = $req->fetch()){
				echo '
				<tr>
					<center><th>'.$row->token.'</th></center>
					<center><th>'.$row->token_use.'</th></center>
				</tr>';

			}
			?>
			</tbody>
		</table>
	</div>

	<div class="col-md-6 col-xs-12">
		<h3><?= $language['account_add_token'] ?></h3>
		<form action="" method="post">
			<div class="form-group">
				<input type="text" name="token" placeholder="0" class="form-control">
			</div>

			<button type="submit" class="btn btn-primary"><?= $language['account_add_token_submit'] ?></button>

		</form>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit_account" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit my account</h5>
      </div>
      <div class="modal-body">
        <form method="post">
			<div class="form-group">
				<label><?= $language['password'] ?></label>
				<input type="password" class="form-control" name="edit_password">
			</div>

			<div class="form-group">
				<label><?= $language['confirm_password'] ?></label>
				<input type="password" class="form-control" name="edit_password_confirm">
			</div>

			<hr />

			<div class="form-group">
				<label><?= $language['current_password'] ?></label>
				<input type="password" class="form-control" name="password">
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary"><?= $language['confirm'] ?></button>
			</div>		
		</form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="email_option" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Email option</h5>
      </div>
      <div class="modal-body">
        <form method="post">
			<div class="form-group">
				<label>Edit your email address</label>
				<input type="text" class="form-control" name="email" value="<?= $_SESSION['auth']->email?>">
				
				<label>
				<input type="checkbox" <?php if($_SESSION['auth']->email_subscribed == 1){echo "checked";}?> name="subscribe"> I want to receive email from administrators </label>
			</div>


			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary"><?= $language['confirm'] ?></button>
			</div>		
		</form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add_referral" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Enter referral code</h5>
      </div>
      <div class="modal-body">
        <form method="post">
			<div class="form-group">
				<label>Enter your referral code</label>
				<input type="text" class="form-control" name="referral_code">
			</div>


			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-primary"><?= $language['confirm'] ?></button>
			</div>		
		</form>
      </div>
    </div>
  </div>
</div>


<?php require 'inc/footer.php'; ?>