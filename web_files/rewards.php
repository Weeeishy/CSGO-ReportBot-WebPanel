<?php $page = "Rewards"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/db.php'; ?>
<?php require 'inc/language.php';
is_logged();
$uid = $_SESSION['auth']->id;
$req = $pdo->query("SELECT * FROM rewards_users WHERE uid = '$uid'");
while($r = $req->fetch()){
	$points = $r->points;
	$notification = $r->enable_notification;
	$group_joined = $r->joined_steamgroup;
}

$req = $pdo->query("SELECT * FROM rewards_config");
while($r = $req->fetch()){
	$tag = $r->tag;
	$consecutivedays = $r->tag_minconsecutivedays;
	$pointsperday = $r->tag_pointsperday;
	$pointsper_report = $r->pointsper_report;
	$pointsper_commend = $r->pointsper_commend;
	$steamgroup_url = $r->steamgroup_url;
	$steamgroup_points = $r->steamgroup_points;
}

if(isset($_GET['get'])){
	$id = $_GET['get'];
	if(is_numeric($id)){
		$req = $pdo->prepare('SELECT * FROM rewards_list WHERE id = ?');
		$req->execute([$id]);
		if($req->rowCount() == 1){
			$req = $pdo->prepare('SELECT * FROM rewards_list WHERE id = ?');
			$req->execute([$id]);
			while($row = $req->fetch()){
				$cost = $row->points_cost;
				$type = $row->reward_type;
				$uses = $row->reward_uses;
				$description = htmlentities($row->description);
				//die($description);
				if($points >= $cost){
					// can get this
					
					if($type == "report" OR $type == "commend"){
						require 'admin/inc/functions.php';
						$token = $type . '_' . uniqidReal();
						$pdo->query("INSERT INTO tokens SET token = '$token', token_use = '$uses', token_type = '$type', token_generation = CURRENT_TIMESTAMP, token_ownerid = '$uid'");
					}



					$pdo->query("UPDATE rewards_users SET points = points - $cost WHERE uid = '$uid'");
					$req = $pdo->prepare("INSERT INTO rewards_history SET uid = '$uid', generated_token = '$token', reward_id = '$row->id', cost = '$cost', type = '$type', uses = '$uses', description = ?");
					$req->execute([$description]);
					$_SESSION['flash']['success'] = $language['rewards_claimed'];
					header("Location: rewards.php");
					exit();
				}else{
					// not enought points
					$_SESSION['flash']['danger'] = $language['rewards_not_enought_points'];
					header("Location: rewards.php");
					exit();
				}
			}

		}else{
			// nothing found with this id
			$_SESSION['flash']['danger'] = $language['rewards_not_found'];
			header("Location: rewards.php");
			exit();
		}
		
	}
	// invalid ID
	$_SESSION['flash']['danger'] = $language['rewards_invalidid'];
	header("Location: rewards.php");
	exit();
}

if(isset($_GET['notification'])){
	$notification_get = $_GET['notification'];
	echo $notification;
	$req = $pdo->query("SELECT * FROM rewards_users WHERE uid = '$uid'");
	$notification = $req->fetch()->enable_notification;


	if($notification_get == "enable"){
		$req = $pdo->query("UPDATE rewards_users SET enable_notification = 1 WHERE uid = '$uid'");
		header("Location: rewards.php");
		exit();
	}else{
		if($notification_get == "disable"){
			
			$req = $pdo->query("UPDATE rewards_users SET enable_notification = 0 WHERE uid = '$uid'");
			header("Location: rewards.php");
			exit();
		}
	}
}

if(isset($_SESSION['auth']->steamid)){
	if($notification == 1){
		$button = '<a href="?notification=disable"><button class="btn btn-danger pull-right">'.$language['rewards_disable_notification'].'</button></a>';
	}else{
		$button = '<a href="?notification=enable"><button class="btn btn-success pull-right">'.$language['rewards_enable_notification'].'</button></a>';
	}
}

if(!isset($_SESSION['auth']->steamid)){
	echo '<a href="account.php">You need to link your account with Steam.</a>';
	die();
}


if(isset($_GET['group'])){
	if(substr($steamgroup_url, -1) != "/"){
		$steamgroup_url .= "/";
	} 

	$steamurl = $steamgroup_url . "memberslistxml/?xml=1";
	$steamid = $_SESSION['auth']->steamid;

	$req = $pdo->query("SELECT * FROM rewards_users WHERE uid = '".$_SESSION['auth']->id."' AND joined_steamgroup = 0");
	if($req->rowCount() == 0){
		// Already joinded SteamGroup
		$_SESSION['flash']['danger'] = "You already joined the group.";
		header("Location: rewards.php");
		exit();
	}

	if(CheckUserInSteamGroup($steamurl, $steamid) == true){
		// user is in group
		$req = $pdo->query("UPDATE rewards_users SET joined_steamgroup = 1, points = points + '$steamgroup_points' WHERE uid = '".$_SESSION['auth']->id."'");

		$_SESSION['flash']['success'] = "You are in the group. You received $steamgroup_points points";
		header("Location: rewards.php");
		exit();
	}else{
		$_SESSION['flash']['warning'] = "You are not in the group. Please join it: <b><a href='$steamgroup_url' style='color: white;'>$steamgroup_url</a></b>";
		header("Location: rewards.php");
		exit();
	}

}

