<?php $page = "Commend"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/header.php'; ?>
<?php require 'inc/db.php'; 
require 'inc/language.php';

if(isset($_GET['steamid'])){
	$steamid = $_GET['steamid'];
}else{
	$steamid = "";
}

if(isset($_SESSION['auth'])){
	$is_logged = 'true';
}else{
	$is_logged = 'false';
}

if($is_logged == 'true'){
unset($_SESSION['token_list']);
unset($_SESSION['token_number']);

$req = $pdo->prepare('SELECT token FROM tokens WHERE token_ownerid = ? AND token_use > 0 AND token_type = "commend"');
$req->execute([$_SESSION['auth']->id]);
$_SESSION['token_list'] = $req->fetchAll();

$req = $pdo->prepare('SELECT * FROM tokens WHERE (token_ownerid = ? AND token_use > 0 AND token_type = "commend")');
$req->execute([$_SESSION['auth']->id]);
$_SESSION['token_number'] = $req->rowCount();

}

if(!isset($_SESSION['token_number'])){
	$have_token = '0';
}elseif($_SESSION['token_number'] > 0){
	$have_token = '1';
}else{
	$have_token = '0';
}

if(isset($_POST['profile']) AND !empty($_POST['profile'])){
	if((strstr($_POST['profile'], "http://steamcommunity.com/profiles/")) or (strstr($_POST['profile'], "https://steamcommunity.com/profiles/")) or (strstr($_POST['profile'], "http://steamcommunity.com/id/")) or (strstr($_POST['profile'], "https://steamcommunity.com/id/"))){
		// Valid link
		$url = $_POST['profile'];
		$parsed = parse_url($url);

		if(isset($parsed['scheme'])){
			if($parsed['scheme'] == "http" OR $parsed['scheme'] == "https"){

			}
		}else{
			$url = "http://" . $url;
		}
		$steam_url = $url . '?xml=1';
		$xml = simplexml_load_file(rawurlencode($steam_url));


		$steamid = (string) $xml->{'steamID64'};
		//$steamid = simplexml_load_file($xml);

	}else{
		// Invalid link
		//unset($_SESSION['report']->steamid);
		$_SESSION['flash']['danger'] = $language['converter_invalid_steamid'];
		header("Location: commend.php");
		exit();

	}

}

echo "<script src='https://www.google.com/recaptcha/api.js'></script>";

