<?php
if(session_status() != PHP_SESSION_ACTIVE ){
session_start();
}
require_once 'config.php';
require_once 'db.php';
require 'language.php';

/* GET UPDATE DETAILS*/
if(isset($_SESSION['auth']) AND $_SESSION['auth']->is_admin == 1 AND !isset($_SESSION['version_ok'])){
    $last_date = getLastDate();
    $last_version = getLastVersion();
    

    if($version != $last_version){
        showVersionAlert($last_version, $last_date);
    }else{
        $_SESSION['version_ok'] = "ok";
    }
}

$req = $pdo->prepare('SELECT * FROM themes WHERE use_this_theme = 1');
$req->execute();

if(($req->rowCount() > 1) or ($req->rowCount() == 0)){
    $theme = 'default';
}else{
    $theme = $req->fetch()->theme_name;
}

if(isset($_POST['lang'])){
    $_SESSION['lang'] = htmlentities($_POST['lang']);
    header("Location: index.php");
    die();
}

if(isset($_GET['logout'])){
    unset($_SESSION['auth']);
	unset($_SESSION['version_ok']);
    require 'language.php';
    $_SESSION['flash']['info'] = $language['logged_out'];
    header("Location: index.php");
    exit();
}

if(isset($_SESSION['auth'])){
	$is_logged = '1';
}else{
	$is_logged = '0';
}

function user_logged(){
    if(isset($_SESSION['auth'])){
        return true;
    }else{
        return false;
    }
}

