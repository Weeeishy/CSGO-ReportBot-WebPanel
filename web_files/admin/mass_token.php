<?php
require '../inc/config.php';
require '../inc/functions.php';
require 'inc/functions.php';
require 'inc/db.php';
require '../inc/language.php';
is_admin();

if(isset($_POST['token_number'])){
	$token_number = htmlentities($_POST['token_number']);
	$token_use = htmlentities($_POST['token_use']);
	$token_type = htmlentities($_POST['token_type']);
	$token_list = "START";

	if($token_type != "report"){
		if($token_type != "commend"){
			if($token_type != "whitelist"){
				die("Invalid token type");
			}
		}
	}

	for($i = 1; $i <= $token_number; $i++){
		$token = "$token_type" ."_". uniqidReal();
		$req = $pdo->prepare("INSERT INTO tokens SET token = :token, token_use = :token_use, token_type = :token_type, token_generation = CURRENT_TIMESTAMP");
		$req->bindParam(':token', $token);
		$req->bindParam(':token_use', $token_use);
		$req->bindParam(':token_type', $token_type);
		$req->execute();

		$token_list = $token_list . ', '. $token;
	}
	require_once 'inc/header.php';
	$token_list = str_replace('START, ', '', $token_list);
	echo 'This is a text ready. Just past it into your selly.gg product. <br />';
	echo 'Generated ' . $token_number . ' Tokens with ' . $token_use . ' use(s)';
	echo '<pre>' . $token_list . '</pre>';
}
require_once 'inc/header.php';

?>
  
<h1><?= $language['admin_token'] ?></h1>


<div class="col-md-12">
	<h1><?= $language['mass_token_generator']?></h1>
	<form method="post" action="">
		<div class="form-group">
			<label><?= $language['mass_token_how_many_token'] ?></label>
			<input required type="number" name="token_number" class="form-control" placeholder=""></label>
		</div>

		<div class="form-group">
			<label><?= $language['admin_token_form_amount_of_use'] ?></label></label>
			<input required type="number" name="token_use" class="form-control" placeholder="0">
		</div>

		<div class="form-group">
			<select class="form-control" name="token_type">
				<option value="report">Report</option>
				<option value="commend">Commend</option>
				<option value="whitelist">Whitelist</option>
			</select>
		</div>

		<button class="btn btn-primary" type="submit"><?= $language['admin_token_form_generate'] ?></button>
	</form>

</div>


<?php include('inc/footer.php');