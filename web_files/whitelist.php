<?php $page = "Whitelist"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/db.php'; 
require 'inc/language.php';
session_start();

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

if(isset($_GET['steamid'])){
	$steamid = $_GET['steamid'];
}else{
	$steamid = '';
}


if($is_logged == 'true'){
unset($_SESSION['token_list']);
unset($_SESSION['token_number']);

$req = $pdo->prepare('SELECT token FROM tokens WHERE token_ownerid = ? AND token_use > 0 AND token_type = "whitelist"');
$req->execute([$_SESSION['auth']->id]);
$_SESSION['token_list'] = $req->fetchAll();

$req = $pdo->prepare('SELECT * FROM tokens WHERE (token_ownerid = ? AND token_use > 0 AND token_type = "whitelist")');
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

require 'inc/header.php';
?>
<script src='https://www.google.com/recaptcha/api.js'></script>

<h1>Whitelist</h1>

<form action="inc/do_whitelist.php" method="post">
	<div class="form-group">
		<label>SteamID 64</label> <b><a href="#steamid-converter" data-toggle="modal">SteamID converter</a></b>
		<input required type="number" class="form-control" name="steamid" value="<?= $steamid ?>" placeholder="SteamID64 (ex: 765xxxxxxxxxxxxxx)">
	</div>

	<div class="form-group">
		
		<?php 
		if($is_logged == '1' AND $have_token == '1'): ?>

		<div class="form-group">
			<label><?= $language['report_select_token']; ?></label>
			<select class="form-control" name="token">
		
		<?php foreach($_SESSION['token_list'] as $type => $message): ?>

			<option><?= $message->token ?></option>

        <?php endforeach; ?>

        	</select>
        </div>
        <hr/>
        <div class="form-group">
        	<label><?= $language['report_custom_token'];  ?></label>
        	<input type="text" class="form-control" name="token_custom">
        </div>

    	<?php else: ?>
    		<label><?= $language['report_enter_token'];  ?></label>
    		<input type="text" class="form-control" name="token_custom">
		<?php endif; ?>
	</div>

	<div class="form-group">
		<div class="g-recaptcha" data-sitekey="<?= $website_key ?>"></div>
	</div>

	<button type="submit" class="btn btn-primary">Whitelist</button>

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

require 'inc/footer.php' ?>
