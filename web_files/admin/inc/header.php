<?php
if(session_status() != PHP_SESSION_ACTIVE ){
session_start();
}
require_once '../inc/config.php';
require_once '../inc/db.php';

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


if(isset($_GET['logout'])){
    unset($_SESSION['auth']);
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

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $website_title ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="../css/<?= $theme ?>/bootstrap.min.css" rel="stylesheet">
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
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/index.php"><?= $website_navtitle ?></a>
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
                            <li><a style="color: <?=$row->text_color ?>;" href="../<?=$row->link?>"><?=$row->name?></a></li>
                        <?php endif; ?>

                        <?php if($row->type == "dropdown_parent") : //dropdown parent ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$row->name?><span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                        <?php
                                        $r = $pdo->query("SELECT * FROM navbar WHERE parentid = '$row->id'");
                                        while($g = $r->fetch()){
                                            if($g->access_level == "all"){
                                                echo "<li><a href='../$g->link'>$g->name</a></li>";
                                            }

                                            if($g->access_level == "logged" AND user_logged()){
                                                echo "<li><a href='../$g->link'>$g->name</a></li>";
                                            }

                                            if($g->access_level == "admin" AND user_admin()){
                                                echo "<li><a href='../$g->link'>$g->name</a></li>";
                                            }

                                        } ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; // end item for all?>

                    <?php if($row->access_level == "non_logged" AND user_logged() == false) : // Only non logged can see it ?>
                        <?php if($row->type == "item" AND ($row->parentid == "0" OR is_null($row->parentid))) : // this is a single item ?>
                            <li><a style="color: <?=$row->text_color ?>;" href="../<?=$row->link?>"><?=$row->name?></a></li>
                        <?php endif; ?>

                        <?php if($row->type == "dropdown_parent") : //dropdown parent ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$row->name?><span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                        <?php
                                        $r = $pdo->query("SELECT * FROM navbar WHERE parentid = '$row->id'");
                                        while($g = $r->fetch()){
                                            if($g->access_level == "all"){
                                                echo "<li><a href='../$g->link'>$g->name</a></li>";
                                            }

                                        } ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; // end item for all?>

                    <?php if($row->access_level == "logged" AND user_logged()) : // Logged can see it ?>
                        <?php if($row->type == "item" AND ($row->parentid == "0" OR is_null($row->parentid))) : // this is a single item ?>
                            <li><a style="color: <?=$row->text_color ?>;" href="../<?=$row->link?>"><?=$row->name?></a></li>
                        <?php endif; ?>

                        <?php if($row->type == "dropdown_parent") : //dropdown parent ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$row->name?><span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                        <?php
                                        $r = $pdo->query("SELECT * FROM navbar WHERE parentid = '$row->id'");
                                        while($g = $r->fetch()){
                                            echo "<li><a href='../$g->link'>$g->name</a></li>";
                                        } ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; // end item for logged only?>

                     <?php if($row->access_level == "admin" AND user_admin()) : // Logged can see it ?>
                        <?php if($row->type == "item" AND ($row->parentid == "0" OR is_null($row->parentid))) : // this is a single item ?>
                            <li><a style="color: <?=$row->text_color ?>;" href="../<?=$row->link?>"><?=$row->name?></a></li>
                        <?php endif; ?>

                        <?php if($row->type == "dropdown_parent") : //dropdown parent ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?=$row->name?><span class="caret"></span></a>

                                <ul class="dropdown-menu">
                                        <?php
                                        $r = $pdo->query("SELECT * FROM navbar WHERE parentid = '$row->id' AND access_level = 'admin'");
                                        while($g = $r->fetch()){
                                            echo "<li><a href='../$g->link'>$g->name</a></li>";
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