$req = $pdo->query("SELECT * FROM rewards_users WHERE uid = '$uid'");
$uip = $req->fetch()->uip;

if(is_null($uip)){
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
	
	$ip = htmlentities($ip);

    $req = $pdo->prepare("UPDATE rewards_users SET uip = ? WHERE uid = ?");
    $req->execute([$ip, $uid]);
    header("Location: rewards.php");
    exit();
}

$req = $pdo->query("SELECT * FROM rewards_users WHERE uip = '$uip'");
if($req->rowCount() > 1){
	$message = "<div class='alert alert-danger'>Another is using rewards with the same IP. This is not allowed. Please contact an administrator.</div>";
	echo $message;
	require 'inc/footer.php';
	die();
	
}

require 'inc/header.php';
?>

<button class="btn btn-info" type="button" data-toggle="modal" data-target="#rewards_help"><?= $language['rewards_howtoget']?></button>
<?=$button?>
<h1><?= $language['rewards'] ?></h1>

<?php if(!is_null($steamgroup_url)){ ?>
<?php if($group_joined == 0){ echo '<p>You can earn '.$steamgroup_points.' points by joining our steam group! You can claim your points by clicking <a href="?group">here</a></p>';}} ?>

<p><?= $language['rewards_you_have'] ?> <?= $points ?> <?= $language['rewards_point'] ?>(s). <br />
<?= $language['rewards_text1'] ?> "<?= $tag ?>" <?= $language['rewards_text2'] ?> <?= $consecutivedays?> <?= $language['rewards_text3'] ?> <?= $pointsperday?> <?= $language['rewards_text4'] ?>.</p>

<?php

$req = $pdo->query('SELECT * FROM rewards_list');
$nb_reward = $req->rowCount();

$req = $pdo->query("SELECT * FROM rewards_list WHERE points_cost <= $points ORDER BY points_cost ASC");
$nb_reward_available = $req->rowCount();
if($req->rowCount() == 0){
	?>
<h4><?= $language['rewards_nothing_available'] ?></h4>
	<?php
}else{
?>

<table class="table table-bordered table-responsive">
	<thead>
		<tr>
			<th><?= $language['rewards_available'] ?></th>
			<th><?= $language['rewards_price'] ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>

<?php 
	while($row = $req->fetch()){
?>


		<tr>
			<td><?= $row->description ?></td>
			<td><?= $row->points_cost ?> points</td>
			<td><center><a href="?get=<?=$row->id?>"><button class="btn btn-info"><?= $language['rewards_claim'] ?></button></a></center></td>
		</tr>


<?php
	} // end while
?>
		</tbody>
</table>
<?php 
} // end if
if($nb_reward != $nb_reward_available){
	// if user cant see all rewards
	// we'll display every others in a list
	echo $language['rewards_unvailable'];
	echo "<pre>";
	$req = $pdo->query("SELECT * FROM rewards_list WHERE points_cost > $points ORDER BY points_cost ASC");
	while($row = $req->fetch()){
		?><li><?= "[".$language['rewards_price'].": $row->points_cost points] $row->description" ?></li><?php 
	} // end while
	echo "</pre>";
}// end if
?>

<!-- Modal -->
<div class="modal fade " id="rewards_help" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title"><?= $language['rewards_howtoget']?></h4>
	    </div>
	    <div class="modal-body">
	    	<p><?= $language['rewards_need_login']?></p>
	    	<table class="table table-bordered table-responsive">
	    		<thead>
	    			<tr>
	    				<th><?= $language['rewards_pointsperday'] ?></th>
	    				<th><?= $language['rewards_pointsperreport'] ?></th>
	    				<th><?= $language['rewards_pointspercommend'] ?></th>
	    			</tr>
	    		</thead>
	    		<tbody>
	    			<tr>
	    				<td><?= $pointsperday ?></td>
	    				<td><?= $pointsper_report?></td>
	    				<td><?= $pointsper_commend?></td>
	    			</tr>
	    		</tbody>
	    	</table>
	    </div>
	  </div>
	  
	</div>
</div>

<?php require 'inc/footer.php'; ?>