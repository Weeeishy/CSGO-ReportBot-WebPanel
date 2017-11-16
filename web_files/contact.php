<?php $page = "Contact"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/header.php'; ?>
<?php require 'inc/db.php'; ?>
<?php require 'inc/language.php';

$req = $pdo->query('SELECT steamid FROM contact');
if($req->rowCount() == 0){
	$steamid_show = "";
}else{
	$steamid_show = $req->fetch()->steamid;
}

$req = $pdo->query('SELECT custom_message FROM contact');
if($req->rowCount() == 0){
	$custom_show = "Not defined yet.";
}else{
	$custom_show = $req->fetch()->custom_message;
}
 ?>

<h1>Contact</h1>



<div class="col-md-6">
<p><?= $custom_show ?></p>
</div>

<div class="col-md-6">
	<a href="http://steamcommunity.com/profiles/<?= $steamid_show?>" target="_blank"><img src="http://steamsignature.com/profile/default/<?= $steamid_show ?>.png"></a>
</div>

<div class="col-md-12">
<hr />
</div>
<?php require 'inc/footer.php'; ?>