<?php 
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/functions.php';
require_once 'inc/db.php';
require '../inc/language.php';
is_admin();

if(isset($_POST['tab_name'])){
	//create a new tab
	$tab_order = htmlentities($_POST['tab_order']);
	$tab_name = htmlentities($_POST['tab_name']);
	$tab_link = htmlentities($_POST['tab_link']);
	$tab_type = htmlentities($_POST['tab_type']);
	$tab_parent = htmlentities($_POST['tab_parent']);
	$access_level = htmlentities($_POST['access_level']);

	/* optional */
	$tab_color = htmlentities($_POST['tab_color']);

	// ------------------------------ //

	// We need to check if tab type is correct
	$valid_type = array("item", "dropdown_parent");
	if(!in_array($tab_type, $valid_type)){
		$_SESSION['flash']['danger'] = "Invalid tab type.";
		header("Location: nav.php");
		exit();
	}

	// tab type = correct

	// ------------------------------ //
	// We need to check if tab parent is correct
	$valid_parent = array("none");
	$req = $pdo->query("SELECT * FROM navbar WHERE type='dropdown_parent'");
	while($row = $req->fetch()){
		array_push($valid_parent, $row->id);
	}


	if(!in_array($tab_parent, $valid_parent)){
		$_SESSION['flash']['danger'] = "Invalid tab parent.";
		header("Location: nav.php");
		exit();
	}
	if($tab_parent == "none"){
		$tab_parent = "0";
	}
	
	// tab parent = correct
	
	// ------------------------------ //
	// We need to check tab color
	$valid_color = array("white", "red", "green", "blue", "yellow", "orange");
	if(!in_array($tab_color, $valid_color)){
		$_SESSION['flash']['danger'] = "Invalid tab color.";
		header("Location: nav.php");
		exit();
	}

	// tab color = correct
	
	// ------------------------------ //
	//We need to check tab access level

	$valid_access_level = array("all", "logged", "non_logged", "admin");
	if(!in_array($access_level, $valid_access_level)){
		$_SESSION['flash']['danger'] = "Invalid tab access level.";
		header("Location: nav.php");
		exit();
	}

	// tab access level = correct

	$req = $pdo->prepare("INSERT INTO navbar SET parentid = :parentid, display_order = :display_order, type = :type, name = :name, link = :link, text_color = :text_color, access_level = :access_level");
	$req->bindParam(":parentid", $tab_parent);
	$req->bindParam(":display_order", $tab_order);
	$req->bindParam(":type", $tab_type);
	$req->bindParam(":name", $tab_name);
	$req->bindParam(":link", $tab_link);
	$req->bindParam(":text_color", $tab_color);
	$req->bindParam(":access_level", $access_level);

	$req->execute();

	$_SESSION['flash']['success'] = "Tab added.";
	header("Location: nav.php");
	exit();
}

if(isset($_GET['del'])){
	//delete
	$id = htmlentities($_GET['del']);
	// We need to check if this is a parent first
	$req = $pdo->prepare("SELECT * FROM navbar WHERE id = ?");
	$req->execute([$id]);

	if($req->rowCount() == 1){
		// valid ID
		while($row = $req->fetch()){
			$type = $row->type;
			$name = $row->name;


			switch($type){
				case "dropdown_parent":
					$pdo->query("UPDATE navbar SET type = 'item', parentid = 0 WHERE parentid = '$id'");
					$pdo->query("DELETE FROM navbar WHERE id = '$id'");
					$_SESSION['flash']['warning'] = "$name has been deleted. Every elements from this dropdown are now single items.";
					header("Location: nav.php");
					exit();
					break;

				case "item" :
					$pdo->query("DELETE FROM navbar WHERE id = '$id'");
					$_SESSION['flash']['warning'] = "$name has been deleted.";
					header("Location: nav.php");
					exit();
					break;
			}
		}
	}else{
		$_SESSION['flash']['danger'] = "Invalid ID.";
		header("Location: nav.php");
		exit();
	}
}

