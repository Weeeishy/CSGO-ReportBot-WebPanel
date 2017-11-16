<?php
require '../inc/config.php';
require '../inc/functions.php';
require 'inc/functions.php';
require 'inc/db.php';
require '../inc/language.php';
is_admin();

$confirm_code = uniqidReal();

$confirm_code_exploded = chunk_split($confirm_code, 1, ' ');

if(isset($_POST['confirm_code_delete'])){
	$delete_code_user = htmlentities($_POST['confirm_code_delete']);
	$delete_code = htmlentities($_POST['code_delete']);
	
	if($delete_code_user == $delete_code){
		// code valid
		// we can delete everything
		
		$req = $pdo->prepare('DELETE FROM tokens;');
		$req->execute();
		
		$_SESSION['flash']['success'] = "Successfully deleted every token.";
		header("Location: token.php");
		exit();
	}else{
		// invalid code
		$_SESSION['flash']['danger'] = "Confirmation code is invalid";
		header("Location: token.php");
		exit();
	}
}


if(isset($_POST['token_name'])){
	if(!empty($_POST['token_name'])){
		if(!empty($_POST['token_use'])){
			if(is_numeric($_POST['token_use'])){
				
			$token = htmlentities($_POST['token_name']);
			$token_use = htmlentities($_POST['token_use']);

			$token_type = htmlentities($_POST['token_type']);

			// some checking
			if($token_type != "report"){
				if($token_type != "commend"){
					if($token_type != "whitelist"){
						die("Invalid token type");
					}
				}
			}

			// We need to check if token with this name already exist
			$req = $pdo->prepare('SELECT * FROM tokens WHERE token = ?');
			$req->execute([$token]);
			$exist = $req->rowCount();
			if($exist == 0){
				$req = $pdo->prepare('INSERT INTO tokens SET token = ?, token_use = ?, token_generation = CURRENT_TIMESTAMP, token_type = ?');
				$req->execute([$token, $token_use, $token_type]);

				$_SESSION['flash']['success'] = $language['admin_token_created'];
				header("Location: token.php");
				exit();
			}else{
				// Token already exist
			$_SESSION['flash']['danger'] = $language['admin_token_already_exist'];
				header("Location: token.php");
				exit();
			}

			}else{
				$_SESSION['flash']['danger'] = $language['admin_token_incorrect_use'];
				header("Location: token.php");
				exit();	
			}

		}else{
			$_SESSION['flash']['danger'] = $language['admin_token_use_empty'];
			header("Location: token.php");
			exit();
		}

	}else{
		$_SESSION['flash']['danger'] = $language['admin_token_empty'];
		header("Location: token.php");
		exit();
	}
}

if(isset($_POST['token_delete_id'])){
	$token_id = htmlentities($_POST['token_delete_id']);
	$req = $pdo->prepare("DELETE FROM tokens WHERE id = ?");
	$req->execute([$token_id]);
	$_SESSION['flash']['success'] = $language['admin_token_deleted'];
	header("Location: token.php");
	exit();
}

if(isset($_POST['token_edit'])){
	if(isset($_POST['token_edit_id'])){
		if(!empty($_POST['token_edit'])){
			if(!empty($_POST['token_edit_id'])){
				if((ctype_digit($_POST['token_edit_id']) AND (ctype_digit($_POST['token_edit'])))){
						// everything ok
						// we check if token exist first
						$req = $pdo->prepare('SELECT * FROM tokens WHERE id = ?');
						$req->execute([htmlentities($_POST['token_edit_id'])]);
						$exist = $req->rowCount();

						$token_type = htmlentities($_POST['token_edit_type']);

						if($token_type != "report"){
							if($token_type != "commend"){
								if($token_type != "whitelist"){
									die("Invalid token type");
								}
							}
						}

						if($exist == 1){
							// token exist, we can edit it
							$req = $pdo->prepare('UPDATE tokens SET token_use = ?, token_type = ? WHERE id = ?');
							$req->execute([htmlentities($_POST['token_edit']), htmlentities($_POST['token_edit_type']), htmlentities($_POST['token_edit_id'])]);
							$_SESSION['flash']['success'] = $language['admin_token_edit_success'];
							header("Location: token.php");
							exit();
						}else{
							// token doesnt exist, return error
							$_SESSION['flash']['danger'] = $language['admin_token_edit_doesnt_exist'];
							header("Location: token.php");
							exit();
						}

				}else{
					// Token or token ID isnt number
					$_SESSION['flash']['danger'] = $language['admin_token_edit_token_not_number'];
					header("Location: token.php");
					exit();
				}
			}else{
				// empty token id
				$_SESSION['flash']['danger'] = $language['admin_token_edit_empty_id'];
				header("Location: token.php");
				exit();
			}
		}else{
			// empty token
			$_SESSION['flash']['danger'] = $language['admin_token_edit_empty_token'];
			header("Location: token.php");
			exit();
		}
	}else{
		// no id provided, return error
		$_SESSION['flash']['danger'] = $language['admin_token_edit_no_id'];
		header("Location: token.php");
		exit();
	}

}