function user_admin(){
    if(user_logged()){
            if($_SESSION['auth']->is_admin == "1"){
                return true;
            }else{
                return false;
            }
    }else{
        return false;
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
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $website_title . ' - ' . $page?></title>

    <!-- Bootstrap Core CSS -->
    <link href="css/<?= $theme ?>/bootstrap.min.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">



    <!-- Custom CSS -->
    <style>
    body {
        padding-top: 70px;
        /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
    }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="./index.php"><?= $website_navtitle ?></a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">

                <?php
                // navbar
                $req = $pdo->query("SELECT * FROM navbar ORDER BY display_order ASC");
                while($row = $req->fetch()): ?>

                    <?php if($row->access_level == "all") : // Everyone can see it ?>
                        <?php if($row->type == "item" AND ($row->parentid == "0" OR is_null($row->parentid))) : // this is a single item ?>
                            <li><a style="color: <?=$row->text_color ?>;" href="<?=$row->link?>"><?=$row->name?></a></li>
                        <?php endif; ?>

                        <?php if($row->type == "dropdown_parent") : //dropdown parent ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$row->name?><span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                        <?php
                                        $r = $pdo->query("SELECT * FROM navbar WHERE parentid = '$row->id'");
                                        while($g = $r->fetch()){
                                            if($g->access_level == "all"){
                                                echo "<li><a href='$g->link'>$g->name</a></li>";
                                            }

                                            if($g->access_level == "logged" AND user_logged()){
                                                echo "<li><a href='$g->link'>$g->name</a></li>";
                                            }

                                            if($g->access_level == "admin" AND user_admin()){
                                                echo "<li><a href='$g->link'>$g->name</a></li>";
                                            }

                                        } ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; // end item for all?>

                    <?php if($row->access_level == "non_logged" AND user_logged() == false) : // Only non logged can see it ?>
                        <?php if($row->type == "item" AND ($row->parentid == "0" OR is_null($row->parentid))) : // this is a single item ?>
                            <li><a style="color: <?=$row->text_color ?>;" href="<?=$row->link?>"><?=$row->name?></a></li>
                        <?php endif; ?>

                        <?php if($row->type == "dropdown_parent") : //dropdown parent ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$row->name?><span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                        <?php
                                        $r = $pdo->query("SELECT * FROM navbar WHERE parentid = '$row->id'");
                                        while($g = $r->fetch()){
                                            if($g->access_level == "all"){
                                                echo "<li><a href='$g->link'>$g->name</a></li>";
                                            }

                                        } ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; // end item for all?>

                    <?php if($row->access_level == "logged" AND user_logged()) : // Logged can see it ?>
                        <?php if($row->type == "item" AND ($row->parentid == "0" OR is_null($row->parentid))) : // this is a single item ?>
                            <li><a style="color: <?=$row->text_color ?>;" href="<?=$row->link?>"><?=$row->name?></a></li>
                        <?php endif; ?>

                        <?php if($row->type == "dropdown_parent") : //dropdown parent ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$row->name?><span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                        <?php
                                        $r = $pdo->query("SELECT * FROM navbar WHERE parentid = '$row->id'");
                                        while($g = $r->fetch()){
                                            echo "<li><a href='$g->link'>$g->name</a></li>";
                                        } ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; // end item for logged only?>

                     <?php if($row->access_level == "admin" AND user_admin()) : // Logged can see it ?>
                        <?php if($row->type == "item" AND ($row->parentid == "0" OR is_null($row->parentid))) : // this is a single item ?>
                            <li><a style="color: <?=$row->text_color ?>;" href="<?=$row->link?>"><?=$row->name?></a></li>
                        <?php endif; ?>

                        <?php if($row->type == "dropdown_parent") : //dropdown parent ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$row->name?><span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                        <?php
                                        $r = $pdo->query("SELECT * FROM navbar WHERE parentid = '$row->id' AND access_level = 'admin'");
                                        while($g = $r->fetch()){
                                            echo "<li><a href='$g->link'>$g->name</a></li>";
                                        } ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; // end item for admin only?>


                <?php endwhile; ?>
               
               

                </ul>
                
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">
        <?php if(isset($_SESSION['flash'])): ?>
            <?php foreach($_SESSION['flash'] as $type => $message): ?>
                <div class="alert alert-<?= $type ?>">
                    <?= $message ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['flash_rewards'])): ?>
            <?php foreach($_SESSION['flash_rewards'] as $type => $message): ?>
                <div class="alert alert-<?= $type ?>">
                    <?= $message ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash_rewards']); ?>
        <?php endif; ?>


        <?php if(isset($_SESSION['flash_admin'])): ?>
            <?php foreach($_SESSION['flash_admin'] as $type => $message): ?>
                <div class="alert alert-<?= $type ?>">
                    <?= $message ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash_admin']); ?>
        <?php endif; ?>

        <?php

        if($is_logged == 1){
            $uid = $_SESSION['auth']->id;
            $req = $pdo->prepare('SELECT * FROM banned_users WHERE uid = ? AND expiration > CURDATE()');
            $req->execute([$uid]);

            if($req->rowCount() > 0){
                while($row = $req->fetch()){
                    require 'language.php';
                    $message = "<div class='alert alert-danger'>".$language['you_are_banned']." $row->reason - ".$language['ban_expire']." $row->expiration</div>";
                    die($message);
                }
            }
        }else{


            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
			
			$ip = htmlentities($ip);

            $req = $pdo->prepare('SELECT * FROM banned_users WHERE ip = ? AND expiration > CURDATE()');
            $req->execute([$ip]);

            if($req->rowCount() > 0){
                while($row = $req->fetch()){
                    require 'language.php';
                    $message = "<div class='alert alert-danger'>".$language['you_are_banned']." $row->reason - ".$language['ban_expire']." $row->expiration</div>";
                    die($message);
                }
            }
        }

        if($is_logged == 1){
            // ticket notification
            $req = $pdo->prepare('SELECT * FROM support_tickets WHERE uid = ? AND (user_viewed != 1 OR user_viewed IS NULL)');
            $req->execute([$_SESSION['auth']->id]);
            if($req->rowCount() > 0){
                $_SESSION['flash']['danger'] = "<a href='support.php' style='color: white;'>".$language['user_ticket_notification']."</a>";
            }

            if($_SESSION['auth']->is_admin == 1){
                // admin notification
                $req = $pdo->query('SELECT * FROM support_tickets WHERE (admin_viewed != 1 OR admin_viewed IS NULL)');
                if($req->rowCount() > 0){

                    $_SESSION['flash_admin']['danger'] = "<a href='/admin/support.php' style='color: white;'>".$language['admin_ticket_notification']."</a>";
                }

            }
        }

        /*
        ----- REWARDS NOTIFICATION -------
        */

        if($is_logged == 1){
            $req = $pdo->query("SELECT * FROM rewards_users WHERE uid = '".$_SESSION['auth']->id."'");
            while($row = $req->fetch()){
                $points = $row->points;
                $enable_notification = $row->enable_notification;                

                $r = $pdo->query("SELECT * FROM rewards_list WHERE $points >= points_cost");
                if($r->rowCount() > 0){
                    while($get = $r->fetch()){
                        if($enable_notification == 1){
                            $_SESSION['flash_rewards']['success'] = "<a href='/rewards.php' style='color: white;'>You have enought point to get a reward!</a>";
                        }
                    }
                }
            }
        }
		
		/*
		----------- EMAIL NOTIFICATION -------
		*/
		if($is_logged == 1){
		if(empty($_SESSION['auth']->email)) : ?>
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
							<input type="checkbox" checked name="subscribe"> I want to receive email from administrators </label>
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

		<?php endif;
		} ?>