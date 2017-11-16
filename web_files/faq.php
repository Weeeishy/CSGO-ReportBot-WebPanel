<?php $page = "Frequently Asked Questions"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/header.php'; ?>
<?php require 'inc/db.php'; 
require 'inc/language.php'; ?>

<h1>Frequently Asked Questions</h1>
	<hr />
<div class="row">

	<div class="panel-group" id="accordion">
		<?php
		$req = $pdo->query("SELECT * FROM faq ORDER BY id ASC");
		$count = $req->rowCount();

		if($count == 0){
			echo "<h3>No questions available</h3>";
		}else{

			while($row = $req->fetch()) : ?>
			<?php

			$id = $row->id;
			if(substr($id, -1) == '1' OR substr($id, -1) == '3' OR substr($id, -1) == '5' OR substr($id, -1) == '7' OR substr($id, -1) == '9'){

				$type = "primary";
			}else{
				$type = "default";
			} ?>

		    <div class="panel panel-<?=$type?>" id="panel1">
		        <div class="panel-heading">
		             <h4 class="panel-title">
		        <a data-toggle="collapse" data-target="#question_<?=$row->id?>" 
		           href="#question_<?=$row->id?>">
		          <?=$row->question?>
		        </a>
		      </h4>

		        </div>
		        <div id="question_<?=$row->id?>" class="panel-collapse collapse">
		            <div class="panel-body"><?=$row->answer?></div>
		        </div>
		    </div>

			<?php endwhile; } ?>

	</div>
</div>


<?php require 'inc/footer.php'; ?>