if(isset($_POST['edit_tab_id'])){
	$id = htmlentities($_POST['edit_tab_id']);
	$tab_name = htmlentities($_POST['edit_tab_name']);
	$tab_link = htmlentities($_POST['edit_tab_link']);
	$tab_order = htmlentities($_POST['edit_tab_order']);
	$tab_type = htmlentities($_POST['edit_tab_type']);
	$tab_parent = htmlentities($_POST['edit_tab_parent']);
	$access_level = htmlentities($_POST['edit_access_level']);
	$tab_color = htmlentities($_POST['edit_tab_color']);

	// we first need to check if ID is valid
	if(is_numeric($id)){
		$req = $pdo->prepare("SELECT * FROM navbar WHERE id = ?");
		$req->execute([$id]);
		if($req->rowCount() == 0){
			// ID DOesnt exist
			$_SESSION['flash']['danger'] = "ID Doesnt exist.";
			header("Location: nav.php");
			exit();
		}
	}else{
		// Invalid ID
		$_SESSION['flash']['danger'] = "Invalid ID.";
		header("Location: nav.php");
		exit();
	}

	// We need to check if tab type is correct
	$valid_type = array("item", "dropdown_parent");
	if(!in_array($tab_type, $valid_type)){
		$_SESSION['flash']['danger'] = "Invalid tab type.";
		header("Location: nav.php");
		exit();
	}

	// tab type = correct

	// ------------------------------ //
	// We need to check if tab parent is correct
	$valid_parent = array("none");
	$req = $pdo->query("SELECT * FROM navbar WHERE type='dropdown_parent'");
	while($row = $req->fetch()){
		array_push($valid_parent, $row->id);
	}


	if(!in_array($tab_parent, $valid_parent)){
		$_SESSION['flash']['danger'] = "Invalid tab parent.";
		header("Location: nav.php");
		exit();
	}
	if($tab_parent == "none"){
		$tab_parent = "0";
	}
	
	// tab parent = correct
	
	// ------------------------------ //
	// We need to check tab color
	$valid_color = array("white", "red", "green", "blue", "yellow", "orange");
	if(!in_array($tab_color, $valid_color)){
		$_SESSION['flash']['danger'] = "Invalid tab color.";
		header("Location: nav.php");
		exit();
	}

	// tab color = correct
	
	// ------------------------------ //
	//We need to check tab access level

	$valid_access_level = array("all", "logged", "non_logged", "admin");
	if(!in_array($access_level, $valid_access_level)){
		$_SESSION['flash']['danger'] = "Invalid tab access level.";
		header("Location: nav.php");
		exit();
	}

	// tab access level = correct

	$req = $pdo->prepare("UPDATE navbar SET parentid = :parentid, display_order = :display_order, type = :type, name = :name, link = :link, text_color = :text_color, access_level = :access_level WHERE id = :id ");
	$req->bindParam(":parentid", $tab_parent);
	$req->bindParam(":display_order", $tab_order);
	$req->bindParam(":type", $tab_type);
	$req->bindParam(":name", $tab_name);
	$req->bindParam(":link", $tab_link);
	$req->bindParam(":text_color", $tab_color);
	$req->bindParam(":access_level", $access_level);
	$req->bindParam(":id", $id);

	$req->execute();

	$_SESSION['flash']['success'] = "Tab edited.";
	header("Location: nav.php");
	exit();

}

