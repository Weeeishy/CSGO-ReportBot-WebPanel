<?php $page = "Products"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/header.php'; ?>
<?php require 'inc/db.php'; ?>
<?php require 'inc/language.php'; ?>
<script src="https://embed.selly.gg"></script>
<?php

if(isset($_GET['category'])){
    $id = htmlentities($_GET['category']);
    
    // check if valid id
    if(!is_numeric($id)){
        $_SESSION['flash']['danger'] = "Invalid category.";
        header("Location: products.php");
        exit();
    }
    echo "<a href='products.php'><button class='btn btn-warning'>Go back</button></a>";
    // now, check if id exit
    $req = $pdo->prepare("SELECT * FROM products_categories WHERE id = ?");
    $req->execute([$id]);
    if($req->rowCount() == 0){
        // id doesnt exist
        $_SESSION['flash']['danger'] = "Invalid category.";
        header("Location: products.php");
        exit();
    }

    $category_name = $req->fetch()->name;
    //now, we need to get products of this category
    ?>
        <div class="row">
            <h1>Products - <?= $category_name ?></h1>
            <div class="col-md-12">
             <div id="products" class="row list-group">
                    <?php
                    $req = $pdo->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY product_order ASC");
                    $req->execute([$id]);

                    if($req->rowCount() == 0){
                        echo "<h2>".$language['product_nothing_available']."</h2>";
                    }else{
                        while ($row = $req->fetch()) {
                            if(empty($row->product_image_url)){
                                $image = 0;
                                $image_url = "img/empty_icon.png";
                            }else{
                                $image = 1;
                                $image_url = $row->product_image_url;
                            }

                            $link = $row->product_url;
                            $link_id = explode('selly.gg/p/', $link);
                            $link_id = $link_id[1];
                            $link_id = str_replace('/','',$link_id);
                            echo "
                            <div class='item  col-md-4'>
                            <div class='thumbnail'>
                            <img class='group list-group-image' style='object-fit: cover;max-width: 100px;max-height: 100px;' width='100%' height='100%' src='$image_url' alt='' />
                                <div class='caption'>
                                    <b class='group inner list-group-item-heading'>
                                        $row->product_name</b>
                                        <br />
                                    <p class='group inner list-group-item-text'>
                                        $row->product_description</p>
                                    <div class='row'>
                                        <div class='col-xs-12 col-md-12'>
                                            <hr />
                                            <p class='lead'>$row->product_price $row->product_currency</p>  
                                        </div>
                                        <div class='col-xs-12 col-md-12'>
                                            <hr />
                                            <button class='btn btn-success' data-selly-product='".$link_id."'>".$language['product_purchase']."</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php

    include 'inc/footer.php';
    die();
} 
?>
<div class="row">
<?php
$req = $pdo->query("SELECT * FROM products_categories ORDER BY order_id ASC");
$count = $req->rowCount();
$col_md = 12 / 4;
$col_xs = 12 / 2;

if($count > 0 ){
    echo "<h1>Categories</h1>";
}
while($row = $req->fetch()) : ?>
<a href="?category=<?=$row->id?>" style="text-decoration: none;">

<div class="<?= "col-md-$col_md col-xs-$col_xs" ?>">
    <div class="panel panel-default">
        <div class="panel-heading"><center><i class="fa <?=$row->icon?> fa-3x" aria-hidden="true"></i></center></div>
        <div class="panel-body"><center><h4 class="display-4"><?=$row->name?></h4></center></div>
      </div>
</div>

</a>

<?php endwhile; ?>
</div>

<div class="row">
    <h1>Products</h1>
    <div class="col-md-12">
     <div id="products" class="row list-group">
            <?php
            $req = $pdo->prepare("SELECT * FROM products WHERE (category_id IS NULL OR category_id = '0') ORDER BY product_order ASC");
            $req->execute();

            if($req->rowCount() == 0){
                echo "<h2>".$language['product_nothing_available']."</h2>";
            }else{
                while ($row = $req->fetch()) {
                    if(empty($row->product_image_url)){
                        $image = 0;
                        $image_url = "img/empty_icon.png";
                    }else{
                        $image = 1;
                        $image_url = $row->product_image_url;
                    }

                    $link = $row->product_url;
                    $link_id = explode('selly.gg/p/', $link);
                    $link_id = $link_id[1];
                    $link_id = str_replace('/','',$link_id);

                    echo "
                    <div class='item  col-md-4'>
                    <div class='thumbnail'>
                    <img class='group list-group-image' style='object-fit: cover;max-width: 100px;max-height: 100px;' width='100%' height='100%' src='$image_url' alt='' />
                        <div class='caption'>
                            <b class='group inner list-group-item-heading'>
                                $row->product_name</b>
                                <br />
                            <p class='group inner list-group-item-text'>
                                $row->product_description</p>
                            <div class='row'>
                                <div class='col-xs-12 col-md-12'>
                                    <hr />
                                    <p class='lead'>$row->product_price $row->product_currency</p>  
                                </div>
                                <div class='col-xs-12 col-md-12'>
                                    <hr />
                                    <button class='btn btn-success' data-selly-product='".$link_id."'>".$language['product_purchase']."</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>";
                }
            }
            ?>
        </div>
    </div>
</div>

<?php require 'inc/footer.php'; ?>