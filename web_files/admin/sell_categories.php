<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once '../inc/functions.php';
require_once 'inc/db.php';
is_admin();

if(isset($_POST['add_category'])){

	$order = htmlentities($_POST['add_order']);
	$category = htmlentities($_POST['add_category']);
	$icon = htmlentities($_POST['add_icon']);

	$req = $pdo->prepare("INSERT INTO products_categories SET name = ?, icon = ?, order_id = ?");
	$req->execute([$category, $icon, $order]);

	$_SESSION['flash']['success'] = "Category added.";
	header("Location: sell_categories.php");
	exit();
}

if(isset($_POST['edit_id'])){
	$id = htmlentities($_POST['edit_id']);
	$order = htmlentities($_POST['edit_order']);
	$name = htmlentities($_POST['edit_category']);
	$icon = htmlentities($_POST['edit_icon']);

	$req = $pdo->prepare('UPDATE products_categories SET name = ?, order_id = ?, icon = ? WHERE id = ?');
	$req->execute([$name, $order, $icon, $id]);

	$_SESSION['flash']['success'] = "Category edited.";
	header("Location: sell_categories.php");
	exit();
}

if(isset($_GET['del'])){
	$id = htmlentities($_GET['del']);
	if(!is_numeric($id)){
		$_SESSION['flash']['danger'] = "Invalid ID";
		header("Location: sell_categories.php");
		exit();
	}

	$req = $pdo->prepare("SELECT * FROM products_categories WHERE id = ?");
	$req->execute([$id]);

	if($req->rowCount() == 0){
		$_SESSION['flash']['danger'] = "Invalid ID";
		header("Location: sell_categories.php");
		exit();
	}

	$req = $pdo->prepare("DELETE FROM products_categories WHERE id = ?");
	$req->execute([$id]);

	// now set all products in this category as null
	$req = $pdo->prepare("UPDATE products SET category_id = NULL where category_id = ?");
	$req->execute([$id]);

	$_SESSION['flash']['success'] = "Category deleted.";
	header("Location: sell_categories.php");
	exit();

}

require_once 'inc/header.php';

?>
<h1 class="heading-1">Store categories</h1>

<div class="row">
<button type="button" data-toggle="modal" data-target="#add" class="btn btn-success pull-right">Add a category</button>
	<h1>Categories List</h1>
	<table class="table table-responsive">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Icon</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		$req = $pdo->query("SELECT * FROM products_categories ORDER BY id ASC");
		while($row = $req->fetch()): ?>
			<tr>
				<td><?= $row->id ?></td>
				<td><?= $row->name ?></td>
				<td><?= $row->icon ?></td>
				<td><a href="?del=<?=$row->id?>">Delete</a> <a data-toggle="modal" data-target="#edit_<?=$row->id?>" href="">Edit</a></td>
			</tr>
		<?php endwhile; ?>
		</tbody>
	</table>

</div>

<?php include 'inc/footer.php'; ?>

<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Add a category</h5>
			</div>

			<div class="modal-body">
				<form method="post" action="">

					<div class="form-group">
						<label>Display Order</label>
						<input required type="number" class="form-control" name="add_order" value="0">
					</div>

					<div class="form-group">
						<label>Category Name</label>
						<input required type="text" class="form-control" name="add_category">
					</div>

					<hr />

					<div class="form-group">
						<label>Category Icon</label>
						<input required type="text" class="form-control" name="add_icon">
					</div>

					<hr />

					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						Close
					</button>

					<button type="submit" class="btn btn-primary">
						Add
					</button>
				</form>

				
			</div>
		</div>
	</div>
</div>


<?php
$req = $pdo->query("SELECT * FROM products_categories");
while($row = $req->fetch()) : ?>
	<div class="modal fade" id="edit_<?=$row->id?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Add a category</h5>
				</div>

				<div class="modal-body">
					<form method="post" action="">
						<input type="hidden" value="<?=$row->id?>" name="edit_id">

						<div class="form-group">
							<label>Display Order</label>
							<input required type="number" class="form-control" name="edit_order" value="<?=$row->order_id?>">
						</div>

						<div class="form-group">
							<label>Category Name</label>
							<input required type="text" class="form-control" name="edit_category" value="<?=$row->name?>">
						</div>

						<hr />

						<div class="form-group">
							<label>Category Icon</label>
							<input required type="text" class="form-control" name="edit_icon" value="<?=$row->icon?>">
						</div>

						<hr />

						<button type="button" class="btn btn-secondary" data-dismiss="modal">
							Close
						</button>

						<button type="submit" class="btn btn-primary">
							Edit
						</button>
					</form>

					
				</div>
			</div>
		</div>
	</div>
<?php endwhile; ?>