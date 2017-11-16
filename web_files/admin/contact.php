<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once '../inc/functions.php';
require_once 'inc/db.php';
is_admin();
$req = $pdo->query('SELECT steamid FROM contact');
$steamid_show = $req->fetch()->steamid;

$req = $pdo->query('SELECT custom_message FROM contact');
$custom_show = $req->fetch()->custom_message;
$custom_show = str_replace('<br />','',$custom_show);

$custom = "";

if(isset($_POST['steam_url'])){

	// We check if there is already something into contact table
	$req = $pdo->prepare('SELECT * FROM contact');
	$req->execute();
	if($req->rowCount() == 0){
		$custom = nl2br(htmlentities($_POST['custom']));
		$steam_url = htmlentities($_POST['steam_url']);

		if((strstr($steam_url, "http://steamcommunity.com/profiles/")) or (strstr($steam_url, "https://steamcommunity.com/profiles/")) or (strstr($steam_url, "http://steamcommunity.com/id/")) or (strstr($steam_url, "https://steamcommunity.com/id/"))){

		$link = $steam_url.'?xml=1';
		$xml = simplexml_load_file(rawurlencode($link));
		$steamid = $xml->{'steamID64'};

		$req = $pdo->prepare("INSERT INTO contact SET steamid = ?, custom_message = ?");
		$req->execute([$steamid, $custom]);
		header("Location: contact.php");
		exit();

		}else{
			// Invalid link
		}
	}else{
		$custom = nl2br(htmlentities($_POST['custom']));
		$steam_url = htmlentities($_POST['steam_url']);

		if((strstr($steam_url, "http://steamcommunity.com/profiles/")) or (strstr($steam_url, "https://steamcommunity.com/profiles/")) or (strstr($steam_url, "http://steamcommunity.com/id/")) or (strstr($steam_url, "https://steamcommunity.com/id/"))){

		$link = $steam_url.'?xml=1';
		$xml = simplexml_load_file(rawurlencode($link));
		$steamid = $xml->{'steamID64'};

		$req = $pdo->prepare("UPDATE contact SET steamid = ?, custom_message = ?");
		$req->execute([$steamid, $custom]);
		header("Location: contact.php");
		exit();
	}
}
	
}
require_once 'inc/header.php';
?>

<h4>Contact</h4>
<?= $custom ?>
<div class="col-md-12">

<form method="post" action="">
	<div class="form-group">
		<label>Steam Profile URL</label>
		<input type="text" name="steam_url" class="form-control" value="http://steamcommunity.com/profiles/<?= $steamid_show ?>"> 
	</div>

	<hr />
	<label>Custom message (ex: set the price of your products, what ever you want)</label>
	<textarea class="form-control" name="custom" rows="10"><?= $custom_show ?></textarea>

	<hr />
	<center><button type="submit" class="btn btn-default">Submit</button></center>
</form>

<hr />

</div>


<?php include 'inc/footer.php'; ?>