if(!isset($_POST['steamid'])){
	if(isset($_GET['l'])){
		include 'inc/commend_log.php';
	}else{
	?>

<h1><?= $language['commend']; ?></h1>

<?php include 'inc/timer_commendbot.php'; ?>

<form action="" method="post">
	<div class="form-group">
		<label>SteamID 64</label> <b><a href="#steamid-converter" data-toggle="modal">SteamID converter</a></b>
		<input required type="number" class="form-control" name="steamid" value="<?= $steamid ?>" placeholder="SteamID64 (ex: 756xxxxxxxxxxxxxx)">
	</div>

	<div class="form-group">
		
		<?php 
		if($is_logged == 'true' AND $have_token == '1'): ?>

		<div class="form-group">
			<label><?= $language['commend_select_token']; ?></label>
			<select class="form-control" name="token">
		
		<?php foreach($_SESSION['token_list'] as $type => $message): ?>

			<option><?= $message->token ?></option>

        <?php endforeach; ?>

        	</select>
        </div>
        <hr/>
        <div class="form-group">
        	<label><?= $language['commend_custom_token']; ?></label>
        	<input type="text" class="form-control" name="token_custom">
        </div>

    	<?php else: ?>
    		<label><?= $language['commend_enter_token']; ?></label>
    		<input type="text" class="form-control" name="token_custom">
		<?php endif; ?>
	</div>

	<div class="form-group">
		<label>
		<input type="checkbox" required> <?= $language['commend_checkbox_text']; ?>
		</label>
		<div class="g-recaptcha" data-sitekey="<?= $website_key ?>"></div>


	</div>

	<button type="submit" class="btn btn-primary"><?= $language['commend_button']; ?></button>
</form>

<div class="modal fade" id="steamid-converter" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			SteamID converter
			</div>

			<div class="modal-body">
				<form method="post" action="">
					<div class="form-group">
						<label>Steam profile</label>
						<input type="text" name="profile" class="form-control" placeholder="http://steamcommunity.com/id/xxxxxxxx/" required>
					</div>

					<button class="btn btn-primary" type="submit">Convert</button>
				</form>
			</div>
		</div>
	</div>
</div>


<?php
	}
}else{
// if post steamid
	

	if(empty($_POST['steamid'])){
		
		$_SESSION['flash']['danger'] = $language['error_no_steamid']; // no steamid 
		header("Location: ../commend.php");
		exit();
	}

	if(!ctype_digit($_POST['steamid'])){
		
		$_SESSION['flash']['danger'] = $language['error_steamid_numberonly']; // steamid isnt only numbers
		header("Location: ../commend.php");
		exit();
	}

	if(strlen($_POST['steamid']) != 17){
		
		$_SESSION['flash']['danger'] = $language['error_steamid_invalid']; // invalid steamid
		header("Location: ../commend.php");
		exit();
	}

	$steamid = $_POST['steamid'];
	if(isset($_POST['token'])){
		$token = $_POST['token'];
	}


	if(isset($_POST['token_custom']) AND strlen($_POST['token_custom']) <= 0){
		unset($_POST['token_custom']);

	}

	if(isset($_POST['token_custom'])){
		if(!strlen($_POST['token_custom']) <= 0){
			$token = $_POST['token_custom'];
		}
	}
	
	$req = $pdo->prepare("SELECT * FROM tokens WHERE token = ? AND token_use > 0");
	$req->execute([$token]);
	if($req->rowCount() == 0){
		echo "Invalid token. <a href='commend.php'>Click here to go back</a>";
		require 'inc/footer.php';
		die();

	}

?>
<h1><?= $language['commendbot_select']?></h1>
<p><?= $language['commendbot_token']?>: <?= $token ?></p>
<p><?= $steamid ?> <?=$language['commendbot_commended']?>:
</p>

<?php
$commendbot = array();
for($i = 1; $i <= $commendbot_number; $i++){
	$req = $pdo->prepare("SELECT * FROM commended_list WHERE commendbot_id = ? AND steamid = ?");
	$req->execute([$i, $steamid]);
	${"commend".$i} = $req->rowCount();

	if(${"commend".$i} == 0){
		$r = $pdo->query("SELECT * FROM commended_list WHERE (commendbot_id = '$i' AND NOW() < datum + interval $commend_timer hour)");
		if($r->rowCount() == 0){
			array_push($commendbot, "Commendbot #$i"); 
		}
	}
}


if(empty($commendbot)){
	echo "No commendbot available for this SteamID.";
}else{
	echo "<ul>";
	foreach ($commendbot as $key => $value) {
		# code...
		echo "<li>$value</li>";
	}
	echo "</ul>";
}



if(!empty($commendbot)) : ?>

<hr />

<form method="post" action="inc/do_commend.php">
	<div class="form-group">
		<label><?= $language['commendbot_select']?></label>
		<select name="commendbot_selected" class="form-control">
			<?php
			foreach ($commendbot as $key => $value) {
				# code...
				$commend = str_replace(' #','_', $value);
				$commend = strtolower($commend);
				echo "<option value='$commend'>$value</option>";
			}

			?>
		</select>
	</div>

	<input type="hidden" name="token" value="<?= $token?>">
	<input type="hidden" name="steamid" value="<?= $steamid?>">

	<div class="g-recaptcha" data-sitekey="<?= $website_key ?>"></div>

	<button type="submit" class="btn btn-success"><?= $language['commendbot_commend']?></button>
</form>
<?php 
endif;
}
require 'inc/footer.php' ?>

