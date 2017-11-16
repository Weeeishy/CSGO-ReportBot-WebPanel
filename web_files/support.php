<?php $page = "Support"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/db.php'; ?>
<?php require 'inc/language.php';
is_logged();

if(isset($_POST["title"])){
	$title = htmlentities(htmlspecialchars($_POST['title']));
	$message = htmlentities(htmlspecialchars($_POST['message']));
	$category = htmlentities(htmlspecialchars($_POST['category']));

	$uid = $_SESSION['auth']->id;

	echo $message; 

	$req = $pdo->prepare('SELECT * FROM support_categories WHERE id = ?');
	$req->execute([$category]);
	if($req->rowCount() == 0){
		// category doesnt exist
		$_SESSION['flash']['danger'] = $language['support_error_category'];
		header("Location: support.php");
		exit();
	}

	if(empty($message)){
		$_SESSION['flash']['danger'] = $language['support_error_empty_message'];
		header("Location: support.php");
		exit();
	}

	$req = $pdo->prepare('INSERT INTO support_tickets SET uid = :uid, title = :title, message = :message, type = :type, state = 2, date = CURRENT_TIMESTAMP');
	$req->bindParam(':uid', $uid);
	$req->bindParam(':title', $title);
	$req->bindParam(':message', $message);
	$req->bindParam(':type', $category);

	$req->execute();

	$_SESSION['flash']['success'] = $language['support_ticket_created'];;
	header("Location: support.php");
	exit();

}

require 'inc/header.php';
?>
<script src="js/jquery.min.js"></script>

<!-- Load WysiBB JS and Theme -->
<script src="js/jquery.wysibb.js"></script>
<link rel="stylesheet" href="css/wbbtheme.css" />
<script>
$(function() {
	var wbbOpt = {
		buttons: "bold,italic,underline,fontcolor,|,img,link",
		height: "500px",
	}
  $("#wysibb").wysibb(wbbOpt);
})
</script>
<h1><?= $language['support_center']; ?></h1>

<div class="panel panel-default">
    <div class="panel-heading"><?= $language['support_your_tickets']; ?></div>
    	<div class="panel-body">
    		<div class="list-group">
			  <?php
			  $req = $pdo->query('SELECT * FROM support_tickets WHERE uid = "' . $_SESSION['auth']->id . '" ORDER BY id ASC');
			  while($row = $req->fetch()){
			  	$q = $pdo->query('SELECT * FROM support_categories WHERE id = "'.$row->type.'"');
			  	while($get_type = $q->fetch()){
			  		$type = $get_type->category_name;
			  		$type_label = $get_type->label_type;	
			  	}

			  	if($row->state == 1){
			  		$state = '<span class="label label-danger">'.$language['support_answered'].'</span>';
			  	}elseif($row->state == 0){
			  		$state = '<span class="label label-info">'.$language['support_closed'].'</span>';
			  	}else{
			  		$state = '<span class="label label-warning">'.$language['support_pending'].'</span>';
			  	}

			  	if($row->user_viewed == 1){
			  		$user_viewed = '';
			  	}else{
			  		$user_viewed = '<i style="color: red;">'.$language['support_unviewed'].'</i>';
			  	}

			  	?>			  	
			  	
			  	<a href="viewticket.php?id=<?= $row->id ?>" class="list-group-item"><?= $state ?> <?= htmlspecialchars_decode($row->title) ?>  <?= $user_viewed ?><span class="pull-right label label-<?= $type_label ?>"><?= $type ?></span></a>
			  	<?php
			  }

			  ?>
			  
			  
			</div>
    	</div>
  </div>
<h2 class="heading"><?= $language['support_create_ticket'] ?></h2>
<form method="post" action="">
	<div class="form-group">
		<label><?= $language['support_category'] ?></label>
		<select class="form-control" name="category">
			<?php
			$req = $pdo->query('SELECT * FROM support_categories ORDER BY id ASC');
			while($row = $req->fetch()){
				echo "<option value='$row->id'>$row->category_name</option>";
			} ?>
		</select>
	</div>

	<hr />

	<div class="form-group">
		<label><?= $language['support_ticket_title'] ?></label>
		<input type="text" class="form-control" name="title" required>
	</div>
	
	<hr />

	<div class="form-group">
		<label><?= $language['support_ticket_message'] ?></label>
		<textarea id="wysibb" name="message" rows="5"></textarea>
	</div>

	<button type="submit" class="btn btn-primary"><?= $language['support_ticket_submit'] ?></button>
</form>	

<hr />

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