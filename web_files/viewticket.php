<?php $page = "Viewticket"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/db.php'; ?>
<?php require 'inc/jbbcode/Parser.php'; ?>
<?php require 'inc/language.php';
is_logged();
require_once "inc/jbbcode/Parser.php";

$parser = new JBBCode\Parser();
$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
	 
if(isset($_GET['id'])){
	$id = $_GET['id'];
	if(is_numeric($id)){
		// we need to check if ticket exist
		$req = $pdo->prepare('SELECT * FROM support_tickets WHERE id = ?');
		$req->execute([$id]);

		if($req->rowCount() == 0){
			$_SESSION['flash']['danger'] = "Ticket doesnt exist";
			header("Location: support.php");
			exit();

		}
		
		$req = $pdo->prepare('SELECT * FROM support_tickets WHERE id = ?');
		$req->execute([$id]);
		while($ticket = $req->fetch()){
			$state = $ticket->state;

			if($_SESSION['auth']->id != $ticket->uid AND $_SESSION['auth']->is_admin != 1){
				$_SESSION['flash']['danger'] = "Not allowed to see this ticket";
				header("Location: support.php");
				exit();
			}

			$user_viewed = $ticket->user_viewed;
			$admin_viewed = $ticket->admin_viewed;

			if(is_null($user_viewed)){
				$user_viewed = 0;
			}

			if(is_null($admin_viewed)){
				$admin_viewed = 0;
			}

			if($_SESSION['auth']->id == $ticket->uid AND $user_viewed == 0){
				$update = $pdo->prepare('UPDATE support_tickets SET user_viewed = 1 WHERE id = ?');
				$update->execute([$ticket->id]);
				header("Location: viewticket.php?id=" .$ticket->id);
				exit();
			}

			if($_SESSION['auth']->is_admin == 1 AND $admin_viewed == 0){
				$update = $pdo->prepare('UPDATE support_tickets SET admin_viewed = 1 WHERE id = ?');
				$update->execute([$ticket->id]);
				header("Location: viewticket.php?id=" .$ticket->id);
				exit();
			}
		

			$title = $ticket->title;
			$first_message = $ticket->message;
			$creation = $ticket->date;

			$q = $pdo->query('SELECT * FROM support_categories WHERE id = "'.$ticket->type.'"');
		  	while($get_type = $q->fetch()){
		  		$type = $get_type->category_name;
		  		$type_label = $get_type->label_type;	
		  	}

			$q = $pdo->query('SELECT * FROM users WHERE id = "'.$ticket->uid.'"');
			while($user = $q->fetch()){
				$submitted_user = $user->username;
				$user_ip = $user->ip;
			}
			
		}
	}else{
		$_SESSION['flash']['danger'] = "Invalid ID";
		header("Location: support.php");
		exit();
	}

}

if(isset($_POST['reply'])){
	$id = htmlspecialchars($_POST['id']);
	$reply = htmlentities(htmlspecialchars($_POST['reply']));
	$uid = $_SESSION['auth']->id;

	$req = $pdo->prepare('SELECT * FROM support_tickets WHERE uid = ? AND id = ?');
	$req->execute([$uid, $id]);

	if($req->rowCount() == 0 AND $_SESSION['auth']->is_admin != 1){
		$_SESSION['flash']['danger'] = "Not allowed.";
		header("Location: support.php");
		exit();
	}

	$req = $pdo->prepare('SELECT * FROM support_tickets WHERE id = ?');
	$req->execute([$id]);
	$state = $req->fetch()->state;
	if($state == 0){
		$_SESSION['flash']['danger'] = "Not allowed.";
		header("Location: support.php");
		exit();
	}

	if($_SESSION['auth']->is_admin == 1){
		$req = $pdo->prepare('UPDATE support_tickets SET state = 1, admin_viewed = 1, user_viewed = 0 WHERE id = ?');
		$req->execute([$id]);

		$req = $pdo->prepare('INSERT INTO support_messages SET parent_id = ?, uid = ?, message = ?, date = CURRENT_TIMESTAMP');
		$req->execute([$id, $uid, $reply]);
	}else{
		$req = $pdo->prepare('UPDATE support_tickets SET state = 2, admin_viewed = 0, user_viewed = 1 WHERE id = ?');
		$req->execute([$id]);

		$req = $pdo->prepare('INSERT INTO support_messages SET parent_id = ?, uid = ?, message = ?, date = CURRENT_TIMESTAMP');
		$req->execute([$id, $uid, $reply]);
	}
	
	header("Location: viewticket.php?id=$id");
	exit();

}

