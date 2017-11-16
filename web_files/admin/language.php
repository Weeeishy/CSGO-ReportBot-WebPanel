<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/functions.php';
require '../inc/language.php';
require_once 'inc/db.php';
is_admin();
$dir = dirname(__FILE__);
$dir = str_replace('admin','',$dir);

if(isset($_POST['language_code'])){
	if(!empty($_POST['language_code'])){
		if(!empty($_POST['language_icon'])){
			if(!empty($_POST['language_name'])){
				// We can start processing
				if(file_exists($dir . '/lang/' . $_POST['language_code'] . '.php')){
					// Language file does exist
					if(file_exists($dir . '/img/lang/' . $_POST['language_icon'] . '.png')){
						// Icon exist
						// Now, we check if language doesnt already exist
						$req = $pdo->prepare('SELECT * FROM language WHERE (language_name = ? OR lang_code = ? OR lang_icon = ?)');
						$req->execute([htmlentities($_POST['language_name']), htmlentities($_POST['language_code']), htmlentities($_POST['language_icon'])]);
						$exist = $req->rowCount();
						if($exist == 0){
							// Now, we insert data into db
							$req = $pdo->prepare('INSERT INTO language SET language_name = ?, lang_code = ?, lang_icon = ?');
							$req->execute([htmlentities($_POST['language_name']), htmlentities($_POST['language_code']), htmlentities($_POST['language_icon'])]);

							// data inserted
							$_SESSION['flash']['success'] = $language['admin_language_added_succes'];
							header("Location: language.php");
							exit();

						}else{
							$_SESSION['flash']['danger'] = $language['admin_language_error_already_exist'];
							header("Location: language.php");
							exit();
						}

					
					}else{
						$_SESSION['flash']['danger'] = $language['admin_language_error_no_icon'];
						header("Location: language.php");
						exit();
					}
				}else{
					echo 'lang file doesnt exist';
					$_SESSION['flash']['danger'] = $language['admin_language_error_no_lang_file'];
					header("Location: language.php");
					exit();
				}



			}else{
				// empty name
				$_SESSION['flash']['danger'] = $language['admin_language_error_empty_name'];
				header("Location: language.php");
				exit();
			}
		}else{
			// empty icon
			$_SESSION['flash']['danger'] = $language['admin_language_error_empty_icon'];
			header("Location: language.php");
			exit();
		}
	}else{
		// empty language code
		$_SESSION['flash']['danger'] = $language['admin_language_error_empty_code'];
		header("Location: language.php");
		exit();
	}
} // end add language

if(isset($_POST['language_delete_id'])){
	$req = $pdo->prepare('DELETE FROM language WHERE id = ?');
	$req->execute([htmlentities($_POST['language_delete_id'])]);
	header("Location: language.php");
	exit();
}

require_once 'inc/header.php';

?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<div class="col-md-6">
	<h1><?= $language['admin_language'] ?></h1>
	<div class="col-md-12">
		<h4>Language Manager</h4>
		<table class="table table-bordered">
		<thead>
			<tr>
				<th>ID</th>
				<th><?= $language['admin_language_name'] ?></th>
				<th><?= $language['admin_language_code'] ?></th>
				<th><?= $language['admin_language_icon'] ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php

			$req = $pdo->query('SELECT * FROM language');
			while($row = $req->fetch()){
				echo '
				<tr>
					<td>'.$row->id.'</td>
					<td>'.$row->language_name.'</td>
					<td>'.$row->lang_code.'</td>
					<td>'.$row->lang_icon.'.png | <img height="20px" width="20px" src="../img/lang/'.$row->lang_icon.'.png"></td>
					<td>
						<form method="post" action="">
						 							
						 	<input type="hidden" name="language_delete_id" value="'.$row->id.'">
						 	<button type="submit" name="language_delete" class="btn btn-danger">'.$language['admin_language_delete'].'</button>
						</form>
					</td>
				</tr>';

			}

			?>
		</tbody>
	</table>
	</div>
</div>

<div class="col-md-6">
	<h1><?= $language['admin_language'] ?></h1>
	<div class="col-md-12">
		<h4><?= $language['admin_language_add'] ?></h4>
		<form method="post" action="">
			<div class="form-group">
				<label><?= $language['admin_language_form_name'] ?></label>
				<input type="text" class="form-control" name="language_name">
			</div>

			<div class="form-group">
				<label><?= $language['admin_language_form_code'] ?> | <i><?= $language['admin_language_form_code_info'] ?></i></label>
				<input type="text" class="form-control" name="language_code" placeholder="">
			</div>

			<div class="form-group">
				<label><?= $language['admin_language_form_flag'] ?> | <i><?= $language['admin_language_form_flag_info'] ?></i></label>
				<input type="text" class="form-control" name="language_icon">
			</div>

			<button type="submit" class="btn btn-primary"><?= $language['admin_language_add'] ?></button>
		</form>
	</div>
</div>

<?php include 'inc/footer.php'; ?>