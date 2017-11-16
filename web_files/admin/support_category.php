<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/functions.php';
require_once 'inc/db.php';
require '../inc/language.php';
is_admin();

if(isset($_GET['del'])){
	$id = htmlentities($_GET['del']);
	if(is_numeric($id)){
		$req = $pdo->prepare('SELECT * FROM support_categories WHERE id = ?');
		$req->execute([$id]);
		if($req->rowCount() > 0){
			$r = $pdo->query('DELETE FROM support_categories WHERE id = "'.$id.'"');

			$_SESSION['flash']['success'] = $language['admin_category_deleted'];;
			header("Location: support_category.php");
			exit();
		}
	}
}

if(isset($_POST['category_name'])){
	$name = htmlentities($_POST['category_name']);
	$type = htmlentities($_POST['label_type']);

	if(!is_null($name)){
		if(!is_null($type)){
			$req = $pdo->prepare('INSERT INTO support_categories SET category_name = ?, label_type = ?');
			$req->execute([$name, $type]);

			$_SESSION['flash']['success'] = $language['admin_category_added'];;
			header("Location: support_category.php");
			exit();
		}
	}
}

if(isset($_POST['edit_id'])){
	$id = htmlentities($_POST['edit_id']);
	$name = htmlentities($_POST['edit_name']);
	$type = htmlentities($_POST['edit_label']);

	if(!is_null($id)){
		if(!is_null($name)){
			if(!is_null($type)){
				$req = $pdo->prepare('SELECT * FROM support_categories WHERE id = ?');
				$req->execute([$id]);

				if($req->rowCount() > 0){
					$r = $pdo->prepare('UPDATE support_categories SET category_name = ?, label_type = ? WHERE id = ?');
					$r->execute([$name, $type, $id]);

					$_SESSION['flash']['success'] = $language['admin_category_updated'];
					header("Location: support_category.php");
					exit();
				}
			}
		}
	}
}

require_once 'inc/header.php';
?>
<button class="btn btn-success pull-right" data-toggle="modal" data-target="#add_category"><?=$language['admin_category_add_title']?></button>

<h1><?=$language['admin_categories']?></h1>

<table class="table table-responsive table-bordered">
	<thead>
		<tr>
			<th>ID</th>
			<th><?=$language['admin_category_name']?></th>
			<th><?=$language['admin_category_label']?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$req = $pdo->query('SELECT * FROM support_categories ORDER BY id ASC');
		while($row = $req->fetch()){
			echo "<tr>
					<td>$row->id</td>
					<td>$row->category_name</td>
					<td><span class='label label-$row->label_type'>$row->category_name</span></td>
					<td><a href='?del=$row->id'>".$language['admin_category_delete']."</a> | <a data-toggle='modal' data-target='#edit_category_$row->id'>".$language['admin_category_edit']."</a></td>
				</tr>";
		}
		?>

	</tbody>
</table>

<div class="modal fade" id="add_category" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title"><?=$language['admin_category_add_title']?></h4>
	    </div>
	    <div class="modal-body">
	      <form method="post" action="">
	      	<div class="form-group">
	      		<label><?=$language['admin_category_name_form']?></label>
	      		<input type="text" class="form-control" name="category_name">
	        </div>

	        <div class="form-group">
	      		<label><?=$language['admin_category_label_form']?></label> <span class="label label-danger">danger</span><span class="label label-success">success</span><span class="label label-info">info</span><span class="label label-warning">warning</span>
	      		<select name="label_type" class="form-control">
	      			<option value="danger">Danger</option>
	      			<option value="success">Success</option>
	      			<option value="info">Info</option>
	      			<option value="warning">Warning</option>
	      		</select>
	        </div>
	      	
	      	<button type="submit" class="btn btn-success"><?=$language['admin_category_add']?></button>
	      	</form>
	    </div>
	  </div>
	  
	</div>
</div>

<?php

$req = $pdo->query('SELECT * FROM support_categories');
while($row = $req->fetch()){
	echo '

<div class="modal fade" id="edit_category_'.$row->id.'" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">'.$language['admin_category_edit'].' : '.$row->category_name.'</h4>
	    </div>
	    <div class="modal-body">
	      <form method="post" action="">
	      	<div class="form-group">
	      		<label>Category Name</label>
	      		<input type="text" name="edit_name" value="'.$row->category_name.'" class="form-control">
	        </div>

	        <div class="form-group">
	      		<label>'.$language['admin_category_label_form'].'</label>
	      		<select name="edit_label" class="form-control">
					';
					if($row->label_type == "danger"){
						echo '<option value="danger">Danger</option>
	      			<option value="success">Success</option>
	      			<option value="info">Info</option>
	      			<option value="warning">Warning</option>';
					}elseif($row->label_type == "success"){
						echo '<option value="success">Success</option>
	      			<option value="danger">Danger</option>
	      			<option value="info">Info</option>
	      			<option value="warning">Warning</option>';
					}elseif($row->label_type == "info"){
						echo '<option value="info">Info</option>
	      			<option value="danger">Danger</option>
	      			<option value="success">Success</option>
	      			<option value="warning">Warning</option>';
					}else{
						echo '<option value="warning">Warning</option>
	      			<option value="danger">Danger</option>
	      			<option value="success">Success</option>
	      			<option value="info">Info</option>';
					}
					echo '	      			
	      		</select>
	        </div>

	        <input type="hidden" value="'.$row->id.'" name="edit_id">
	      	
	      	<button type="submit" class="btn btn-info">'.$language['admin_category_update'].'</button>
	      	</form>
	    </div>
	  </div>
	  
	</div>
</div>';
}
?>
<?php include('inc/footer.php');