if(isset($_POST['close'])){
	$id = $_POST['close'];
	if(is_numeric($id)){
		// first we check if uid is valid
		$req = $pdo->prepare('SELECT * FROM support_tickets WHERE uid = ? AND id = ?');
		$req->execute([$_SESSION['auth']->id, $id]);
		if($req->rowCount() == 0 AND $_SESSION['auth']->is_admin != 1){
			$_SESSION['flash']['danger'] = "Not allowed.";
			header("Location: support.php");
			exit();
		}

		$req = $pdo->prepare('UPDATE support_tickets SET state = "0", admin_viewed = 1, user_viewed = 1 WHERE id = ?');
		$req->execute([$id]);

		header("Location: support.php");
		exit();
	}
}

require 'inc/header.php';
?>
<!-- Load jQuery  -->
<script src="js/jquery.min.js"></script>

<!-- Load WysiBB JS and Theme -->
<script src="js/jquery.wysibb.js"></script>
<link rel="stylesheet" href="css/wbbtheme.css" />

<script>
$(function() {
	var wbbOpt = {
		buttons: "bold,italic,underline,fontcolor,|,img,link"
	}
  $("#wysibb").wysibb(wbbOpt);
})
</script>
</head>

<?php
if($state != 0){
?>

<form method="post" action="">
	<input type="hidden" name="close" value="<?= $id ?>">
	<button class="btn btn-danger pull-right"><?= $language['ticket_close'] ?></button>
</form>

<?php
}else{
echo "<p class='pull-right'>".$language['ticket_closed']."</p>";
}

?>


<h1><?= $language['ticket_view'] ?></h1>
<div class="panel panel-default">
	<div class="panel-heading">
		Ticket: <?= html_entity_decode($title) ?> <span class="pull-right label label-<?= $type_label ?>"><?= $type ?></span>
	</div>

	<div class="panel-body">
		<?php $parser->parse($first_message);
		$message = html_entity_decode($parser->getAsHtml());
		echo $message; ?>
	</div>

	<div class="panel-footer">
		<?= $language['ticket_submitted'] ?>: <?= $submitted_user ?> (<?= $user_ip ?>) <p class="pull-right"><?= $creation ?></p>
	</div>

</div>


<?php

$req = $pdo->prepare('SELECT * FROM support_messages WHERE parent_id = ?');
$req->execute([$id]);
if($req->rowCount() == 0){
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<?= $language['ticket_no_answer'] ?>
	</div>
</div>
<?php

}else{
	require_once "inc/jbbcode/Parser.php";
	while($row = $req->fetch()){
		$parser = new JBBCode\Parser();
		$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
	 
		$parser->parse($row->message);
	 
		$message = html_entity_decode($parser->getAsHtml());

		$q = $pdo->query('SELECT * FROM users WHERE id = "'.$row->uid.'"');
		while($get = $q->fetch()){
			$username = $get->username;
			$is_admin = $get->is_admin;
		}
		?>
<div class="panel panel-default">
	<div class="panel-heading">
		<?php
		if($is_admin == 1){
			echo '<p>'.$language['ticket_replyfrom'].': <font color="red">(ADMIN)</font> '.$username.'</p>';
		}else{
			echo '<p>'.$language['ticket_replyfrom'].': '.$username.'</p>';
		}?>
		</div>
	<div class="panel-body">
		<?php 
		if($is_admin == 1){
			echo '<p>'.$message.'</p>';
		}else{
			echo '<p>'.$message.'</p>';
		} ?>
	</div>
	<div class="panel-footer">
		<?= $language['ticket_reply_message'] ?>: <?= $row->date ?>
	</div>
</div>
		<?php
	}
}
?>

<?php
if($state != 0){
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<?= $language['ticket_reply'] ?>
	</div>
	<div class="panel-body">
		<form method="post" action="">
			<input type="hidden" name="id" value="<?= $id ?>">
			<div class="form-group">
				<textarea id="wysibb" rows="5" name="reply" class="textarea"></textarea>
			</div>
			<button type="submit" class="btn btn-default"><?= $language['ticket_reply'] ?></button>
		</form>
	</div>
</div>
<?php
}
?>



<style>
.language {
    display: inline-block;
}
</style>

	<footer>        
        <p>&copy; 2017 <?= $website_navtitle ?><br />
        <p class="language">Language:</p>
        <?php
        $req = $pdo->prepare("SELECT * FROM language");
        $req->execute();
        $language_number = $req->rowCount();
        $id = 0;
        while($row = $req->fetch()){
            $id = $id + 1;
           if($id > 1){
            echo ' | ';
           }
            echo '
            <form class="language" method="post" name="'.$row->language_name.'" action="">
        
            <input type="hidden" name="lang" value="'.$row->lang_code.'">
            <img src="img/lang/'.$row->lang_icon.'.png" width="20" height="20" onclick="document.forms[\''.$row->language_name.'\'].submit();" title="'.$row->language_name.'">
        </form>';
        }


        ?>
		
        </p>
    </footer>
    </div>
    <!-- /.container -->

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>
 
</html>