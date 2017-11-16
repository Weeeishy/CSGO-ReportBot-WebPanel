<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once '../inc/functions.php';
require_once 'inc/db.php';
is_admin();

require_once "../inc/jbbcode/Parser.php";

$parser = new JBBCode\Parser();
$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());

if(isset($_POST['title'])){

	require_once "../inc/jbbcode/Parser.php";

	$parser = new JBBCode\Parser();
	$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());

	$title = $_POST['title'];
	$message = nl2br(htmlentities(htmlspecialchars($_POST['message'])));

	$req = $pdo->prepare("INSERT INTO news SET title = ?, message = ?, addedby_uid = ?");
	$req->execute([$title, $message, $_SESSION['auth']->id]);

	$_SESSION['flash']['success'] = "News added";
	header("Location: news.php");
	exit();
}

if(isset($_GET['del'])){
	$id = $_GET['del'];
	if(is_numeric($id)){

		$req = $pdo->prepare("DELETE FROM news WHERE id = ?");
		$req->execute([$id]);

		if($req->rowCount() == 1){
			$_SESSION['flash']['success'] = "News deleted.";
			header("Location: news.php");
			exit();
	
		}else{
			$_SESSION['flash']['danger'] = "News doesnt exist.";
			header("Location: news.php");
			exit();
	
		}

		$_SESSION['flash']['danger'] = "News deleted.";
		header("Location: news.php");
		exit();
	}
}

if(isset($_POST['edit_id'])){
	$title = htmlspecialchars(htmlentities($_POST['edit_title']));
	$message = nl2br(htmlspecialchars(htmlentities($_POST['edit_message'])));
	$id = $_POST['edit_id'];

	//die($message);


	$req = $pdo->prepare("UPDATE news SET title = ?, message = ?, date = date WHERE id = ?");
	$req->execute([$title, $message, $id]);
	
	$_SESSION['flash']['success'] = "News updated.";
	header("Location: news.php");
	exit();

}

require_once 'inc/header.php';

?>

<script src="../js/jquery.min.js"></script>

<!-- Load WysiBB JS and Theme -->
<script src="../js/jquery.wysibb.js"></script>
<link rel="stylesheet" href="../css/wbbtheme.css" />

<script>
$(function() {
	var wbbOpt = {
		buttons: "bold,italic,underline,fontcolor,|,img,link"
	}
  $("#wysibb").wysibb(wbbOpt);
})

</script>

<h1>News</h1>
<div class="row">
	<div class="col-md-6">
		<h2>Add a new</h2>
		<form method="post">
			<div class="form-group">
				<label>News title</label>
				<input type="text" name="title" class="form-control">
			</div>

			<div class="form-group">
				<textarea id="wysibb" name="message" rows="5"></textarea>
			</div>

			<button type="submit" class="btn btn-primary">Add</button>
		</form>
	</div>

	<div class="col-md-6">
		<h2>News list</h2>
		<table class="table table-bordered table-responsive">
			<thead>
				<tr>
					<th>ID</th>
					<th>Title</th>
					<th>Message</th>
					<th>Date</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<tr>
		<?php
		

		$req = $pdo->query("SELECT * FROM news ORDER BY id ASC");
		while ($row = $req->fetch()) : ?>
		<?php
		$parser->parse($row->message);
        $message = html_entity_decode($parser->getAsHtml());
		?>
					<td><?= $row->id ?></td>
					<td><?= $row->title ?></td>
					<td><?= $message ?></td>
					<td><?= $row->date ?></td>
					<td><a href="?del=<?= $row->id ?>"><button class="btn btn-xs btn-danger">Delete</button></a> <button type="button" data-toggle="modal" data-target="#edit_<?=$row->id?>" class="btn btn-xs btn-info">Edit</button></td>
				</tr>
		<?php endwhile; ?>
			</tbody>
		</table>
	</div>

</div>

<?php
$req = $pdo->query("SELECT * FROM news");
while($row = $req->fetch()): ?>
<script>
$(function() {
	var wbbOpt = {
		buttons: "bold,italic,underline,fontcolor,|,img,link"
	}
  $("#wysibb_<?=$row->id?>").wysibb(wbbOpt);
})
</script>
<div class="modal fade" id="edit_<?=$row->id?>" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Edit news - <?=$row->title?></h4>
	    </div>
	    <div class="modal-body">
	      <form method="post" action="">
	      	<div class="form-group">
	      		<label>Edit title</label>
	      		<input type="text" name="edit_title" class="form-control" value="<?=$row->title?>">
	        </div>

	        <div class="form-group">
	        	<textarea id="wysibb_<?=$row->id?>" name="edit_message" rows="5"><?php
	        	$message =  html_entity_decode($row->message);
	        	$message = str_replace("<br />", "", $message);
	        	
	        	echo $message; ?></textarea>
	        </div>

	        <input type="hidden" value="<?=$row->id?>" name="edit_id">
	      	
	      	<button type="submit" class="btn btn-success">Edit</button>
	      	</form>
	    </div>
	  </div>
	  
	</div>
</div>

<?php endwhile; ?>

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
 
</html>