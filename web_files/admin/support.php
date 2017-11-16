<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/functions.php';
require_once 'inc/db.php';
require '../inc/language.php';
is_admin();


if(isset($_POST['delete_ticket_id'])){
	$ticket_id = htmlentities($_POST['delete_ticket_id']);
	if(!is_null($ticket_id)){
		$req = $pdo->prepare('SELECT * FROM support_tickets WHERE id = ?');
		$req->execute([$ticket_id]);
		if($req->rowCount() != 0){
			$req = $pdo->prepare('DELETE FROM support_tickets WHERE id = ?');
			$req->execute([$ticket_id]);

			$_SESSION['flash']['success'] = $language['admin_support_deleted'];
			header("Location: support.php");
			exit();
		}
	}
}

if(isset($_POST['edit_ticket_id'])){
	$ticket_id = htmlentities($_POST['edit_ticket_id']);
	$ticket_state = htmlentities($_POST['edit_ticket_state']);

	if(!is_null($ticket_id) AND !is_null($ticket_state)){
		if($ticket_state == 0 OR $ticket_state == 1 OR $ticket_state == 2){
			$req = $pdo->prepare('SELECT * FROM support_tickets WHERE id = ?');
			$req->execute([$ticket_id]);

			if($req->rowCount() != 0){
				$req = $pdo->prepare('UPDATE support_tickets SET state = ? WHERE id = ?');
				$req->execute([$ticket_state, $ticket_id]);

				$_SESSION['flash']['success'] = $language['admin_support_updated'];
				header("Location: support.php");
				exit();
			}
		}
	}
}

require_once 'inc/header.php';
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<!-- Load WysiBB JS and Theme -->
<script src="../js/jquery.wysibb.js"></script>
<link rel="stylesheet" href="http://cdn.wysibb.com/css/default/wbbtheme.css" />
<script>
$(function() {
	var wbbOpt = {
		buttons: "bold,italic,underline,fontcolor,|,img,link",
		height: "500px",
	}
  $("#wysibb").wysibb(wbbOpt);
})
</script>

<button class="btn btn-danger pull-right" data-toggle="modal" data-target="#delete_ticket"><?= $language['admin_support_delete'] ?></button>
<button class="btn btn-info pull-right" data-toggle="modal" data-target="#edit_ticket"><?= $language['admin_support_action'] ?></button>

<h1><?= $language['support_center']; ?></h1>

<div class="panel panel-default">
    <div class="panel-heading"><?= $language['support_your_tickets']; ?></div>
    	<div class="panel-body">
    		<div class="list-group">
			  <?php
			  $req = $pdo->query('SELECT * FROM support_tickets ORDER BY id ASC');
			  while($row = $req->fetch()){
			  	$q = $pdo->query('SELECT * FROM support_categories WHERE id = "'.$row->type.'"');
			  	while($get_type = $q->fetch()){
			  		$type = $get_type->category_name;
			  		$type_label = $get_type->label_type;	
			  	}

			  	$r = $pdo->query('SELECT * FROM users WHERE id = "'.$row->uid.'"');
			  	while($u = $r->fetch()){
			  		$username = $u->username;
			  	}

			  	if($row->admin_viewed == 1){
			  		$admin_viewed = '';
			  	}else{
			  		$admin_viewed = '<i style="color: red;">Unviewed</i>';
			  	}

			  	if($row->state == 1){
			  		$state = '<span class="label label-danger">'.$language['support_answered'].'</span>';
			  	}elseif($row->state == 0){
			  		$state = '<span class="label label-info">'.$language['support_closed'].'</span>';
			  	}else{
			  		$state = '<span class="label label-warning">'.$language['support_pending'].'</span>';
			  	}
			  	?>			  	
			  	
			  	<a href="../viewticket.php?id=<?= $row->id ?>" class="list-group-item"><?= $state ?> [ID: <?= $row->id ?>] <?= htmlspecialchars_decode($row->title) ?> [<?= $username ?>]<span class="pull-right label label-<?= $type_label ?>"><?= $type ?></span>   <?= $admin_viewed ?></a>
			  	<?php
			  }

			  ?>
			  
			  
			</div>
    	</div>
  </div>


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
            <img src="../img/lang/'.$row->lang_icon.'.png" width="20" height="20" onclick="document.forms[\''.$row->language_name.'\'].submit();" title="'.$row->language_name.'">
        </form>';
        }


        ?>
		
        </p>
    </footer>
    </div>
    <!-- /.container -->

  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</body>

<div class="modal fade" id="edit_ticket" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title"><?= $language['admin_support_action_text'] ?></h4>
	    </div>
	    <div class="modal-body">
	      <form method="post" action="">
	      	<div class="form-group">
	      		<label><?= $language['admin_support_select_ticket'] ?></label>
	      		<select name="edit_ticket_id" class="form-control">
	      			<?php 

	      			$req = $pdo->query('SELECT * FROM support_tickets');
	      			while($r = $req->fetch()){
	      				echo '<option value="'.$r->id.'">[ID: '.$r->id.'] '.$r->title.'</option>';
	      			}

	      			?>
	      		</select>
	        </div>

	        <div class="form-group">
	        	<label><?= $language['admin_support_select_state'] ?></label>
	        	<select name="edit_ticket_state" class="form-control">
	        		<option value="0"><?= $language['support_closed'] ?></option>
	        		<option value="1"><?= $language['support_answered'] ?></option>
	        		<option value="2"><?= $language['support_pending'] ?></option>
	        	</select>
	        </div>

	      	
	      	<button type="submit" class="btn btn-success"><?= $language['admin_support_confirm'] ?></button>
	      	</form>
	    </div>
	  </div>
	  
	</div>
</div>

<div class="modal fade" id="delete_ticket" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title"><?= $language['admin_support_delete']?></h4>
	    </div>
	    <div class="modal-body">
	      <form method="post" action="">
	      	<div class="form-group">
	      		<label><?= $language['admin_support_select_ticket']?></label>
	      		<select name="delete_ticket_id" class="form-control">
	      			<?php 

	      			$req = $pdo->query('SELECT * FROM support_tickets');
	      			while($r = $req->fetch()){
	      				echo '<option value="'.$r->id.'">[ID: '.$r->id.'] '.$r->title.'</option>';
	      			}

	      			?>
	      		</select>
	          </div>
	      	
	      	<button type="submit" class="btn btn-danger"><?= $language['admin_support_delete_confirm'] ?></button>
	      	</form>
	    </div>
	  </div>
	  
	</div>
</div>

 
</html>