<?php
require '../inc/config.php';
require '../inc/functions.php';
require 'inc/functions.php';
require 'inc/db.php';
require '../inc/language.php';
is_admin();

if(isset($_POST['product_name'])){
	
	$product_category = htmlentities($_POST['product_category']);

	$product_name = htmlentities($_POST['product_name']);
	$product_order = htmlentities($_POST['product_order']);
	$product_url = htmlentities($_POST['product_url']);
	$product_description = htmlentities($_POST['product_description']);

	$product_price = htmlentities($_POST['product_price']);
	$product_currency = htmlentities($_POST['product_currency']);

	$product_image_url = htmlentities($_POST['product_image_url']);

	// Product name can't be empty
	// Product URL can't be empty
	// Price can't be empty
	// CUrrency can't be empty

	if(empty($product_price) OR empty($product_currency)){
		// Display error message
		$_SESSION['flash']['danger'] = "Product Price or Product Currency can't be empty.";
		header("Location: sell.php");
		exit();
	}

	if(empty($product_name) OR empty($product_url)){
		// Display error message
		$_SESSION['flash']['danger'] = "Product name or Product URL can't be empty.";
		header("Location: sell.php");
		exit();
	}

	$req = $pdo->prepare("INSERT INTO products SET product_name = :product_name, category_id = :category_id, product_order = :product_order, product_url = :product_url, product_description = :product_description, product_price = :product_price, product_currency = :product_currency, product_image_url = :product_image_url, date_added = CURRENT_TIMESTAMP");
	$req->bindParam(':product_order', $product_order);
	$req->bindParam(':category_id', $product_category);
	$req->bindParam(':product_name', $product_name);
	$req->bindParam(':product_url', $product_url);
	$req->bindParam(':product_description', $product_description);
	$req->bindParam(':product_price', $product_price);
	$req->bindParam(':product_currency', $product_currency);
	$req->bindParam(':product_image_url', $product_image_url);

	$req->execute();

	$_SESSION['flash']['success'] = "Successfully created the products.";
	header("Location: sell.php");
	exit();

}

if(isset($_POST['edit_product_name'])){
	
	$product_category = htmlentities($_POST['edit_product_category']);

	//die($product_category);
	$product_order = htmlentities($_POST['edit_product_order']);
	$product_name = htmlentities($_POST['edit_product_name']);
	$product_url = htmlentities($_POST['edit_product_url']);
	$product_description = nl2br(htmlentities($_POST['edit_product_description']));

	$product_price = htmlentities($_POST['edit_product_price']);
	$product_currency = htmlentities($_POST['edit_product_currency']);

	$product_image_url = htmlentities($_POST['edit_product_image_url']);
	$product_id = htmlentities($_POST['edit_product_id']);

	if($product_category == "NULL"){
		$product_category = NULL;
	}


	// Product name can't be empty
	// Product URL can't be empty
	// Price can't be empty
	// CUrrency can't be empty

	if(empty($product_price) OR empty($product_currency)){
		// Display error message
		$_SESSION['flash']['danger'] = "Product Price or Product Currency can't be empty.";
		header("Location: sell.php");
		exit();
	}

	if(empty($product_name) OR empty($product_url)){
		// Display error message
		$_SESSION['flash']['danger'] = "Product name or Product URL can't be empty.";
		header("Location: sell.php");
		exit();
	}

	$req = $pdo->prepare("UPDATE products SET product_order = :product_order, category_id = :category_id, product_name = :product_name, product_url = :product_url, product_description = :product_description, product_price = :product_price, product_currency = :product_currency, product_image_url = :product_image_url, date_added = date_added WHERE id = :id");
	$req->bindParam(':product_order', $product_order);

	if(is_null($product_category)){
		$req->bindParam(':category_id', $product_category = null, PDO::PARAM_INT);
	}else{
		$req->bindParam(':category_id', $product_category);
	}
	
	$req->bindParam(':product_name', $product_name);
	$req->bindParam(':product_url', $product_url);
	$req->bindParam(':product_description', $product_description);
	$req->bindParam(':product_price', $product_price);
	$req->bindParam(':product_currency', $product_currency);
	$req->bindParam(':product_image_url', $product_image_url);
	$req->bindParam(':id', $product_id);


	$req->execute();

	$_SESSION['flash']['success'] = "Successfully updated the products.";
	header("Location: sell.php");
	exit();

}

