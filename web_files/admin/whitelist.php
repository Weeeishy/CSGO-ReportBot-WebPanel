<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once '../inc/functions.php';
require '../inc/language.php';
require_once 'inc/db.php';
is_admin();

if(isset($_POST['steamid'])){
	if(!empty($_POST['steamid'])){
		if(!empty($_POST['comment'])){
			if(ctype_digit($_POST['steamid'])){
				$steamid = htmlentities($_POST['steamid']);
				$comment = htmlentities($_POST['comment']);

				// We check if steamid is not in database
				$req = $pdo->prepare('SELECT * FROM whitelist WHERE steamid = ?');
				$req->execute([$steamid]);
				if($req->rowCount() == 0){
					$req = $pdo->prepare('INSERT INTO whitelist SET steamid = ?, comment = ?, added_date = CURRENT_TIMESTAMP');
					$req->execute([$steamid, $comment]);

					// OK
					$_SESSION['flash']['success'] = $language['admin_whitelist_successfully_whitelisted'];
					header("Location: whitelist.php");
					exit();
				}else{
					// SteamID is already whitelist
					$_SESSION['flash']['danger'] = $language['admin_whitelist_error_already_whitelist'];
					header("Location: whitelist.php");
					exit();
				}
			}else{
				//steamid is not a number
				$_SESSION['flash']['danger'] = $language['admin_whitelist_error_invalid_steamid'];
				header("Location: whitelist.php");
			exit();
			}
		}else{
			// empty comment
			$_SESSION['flash']['danger'] = $language['admin_whitelist_error_empty_comment'];
			header("Location: whitelist.php");
			exit();
		}
	}else{
		// empty steamid
		$_SESSION['flash']['danger'] = $language['admin_whitelist_error_empty_steamid'];
		header("Location: whitelist.php");
		exit();
	}
}

if(isset($_POST['delete_id'])){
	if(!empty($_POST['delete_id'])){
		//check if value exist first
		$req = $pdo->prepare('SELECT * FROM whitelist WHERE id = ?');
		$req->execute([$_POST['delete_id']]);
		if($req->rowCount() == 1){
			$req = $pdo->prepare('DELETE FROM whitelist WHERE id = ?');
			$req->execute([$_POST['delete_id']]);

			// OK
			$_SESSION['flash']['success'] = $language['admin_whitelist_successfully_deleted'];
			header("Location: whitelist.php");
			exit();
		}else{
			// doesnt exist
			$_SESSION['flash']['danger'] = $language['admin_whitelist_error_no_entry'];
			header("Location: whitelist.php");
			exit();
		}
	}else{
		// empty id
		$_SESSION['flash']['danger'] = $language['admin_whitelist_error_empty_id'];
		header("Location: whitelist.php");
		exit();
		
	}
}

if(isset($_POST['edit_comment'])){
	if(!empty($_POST['edit_comment'])){
		if(!empty($_POST['edit_id'])){
			//First we check if there is an entry for this ID
			$req = $pdo->prepare('SELECT * FROM whitelist WHERE id = ?');
			$req->execute([$_POST['edit_id']]);
			if($req->rowCount() == 1){
				$req = $pdo->prepare('UPDATE whitelist SET comment = ? WHERE id = ?');
				$req->execute([$_POST['edit_comment'], $_POST['edit_id']]);

				// OK
				$_SESSION['flash']['success'] = $language['admin_whitelist_successfully_edited'];
				header("Location: whitelist.php");
				exit();
			}else{
				// no entry
				$_SESSION['flash']['danger'] = $language['admin_whitelist_error_no_entry'];
			header("Location: whitelist.php");
			exit();
			}
		}else{
			// empty ID
			$_SESSION['flash']['danger'] = $language['admin_whitelist_error_empty_id'];
			header("Location: whitelist.php");
			exit();
		}
	}else{
		// empty comment
		$_SESSION['flash']['danger'] = $language['admin_whitelist_error_empty_comment'];
		header("Location: whitelist.php");
		exit();
	}
}

require_once 'inc/header.php';

?>

<h1><?= $language['admin_whitelist'] ?></h1>

<div class="col-md-7">
	<table class="table table-bordered">
		<thead>
			<tr>
				<th><?= $language['admin_whitelist_id'] ?></th>
				<th><?= $language['admin_whitelist_steamid'] ?></th>
				<th><?= $language['admin_whitelist_comment'] ?></th>
				<th><?= $language['admin_whitelist_date'] ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$req = $pdo->prepare('SELECT * FROM whitelist ORDER BY id ASC');
			$req->execute();
			while($row = $req->fetch()){
				echo '
				<tr>
					<td>'.$row->id.'</td>
					<td>'.$row->steamid.'</td>
					<td>'.$row->comment.'</td>
					<td>'.$row->added_date.'</td>
					<td><button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#edit_whitelist_'.$row->id.'">'.$language['admin_token_edit'].'</button><hr />
					<form action="" method="post">
					<input type="hidden" name="delete_id" value="'.$row->id.'">
					<button class="btn btn-sm btn-danger">'.$language['admin_whitelist_delete'].'</button>
					</form></td>
				</tr>';
			}

			?>
		</tbody>
	</table>
</div>

<div class="col-md-5">
<h4><?= $language['admin_whitelist_add_someone']?></h4>
	<form method="post" action="">
		<div class="form-group">
			<label><?= $language['admin_whitelist_steamid_to_whitelist'] ?></label>
			<input type="text" class="form-control" name="steamid" required>
		</div>

		<div class="form-group">
			<label><?= $language['admin_whitelist_comment'] ?></label>
			<input type="text" class="form-control" name="comment" required>
		</div>

		<hr />

		<button class="btn btn-default" type="submit"><?= $language['admin_whitelist_add'] ?></button>
	</form>
</div>

<?php

$req = $pdo->prepare('SELECT * FROM whitelist');
$req->execute();
while($row = $req->fetch()){

	echo '
	<div class="modal fade" id="edit_whitelist_'.$row->id.'" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">'.$language['admin_whitelist_edit'].' ' .$row->steamid.'</h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="">

          <div class="form-group">
          		<label>'.$language['admin_whitelist_comment'].'</label>
          		<input type="text" class="form-control" name="edit_comment" value="'.$row->comment.'" required>
          	</div>
          	          	
          	<hr />
          	<input type="hidden" name="edit_id" value="'.$row->id.'">
          	<button type="submit" class="btn btn-info">'.$language['admin_whitelist_edit'].'</button>
          	</form>
        </div>
      </div>
      
    </div>
  </div>

	';
}
?>

<?php include 'inc/footer.php'; ?>