require_once 'inc/header.php';
?>
  
<h1><?= $language['admin_token'] ?></h1>
  <div class="col-md-10"></div>

 <div class=".col-md-2 .offset-md-2">
      <button class="btn btn-danger" data-toggle="modal" data-target="#token_delete_all">Delete all my token</button>
 </div>

<div class="col-md-9">
	<h1><?= $language['admin_token_manager'] ?></h1>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>ID</th>
				<th><?= $language['admin_token_name'] ?></th>
				<th><?= $language['admin_token_number_of_use'] ?></th>
				<th><?= $language['admin_token_generation_date'] ?></th>
				<th>Type</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php

			$req = $pdo->query('SELECT * FROM tokens');
			while($row = $req->fetch()){
				echo '
				<tr>
					<td>'.$row->id.'</td>
					<td>'.$row->token.'</td>
					<td>'.$row->token_use.'</td>
					<td>'.$row->token_generation.'</td>
					<td>'.$row->token_type.'</td>
					<td>
						<form method="post" action="">
						 	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit_token_'.$row->id.'">
'.$language['admin_token_edit'].'</button>						
						 	<input type="hidden" name="token_delete_id" value="'.$row->id.'">
						 	<button type="submit" name="token_delete" class="btn btn-danger">'.$language['admin_token_delete'].'</button>
						</form>
					</td>
				</tr>';

			}

			?>
		</tbody>
	</table>

</div>

<div class="col-md-3">
	<h1><?= $language['admin_token_generator'] ?></h1>
	<form method="post" action="">
		<div class="form-group">
			<label><?= $language['admin_token_form_name'] ?></label>
			<input required type="text" name="token_name" class="form-control" placeholder="<?= $language['admin_token_form_name'] ?>"></label>
		</div>

		<div class="form-group">
			<label><?= $language['admin_token_form_amount_of_use'] ?></label></label>
			<input required type="number" name="token_use" class="form-control" placeholder="0">
		</div>

		<div class="form-group">
			<label>Token type</label>
			<select class="form-control" name="token_type">
				<option value="report">Report</option>
				<option value="commend">Commend</option>
				<option value="whitelist">Whitelist</option>
			</select>
		</div>

		<button class="btn btn-primary" type="submit"><?= $language['admin_token_form_generate'] ?></button>
	</form>

</div>

<div class="modal fade" id="token_delete_all" role="dialog">
	<div class="modal-dialog">
			<div class="modal-content">
					<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4>Delete all tokens</h4>
					</div>

					<div class="modal-body">
						<form method="post" action="">
							<label>Please, enter the following code without spaces: <pre><center><?= $confirm_code_exploded ?></center></pre></label>
							<input type="text" name="confirm_code_delete" class="form-control" required>
							<hr />
							<input type="hidden" name="code_delete" value="<?= $confirm_code ?>">
							<button type="submit" class="btn btn-danger">Delete</button>
						</form>
					</div>
			</div>
	</div>
</div>


<?php

$req = $pdo->prepare('SELECT * FROM tokens');
$req->execute();
while($row = $req->fetch()){
	echo '

	<div class="modal fade" id="edit_token_'.$row->id.'" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">'.$language['admin_token_edit'].' ' .$row->token.'</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="">
          	<div class="form-group">
          		<label>'.$language['admin_token_form_amount_of_use'].'</label>
          		<input required type="number" class="form-control" name="token_edit" value="'.$row->token_use.'">
          		<input type="hidden" name="token_edit_id" value="'.$row->id.'">
          	</div>

          	<div class="form-group">
          		<select class="form-control" name="token_edit_type">
          			<option value="report">Report</option>
          			<option value="commend">Commend</option>
          			<option value="whitelist">Whitelist</option>
          		</select>
	          </div>
          	
          	<button type="submit" class="btn btn-default">Submit</button>
          	</form>
        </div>
      </div>
      
    </div>
  </div>

	';
}
?>



<?php include('inc/footer.php');