require_once 'inc/header.php';
?>
<div class="row">
	<h1><?=$language['admin_nav_editor'] ?></h1>

	<button type="button" data-toggle="modal" data-target="#add_tab" class="btn btn-success"><?=$language['admin_nav_add_tab']?></button>

	<hr />

	<div class="row">
		<table class="table table-bordered table-responsive">
			<thead>
				<tr>
					<th>ID</th>
					<th><?= $language['admin_nav_name']?></th>
					<th><?= $language['admin_nav_parent_id']?></th>
					<th><?= $language['admin_nav_display_id']?></th>
					<th><?= $language['admin_nav_type']?></th>
					<th><?= $language['admin_nav_link']?></th>
					<th><?= $language['admin_nav_accesslevel']?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$req = $pdo->query("SELECT * FROM navbar ORDER BY id ASC");
			while($row = $req->fetch()) : ?>
			<?php
			switch($row->access_level){
				case "admin":
					$access_level = "<center><span class='label label-danger'>Admin</span></center>";
					break;

				case "all" :
					$access_level = "<center><span class='label label-primary'>Everyone</span></center>";
					break;

				case "non_logged" :
					$access_level = "<center><span class='label label-warning'>Only non logged</span></center>";
					break;

				case "logged" :
					$access_level = "<center><span class='label label-info'>Only Logged</span></center>";
					break;

				default :
					$access_level = "Unknown";
					break;
			}

			switch($row->type){
				case "item":
					if($row->parentid == "0" OR is_null($row->parentid)){
						$type = "<b><font color='green'>Item</font></b>";
						break;
					}else{
						$type = "<i>Dropdown Item</i>";
						break;
					}
					

				case "dropdown_parent":
					$type = "<u>Dropdown</u>";
					break;
			}

			switch($row->parentid){
				case "0":
					$parentid = "<i>None</i>";
					break;

				case NULL:
					$parentid = "<i>None</i>";
					break;

				case $row->parentid > 0:
					$r = $pdo->query("SELECT * FROM navbar WHERE id = '$row->parentid'");
					$parentid = "<b>" . $r->fetch()->name . "</b>";
					break;

			}

			if($row->text_color == "white"){
				$color = "black";
			}else{
				$color = $row->text_color;
			}
			?>
				<tr>
					<td><?= $row->id ?></td>
					<td><font color="<?=$color ?>"><?= $row->name ?></font></td>
					<td><?= $parentid ?></td>
					<td><?= $row->display_order ?></td>
					<td><?= $type ?></td>
					<td><?= $row->link ?></td>
					<td><?= $access_level ?></td>
					<td><button type="button" class="btn btn-xs btn-warning" data-toggle="modal" data-target="#edit_<?=$row->id?>">Edit</button></td>
				</tr>
			<?php endwhile; ?>
			</tbody>
		</table>
	</div>
</div>

<?php include 'inc/footer.php'; ?>

<div class="modal fade " id="add_tab" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Add a new tab</h4>
	    </div>
	    <div class="modal-body">
	    	<form method="post">
	    		<div class="form-group">
	    			<label>Tab Name</label>
	    			<input type="text" name="tab_name" class="form-control">
	    		</div>

	    		<div class="form-group">
	    			<label>Tab Link</label>
	    			<input type="text" name="tab_link" class="form-control">
	    		</div>

	    		<div class="form-group">
	    			<label>Tab Display Order</label>
	    			<input type="number" name="tab_order" class="form-control">
	    		</div>

	    		<div class="form-group">
	    			<label>Tab Type</label>
	    			<select name="tab_type" class="form-control">
	    				<option value="item">Item</option>
	    				<option value="dropdown_parent">Dropdown Parent</option>
	    			</select>
	    		</div>

				<div class="form-group">
	    			<label>Select a parent</label>
	    			<select name="tab_parent" class="form-control">
	    			<?php
	    			$req = $pdo->query("SELECT * FROM navbar WHERE type = 'dropdown_parent'");
	    			$count = $req->rowCount();
	    			if($count == 0){
	    				echo "<option value='none'>None</option>";
	    			}else{
	    				echo "<option value='none'>None</option>";
	    				while($row = $req->fetch()){
	    					echo "<option value='$row->id'>$row->name</option>";
	    				}
	    			} ?>
	    			</select>
	    		</div>

	    		<div class="form-group">
	    			<label>Access Level</label>
	    			<select name="access_level" class="form-control">
	    				<option value="all">Everyone</option>
	    				<option value="non_logged">Non Logged Only</option>
	    				<option value="logged">Logged Only</option>
	    				<option value="admin">Admin Only</option>
	    			</select>
	    		</div>

	    		<hr />

	    		<div class="form-group">
	    			<i>OPTIONAL - Tab text color</i>
	    			<select name="tab_color" class="form-control">
	    				<option value="white">Default</option>
	    				<option value="red">Red</option>
	    				<option value="green">Green</option>
	    				<option value="blue">Blue</option>
	    				<option value="yellow">Yellow</option>
	    				<option value="orange">Orange</option>
	    			</select>
	    		</div>

	    		<button type="sumbit" class="btn btn-success">Create</button>
	    	</form>
	    </div>
	  </div>
	  
	</div>
