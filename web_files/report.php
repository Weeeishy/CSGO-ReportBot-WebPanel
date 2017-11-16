<?php
session_start();
$page = "Report"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
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



if(isset($_GET['clear'])){
	unset($_SESSION['report']);
	header("Location: report.php");
	exit();
}

if(isset($_SESSION['report']['steamid'])){
	$steamid = $_SESSION['report']['steamid'];
}else{
	$steamid = "";
}

if(isset($_SESSION['report']['sharecode'])){
	$code = $_SESSION['report']['sharecode'];
}else{
	$code = "";
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


		$_SESSION['report']['steamid'] = $steamid;

	}else{
		// Invalid link
		unset($_SESSION['report']->steamid);
		$_SESSION['flash']['danger'] = $language['converter_invalid_steamid'];
		header("Location: converter.php");
		exit();

	}

}



if(isset($_POST['sharecode'])){

	$code = $_POST['sharecode'];
	$_SESSION['matchid'] = $code;
	if(strpos($code, 'steam://rungame/730/76561202255233023/+csgo_download_match%20') !== false){
		$code = str_replace("steam://rungame/730/76561202255233023/+csgo_download_match%20", "", $code);
	}
	
	$code = file_get_contents("http://api.cs-report.me/sharecode.php?code=$code");
	//echo $code;
	$code = json_decode($code);
	$code = $code->msg;
	$code = str_replace("Match-ID: ", '', $code);
	$_SESSION['report']['matchid'] = $code;
}



require 'inc/header.php';

if(!isset($_SESSION['token_number'])){
	$have_token = '0';
}elseif($_SESSION['token_number'] > 0){
	$have_token = '1';
}else{
	$have_token = '0';
}

if($is_logged == 'true'){
unset($_SESSION['token_list']);
unset($_SESSION['token_number']);
}


if(isset($_GET['l'])){
	include 'inc/report_log.php';
}else{
	?>

<script src='https://www.google.com/recaptcha/api.js'></script>

<h1>Report</h1> <a href="?clear">Clear fields</a>

<?php include 'inc/timer_reportbot.php'; ?>
<form action="inc/do_report.php" method="post">
	<div class="form-group">
		<label>SteamID 64</label> <b><a href="#steamid-converter" data-toggle="modal">SteamID converter</a></b>
		<input required type="number" class="form-control" name="steamid" value="<?= $steamid ?>" placeholder="SteamID64 (ex: 756xxxxxxxxxxxxxx)">
	</div>

	<div class="form-group">
		<label>MatchID</label> <b><a href="#sharecode-converter" data-toggle="modal">Sharecode converter</a></b>
		<input type="text" name="matchid" value="<?= $code ?>" placeholder="Not needed" class="form-control">
	</div>

	<hr />

	<div class="form-group">
		
	<?php
	if(isset($_SESSION['auth'])){
		$uid = $_SESSION['auth']->id; 
		$req = $pdo->query("SELECT * FROM tokens WHERE token_ownerid = '$uid' AND token_type = 'report' AND token_use > 0");
		if($req->rowCount() == 0){
			?>
			<label><?= $language['report_enter_token'];  ?></label>
    		<input type="text" class="form-control" name="token_custom">
			<?php
		}else{
			$_SESSION['token_list'] = $req->fetchAll();
			echo "<label>".$language['report_select_token']."</label>";
			echo "<select class='form-control' name='token'>";
			foreach ($_SESSION['token_list'] as $key => $value) {
				echo "<option value='$value->token'>$value->token</option>";
			}
			echo '</select>';
			echo "<hr />";

			?>		
			<label><?= $language['report_custom_token'];  ?></label>
    		<input type="text" class="form-control" name="token_custom">
			<?php
		}

	}else{
		?>
		<label><?= $language['report_enter_token'];  ?></label>
		<input type="text" class="form-control" name="token_custom">
	<?php }?>
		
	</div>



	<div class="form-group">
		<label>
		<input type="checkbox" required> <?= $language['report_checkbox_text']; ?>
		</label>

		<div class="g-recaptcha" data-sitekey="<?= $website_key ?>"></div>
	</div>

	<button type="submit" class="btn btn-primary">Report</button>

</form>


  <!-- Modal -->
<div class="modal fade" id="sharecode-converter" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			Sharecode converter
			</div>

			<div class="modal-body">
				<form method="post" action="">
					<div class="form-group">
						<label>CS:GO Sharecode</label>
						<input type="text" name="sharecode" class="form-control" placeholder="CSGO-xxxxx-xxxxx-xxxxx-xxxxx-xxxxx" required>
					</div>

					<button class="btn btn-primary" type="submit">Convert</button>
				</form>
			</div>
		</div>
	</div>
</div>

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
require 'inc/footer.php' ?>