if(isset($_POST['delete_id'])){
	$id = htmlentities($_POST['delete_id']);
	// We need to check if this ID exist first
	$req = $pdo->prepare('SELECT * FROM products WHERE id = ?');
	$req->execute([$id]);
	
	if($req->rowCount() == 1){
		// ID EXIST
		$req = $pdo->prepare("DELETE FROM products WHERE id = ?");
		$req->execute([$id]);
		
		$_SESSION['flash']['success'] = "Successfully deleted this product.";
		header("Location: sell.php");
		exit();
	}
}

require_once 'inc/header.php';

?>
<div class="header">
<h1><?= $language['admin_product_page_title']?></h1>
<h2><?= $language['admin_product_recommend']?> <a href="http://selly.gg" target="_blank">selly.gg</a></h2>
</div>

<div class="col-md-12">
	<h3><?= $language['admin_product_your_product']?></h3>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>ID</th>
				<th>Order</th>
				<th><?= $language['admin_product_name'] ?></th>
				<th>Product Category</th>
				<th><?= $language['admin_product_url'] ?></th>
				<th><?= $language['admin_product_description'] ?></th>
				<th><?= $language['admin_product_price'] ?></th>
				<th><?= $language['admin_product_date_added'] ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$req = $pdo->prepare("SELECT * FROM products");
			$req->execute();

			while($row = $req->fetch()){
				if(!is_null($row->category_id)){
					$r = $pdo->query("SELECT * FROM products_categories WHERE id = '$row->category_id'");
					if($r->rowCount() == 0){
						$category_name = "None";
					}else{
						
						$category_name = $r->fetch()->name;
					}
				}else{
					$category_name = "None";
				}
				echo "
				<tr>
					<td>$row->id</td>
					<td>$row->product_order</td>
					<td>$row->product_name</td>
					<td>$category_name</td>
					<td>$row->product_url</td>
					<td>$row->product_description</td>
					<td>$row->product_price $row->product_currency</td>
					<td>$row->date_added</td>
					<td>
					<button type='submit'  data-toggle='modal' data-target='#edit_product_$row->id' class='btn btn-info'>Edit</button>
					<button type='submit'  data-toggle='modal' data-target='#delete_product_$row->id' class='btn btn-danger'>Delete</button></td>
				</tr>
";
			}

			?>
		</tbody>
	</table>
</div>  


<div class="col-md-12">
	<form method="post" action="">
		
		<div class="form-group">
			<label>Product Category</label>
			<select name="product_category" class="form-control">
			<?php
			$req = $pdo->query("SELECT * FROM products_categories ORDER BY id ASC");
			if($req->rowCount() == 0){
				echo "<option value='0'>No category available</option>";
			}else{
				echo "<option value='0'>None</option>";
				while($row = $req->fetch()) : ?>
				<option value="<?=$row->id ?>"><?=$row->name ?></option>

			<?php endwhile; }?>
			</select>
		</div>

		<div class="form-group">
			<label>Product Order</label>
			<input type="number" name="product_order" class="form-control" required>
		</div>

		<div class="form-group">
			<label><?= $language['admin_product_name'] ?></label>
			<input type="text" name="product_name" class="form-control" required>
		</div>

		<div class="form-group">
			<label><?= $language['admin_product_url'] ?></label>
			<input type="text" name="product_url" class="form-control" required>
		</div>

		<hr />

		<div class="form-group">
			<label><?= $language['admin_product_price'] ?></label>
			<input type="text" name="product_price" class="form-control" required>
		</div>
		<div class="form-group">
			<label><?= $language['admin_product_currency'] ?></label>
			<input type="text" placeholder="Ex: EUR" name="product_currency" class="form-control" required>
		</div>

		<hr />
		<div class="form-group">
			<label><?= $language['admin_product_image_url'] ?></label>
			<input type="text" name="product_image_url" class="form-control" placeholder="http://">
		</div>
		<hr />

		<div class="form-group">
			<label><?= $language['admin_product_description'] ?></label>
			<textarea name="product_description" rows="5" class="form-control"></textarea>
		</div>

		<hr />

		<button type="submit" class="btn btn-default"><?= $language['admin_product_submit'] ?></button>
		
	</form>