</div>


<?php
$req = $pdo->query("SELECT * FROM navbar");
while($row = $req->fetch()) : ?>
<div class="modal fade " id="edit_<?=$row->id?>" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title">Edit: <?=$row->name ?></h4>
	    </div>
	    <div class="modal-body">
	    	<form method="post">
	    	<input type="hidden" name="edit_tab_id" value="<?=$row->id?>">
	    		<div class="form-group">
	    			<label>Tab Name</label>
	    			<input type="text" name="edit_tab_name" class="form-control" value="<?=$row->name?>">
	    		</div>

	    		<div class="form-group">
	    			<label>Tab Link</label>
	    			<input type="text" name="edit_tab_link" value="<?=$row->link?>" class="form-control">
	    		</div>

	    		<div class="form-group">
	    			<label>Tab Display Order</label>
	    			<input type="number" name="edit_tab_order" value="<?=$row->display_order?>" class="form-control">
	    		</div>

	    		<div class="form-group">
	    			<label>Tab Type</label>
	    			<select name="edit_tab_type" class="form-control">
	    				<option value="item">Item</option>
	    				<option value="dropdown_parent">Dropdown Parent</option>
	    			</select>
	    		</div>

				<div class="form-group">
	    			<label>Select a parent <?=$row->parentid ?></label>
	    			<select name="edit_tab_parent" class="form-control">
	    			<?php
	    			$parent = array("0", NULL);
	    			if(in_array($row->parentid, $parent)){
	    				// no parents
	    				echo "<option value='none'>None</option>";
	    				$r = $pdo->query("SELECT * FROM navbar WHERE type = 'dropdown_parent'");
	    				while($g = $r->fetch()){
	    					echo "<option value='$g->id'>$g->name</option>";
	    				}
	    			}else{
	    				// have parent
	    				$r = $pdo->query("SELECT * FROM navbar WHERE id = '$row->parentid'");
	    				while($g = $r->fetch()){
	    					echo "<option value='$g->id'>$g->name</option>";
	    				}

	    				$r = $pdo->query("SELECT * FROM navbar WHERE type = 'dropdown_parent'");
	    				while($g = $r->fetch()){
	    					echo "<option value='$g->id'>$g->name</option>";
	    				}

	    				echo "<option value='none'>None</option>";
	    			}
	    			
	    			?>
	    			</select>
	    		</div>

	    		<div class="form-group">
	    			<label>Access Level</label>
	    			<select name="edit_access_level" class="form-control">
	    				<option value="all">Everyone</option>
	    				<option value="non_logged">Non Logged Only</option>
	    				<option value="logged">Logged Only</option>
	    				<option value="admin">Admin Only</option>
	    			</select>
	    		</div>

	    		<hr />

	    		<div class="form-group">
	    			<i>OPTIONAL - Tab text color</i>
	    			<select name="edit_tab_color" class="form-control">
	    				<option value="white">Default</option>
	    				<option value="red">Red</option>
	    				<option value="green">Green</option>
	    				<option value="blue">Blue</option>
	    				<option value="yellow">Yellow</option>
	    				<option value="orange">Orange</option>
	    			</select>
	    		</div>

	    		<div class="modal-footer">
	    			<button type="submit" class="btn btn-success">Edit</button>
	    			<a href="?del=<?=$row->id?>"><button type="button" class="btn btn-danger">Delete</button></a>
	    		</div>
	    	</form>
	    	
	    </div>
	  </div>
	  
	</div>
</div>

<?php endwhile; ?>