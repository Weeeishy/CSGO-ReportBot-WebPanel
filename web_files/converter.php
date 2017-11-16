<?php $page = "SteamID Converter"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/db.php'; 
require 'inc/language.php';
$converted = "0";
if(isset($_POST['steam']) AND !empty($_POST['steam'])){
	if((strstr($_POST['steam'], "http://steamcommunity.com/profiles/")) or (strstr($_POST['steam'], "https://steamcommunity.com/profiles/")) or (strstr($_POST['steam'], "http://steamcommunity.com/id/")) or (strstr($_POST['steam'], "https://steamcommunity.com/id/"))){
		// Valid link
		$url = htmlentities($_POST['steam']);
		$url = str_replace(" ", "", $url);
		$parsed = parse_url($url);

		if(isset($parsed['scheme'])){
			if($parsed['scheme'] == "http" OR $parsed['scheme'] == "https"){

			}
		}else{
			$url = "http://" . $url;
		}
		$steam_url = $url . '?xml=1';
		$xml = simplexml_load_file(rawurlencode($steam_url));


		$steamid = $xml->{'steamID64'};
		$avatar = $xml->{'avatarFull'};
		$name = $xml->{'steamID'};
		$status = $xml->{'onlineState'};
		
		if(empty($steamid)){
			$_SESSION['flash']['danger'] = $language['converter_invalid_steamid'];
			header("Location: converter.php");
			exit();
		}

		if($status == 'offline'){
			$status = '<b><font style="color: red;">'.$language['converter_offline'].'</font></b>';
		}elseif($status == 'online'){
			$status = '<b><font style="color: green;">'.$language['converter_online'].'</font></b>';

		}elseif($status == 'in-game'){	
			$status = '<b><font style="color: green;">'.$language['converter_ingame'].'</font></b>';

		}else{
			$status = $xml->{'onlineState'}; 
		}

		$converted = "1";

	}else{
		// Invalid link
		$_SESSION['flash']['danger'] = $language['converter_invalid_steamid'];
		header("Location: converter.php");
		exit();

	}

}
require 'inc/header.php';

?>

<h1><?= $language['steamid_converter'] ?></h1>

<?php

if($converted == "1"){
	// If we successfully converted someone
?>

<h1>Information:</h1>
<div class="col-md-6">
	<p><?= $language['converter_name'] ?>: <?= $name ?></p>
	<p>SteamID: <?= $steamid ?></p>
	<p><?= $language['converter_status'] ?>: <?= $status ?></p>
	<hr />
	<a href="report.php?steamid=<?=$steamid?>" target="_blank"><button class="btn btn-danger"><?= $language['converter_report'] ?></button></a>
	<a href="commend.php?steamid=<?=$steamid?>" target="_blank"><button class="btn btn-info"><?= $language['converter_commend'] ?></button></a>
	<a href="whitelist.php?steamid=<?=$steamid?>" target="_blank"><button class="btn btn-warning">Whitelist</button></a>

</div>

<div class="col-md-6">
	<p><?= $language['converter_avatar'] ?></p>
	<img src="<?= $avatar ?>">
</div>

<div class="col-md-12">
	<hr />
</div>


<?php 
}


?>
<form action="" method="post">
	<div class="form-group">
		<label>Steam URL</label>
		<input type="text" class="form-control" name="steam" placeholder="http://steamcommunity.com/profiles/">
	</div>
	<hr />

	<button type="submit" class="btn btn-default"><?= $language['converter_submit'] ?></button>
</form>


<?php require 'inc/footer.php' ?>