<hr />

</div>


<?php

$r = $pdo->query("SELECT * FROM products_categories");
$categories = $r->fetchAll();

$req = $pdo->prepare('SELECT * FROM products');
$req->execute();

while($row = $req->fetch()){ ?>


	<div class="modal fade" id="edit_product_<?=$row->id?>" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit product - <?= $row->product_name ?></h4>
        </div>
        <div class="modal-body">
          <form method="post" action="">
          	
          	<div class="form-group">
				<label>Product Category</label>
				<select name="edit_product_category" class="form-control">
				<?php
				$r = $pdo->query("SELECT * FROM products_categories ORDER BY id ASC");
				if($r->rowCount() == 0){
					echo "<option value='0'>No category available</option>";
				}else{
					foreach ($categories as $key => $value) {
						echo "<option value='$value->id'>$value->name</option>";
					}
					echo "<option value='0'>None</option>"; }?>
				</select>
			</div>
          	<div class="form-group">
          		<label>Product Order</label>
          		<input type="number" name="edit_product_order" class="form-control" value="<?= $row->product_order?>">
          	</div>

	        <div class="form-group">
				<label><?= $language['admin_product_name']?></label>
				<input type="text" name="edit_product_name" class="form-control" value="<?= $row->product_name?>" required>
			</div>

			<div class="form-group">
				<label><?= $language['admin_product_url']?></label>
				<input type="text" name="edit_product_url" class="form-control" value="<?= $row->product_url?>" required>
			</div>

			<hr />

			<div class="form-group">
				<label><?= $language['admin_product_price']?></label>
				<input type="text" name="edit_product_price" class="form-control" value="<?= $row->product_price?>" required>
			</div>
			<div class="form-group">
				<label><?= $language['admin_product_currency']?></label>
				<input type="text" placeholder="Ex: EUR" name="edit_product_currency" class="form-control" value="<?= $row->product_currency?>" required>
			</div>

			<hr />
			<div class="form-group">
				<label><?= $language['admin_product_image_url']?></label>
				<input type="text" name="edit_product_image_url" class="form-control" value="<?= $row->product_image_url?>" placeholder="http://">
			</div>
			<hr />

			<div class="form-group">
				<label><?= $language['admin_product_description']?></label>
				<textarea name="edit_product_description" rows="5" class="form-control"><?= $row->product_description?></textarea>
			</div>

			<input type="hidden" name="edit_product_id" value="<?=$row->id?>">

			<hr />


          	<button type="submit" class="btn btn-default">Submit</button>
          	</form>
        </div>
      </div>
      
    </div>
  </div>

<?php
}
?>

<?php

$req = $pdo->prepare('SELECT * FROM products');
$req->execute();
while($row = $req->fetch()){
	echo '

	<div class="modal fade" id="delete_product_'.$row->id.'" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete product - '.$row->product_name.'</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="">
			<input type="hidden" value="'.$row->id.'" name="delete_id">
			<p><b>Do you really want to delete '.$row->product_name.' ?</b></p>
			<button type="submit" class="btn btn-danger">Delete</button>
          </form>
        </div>
      </div>
      
    </div>
  </div>

	';
}
?>

<?php include('inc/footer.php');