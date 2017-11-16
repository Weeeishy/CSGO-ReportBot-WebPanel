<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/functions.php';
require '../inc/language.php';
require_once 'inc/db.php';
is_admin(); 

if(isset($_POST['homepage_title'])){
	// Its an edit
	// First, we check the ID
	if(isset($_POST['homepage_id'])){
		// Update
		$req = $pdo->prepare('UPDATE homepage SET title = :title, `text` = :text, icon = :icon WHERE id = :id');
		$req->bindParam(':title', htmlentities($_POST['homepage_title']));
		$req->bindParam(':text', htmlentities($_POST['homepage_text']));
		$req->bindParam(':icon', htmlentities($_POST['homepage_icon']));
		$req->bindParam(':id', htmlentities($_POST['homepage_id']));
		$req->execute();

		echo 'edited';
		header("Location: home.php");
		exit();
		
	}else{
		// no id
	}

}

if(isset($_POST['homepage_title_create'])){
	// Creation of a block
	// First, we need to check how many blocks exists
	$req = $pdo->prepare('SELECT * FROM homepage');
	$req->execute();
	if($req->rowCount() < 4){
		// There is less than 4 blocks
		if(!empty($_POST['homepage_title_create'])){
			if(!empty($_POST['homepage_text_create'])){
				if(!empty($_POST['homepage_icon_create'])){
					// Everything is fine, lets make the request
					$req = $pdo->prepare('INSERT INTO homepage SET title = :title, `text` = :text, icon = :icon');
					$req->bindParam(':title', htmlentities($_POST['homepage_title_create']));
					$req->bindParam(':text', htmlentities($_POST['homepage_text_create']));
					$req->bindParam(':icon', htmlentities($_POST['homepage_icon_create']));
					$req->execute();
					$_SESSION['flash']['success'] = $language['admin_homepage_success'];
					header("Location: home.php");
					exit();

				}else{
					// homepage_icon_create is empty
					$_SESSION['flash']['danger'] = $language['admin_homepage_empty_icon'];
					header("Location: home.php");
					exit();
				}
			}else{
				// homepage_text_create is empty
				$_SESSION['flash']['danger'] = $language['admin_homepage_empty_text'];
				header("Location: home.php");
				exit();
			}
		}else{
			//homepage_title_create is empty
			$_SESSION['flash']['danger'] = $language['admin_homepage_empty_title'];
			header("Location: home.php");
			exit();
		}

	}else{
		// There is already 4 blocks
		$_SESSION['flash']['danger'] = $language['admin_homepage_4blocks'];
		header("Location: home.php");
		exit();
	}
}

if(isset($_POST['homepage_delete'])){
	// Check if homepage exist
	$req = $pdo->prepare('SELECT * FROM homepage WHERE id = ?');
	$req->execute([htmlentities($_POST['homepage_delete'])]);
	if($req->rowCount() == 1){
		// exist
		$req = $pdo->prepare('DELETE FROM homepage WHERE id = ?');
		$req->execute([htmlentities($_POST['homepage_delete'])]);

		// Now we'll check if there is an entry left
		$req = $pdo->prepare('SELECT * FROM homepage');
		$req->execute();
		if($req->rowCount == 0){
			$req = $pdo->query('ALTER TABLE homepage AUTO_INCREMENT = 1');
			$req->execute();
		}
		header("Location: home.php");
		exit();

	}else{
		// Doesnt exist
	}

}

require_once 'inc/header.php';

?>

<h1><?= $language['admin_homepage_editor']; ?> <a href="http://fontawesome.io/icons/" target="_blank" style="font-size: 20px;"><?= $language['admin_homepage_icon_list']; ?></a></h4>
<div class="col-md-12">
	<?php
	$req = $pdo->prepare("SELECT * FROM homepage");
	$req->execute();
	$blocks = $req->rowCount();
	$blocks_div = $req->rowCount();
	$req = $pdo->prepare('SELECT * FROM homepage');
	$req->execute();
	while($row = $req->fetch()){
			echo '
			<div class="col-md-3">
				<form method="POST" action="">
					<div class="form-group">
						<label>'.$language['admin_homepage_title'] .'</label>
						<input required type="text" name="homepage_title" class="form-control" value="'.$row->title.'">
					</div>

					<div class="form-group">
						<label>'.$language['admin_homepage_text'] .'</label>
						<textarea required type="text" name="homepage_text" class="form-control" rows="7">'.$row->text.'</textarea>
					</div>

					<div class="form-group">
						<label>'.$language['admin_homepage_icon'] .'</label>
						<input required type="text" name="homepage_icon" class="form-control" value="'.$row->icon.'">
					</div>
					<input type="hidden" value="'.$row->id.'" name="homepage_id">
					<center><button class="btn btn-default" name="homepage_edit" value="1" type="submit">'.$language['admin_homepage_save'] .'</button>
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#delete_homepage_'.$row->id.'">
'.$language['admin_homepage_delete'] .'</button></center>
				</form>
					
			</div>';	
	}

	for($i = 1; $i <= 4 - $blocks; $i++){
		echo '
			<div class="col-md-3">
				<form method="POST" action="">
					<div class="form-group">
						<label>Title</label>
						<input required type="text" name="homepage_title_create" class="form-control" value="">
					</div>

					<div class="form-group">
						<label>Text</label>
						<textarea get_required_files()type="text" name="homepage_text_create" class="form-control" rows="7"></textarea>
					</div>

					<div class="form-group">
						<label>Icon</label>
						<input get_required_files()type="text" name="homepage_icon_create" class="form-control" value="">
					</div>

					<center><button class="btn btn-default" type="submit">Create</button></center>
				</form>
			</div>';	
	}

	echo '<div class="col-md-12"><hr /></div>';
	?>
</div>

<div class="col-lg-12">
<iframe src="../index.php" width="100%" height="600" frameBorder="0" scrolling="auto"></iframe>
</div>

<?php
$req = $pdo->prepare('SELECT * FROM homepage');
$req->execute();
while($row = $req->fetch()){
	echo '

	<div class="modal fade" id="delete_homepage_'.$row->id.'" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">'.$language['admin_token_edit'].' ' .$row->title.'</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="">
          	<div class="form-group">
          		<center><label>'.$language['admin_homepage_delete_confirm'].'</label></center>
          		<input type="hidden" name="homepage_delete" value="'.$row->id.'">
          	</div>
          	<center><button type="submit" class="btn btn-default">'.$language['admin_homepage_delete'].'</button></center>
          	</form>
        </div>
      </div>
      
    </div>
  </div>

	';
}
?>

<?php require 'inc/footer.php'; ?>