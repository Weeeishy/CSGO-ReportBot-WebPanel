<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/functions.php';
require_once 'inc/db.php';
require '../inc/language.php';
is_admin();

if(isset($_POST['token_use'])){
	// We add a token to this user
	$uid = htmlentities($_POST['user_id']);
	if(is_null($uid)){
		// error, id cant be null
		header("Location: memberlist.php");
		exit();
	}

	$token_use = htmlentities($_POST['token_use']);
	$token_type = htmlentities($_POST['token_type']);

	if(is_null($token_type)){
		// set default token as report
		$token_type = "report";
	}

	if($token_type != "report"){
		if($token_type != "commend"){
			// invalid type
			$_SESSION['flash']['danger'] = "Invalid token type.";
		header("Location: memberlist.php");
		exit();
		}
	}
	$token = uniqidReal();

	$req = $pdo->prepare('INSERT INTO tokens SET token = :token, token_use = :use, token_type = :type, token_generation = CURRENT_TIMESTAMP, token_ownerid = :owner');
	$req->bindParam(':token', $token);
	$req->bindParam(':use', $token_use);
	$req->bindParam(':type', $token_type);
	$req->bindParam(':owner', $uid);
	$req->execute();

	$_SESSION['flash']['success'] = $language['admin__memberlist_token_created'] . "<br />Code: $token ($token_type | $token_use)";
	header("Location: memberlist.php");
	exit();
}

if(isset($_POST['edit_password'])){
	$password = htmlentities($_POST['edit_password']);
	$password_confirm = htmlentities($_POST['edit_passwordconfirm']);
	$uid = htmlentities($_POST['user_id']);

	if($password == $password_confirm){
		// we can change pw
		$password = password_hash($password, PASSWORD_BCRYPT);
		$req = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
		$req->bindParam(':password', $password);
		$req->bindParam(':id', $uid);
		$req->execute();

		$_SESSION['flash']['success'] = $language['admin_memberlist_password_edited'];
		header("Location: memberlist.php");
		exit();
	}else{
		// invalid pw
		$_SESSION['flash']['danger'] = $language['admin_memberlist_password_error'];
		header("Location: memberlist.php");
		exit();
	}

}

if(isset($_POST['delete'])){
	if($_POST['delete'] == 1){
		$uid = htmlentities($_POST['user_id']);

		$req = $pdo->prepare('DELETE FROM users WHERE id = ?');
		$req->execute([$uid]);

		$_SESSION['flash']['success'] = $language['admin_memberlist_deleted'];
		header("Location: memberlist.php");
		exit();


	}else{
		// invalid request
	}
}

if(isset($_GET['del'])){
	$tid = htmlentities($_GET['tid']);
	$req = $pdo->query("DELETE FROM tokens WHERE id = '$tid'");
	//$req->execute([$tid]);
	//debug($req);
	//die();
	header("Location: memberlist.php");
	exit();
}

if(isset($_POST['ban_reason'])){
	$ban_reason = htmlentities($_POST['ban_reason']);
	$ban_number = htmlentities($_POST['ban_number']);
	$ban_type = htmlentities($_POST['ban_type']);

	$today = date('Y-m-d');
	

	$ban_expiration = date("Y-m-d", strtotime($today . '+ ' . $ban_number . ' ' . $ban_type));


	$uid = htmlentities($_POST['user_id']);

	// we need to check if already banned

	$req = $pdo->prepare('SELECT * FROM banned_users WHERE uid = ? AND expiration > CURDATE()');
	$req->execute([$uid]);
	if($req->rowCount() > 0){
		$_SESSION['flash']['danger'] = $language['admin_memberlist_already_banned'];
		header("Location: memberlist.php");
		exit();
	}

	$req = $pdo->prepare('SELECT * FROM users WHERE id = ?');
	$req->execute([$uid]);

	$ip = htmlentities($req->fetch()->ip);

	if(is_null($ip)){
		$ip = "0.0.0.0";
	}
	
	

	$req = $pdo->prepare('INSERT INTO banned_users SET uid = :uid, ip = :ip, reason = :reason, expiration = :expiration');

	$req->bindParam(':uid', $uid);
	$req->bindParam(':ip', $ip);
	$req->bindParam(':reason', $ban_reason);
	$req->bindParam(':expiration', $ban_expiration);
	$req->execute();

	$_SESSION['flash']['success'] = $language['admin_memberlist_successfully_banned'];
	header("Location: memberlist.php");
	exit();

}

if(isset($_POST['unban_user_id'])){
	$uid = htmlentities($_POST['unban_user_id']);
	// we need to check if user is banned or not
	$req = $pdo->prepare('SELECT * FROM banned_users WHERE uid = ? AND expiration > CURDATE()');
	$req->execute([$uid]);
	if($req->rowCount() == 0){
		$_SESSION['flash']['danger'] = $language['admin_memberlist_not_banned'];
		header("Location: memberlist.php");
		exit();
	}


	$req = $pdo->prepare('UPDATE banned_users SET expiration = "1970-01-01" WHERE uid = ?');
	$req->execute([$uid]);

	$_SESSION['flash']['success'] = $language['admin_memberlist_unbanned'];
	header("Location: memberlist.php");
	exit();

}

if(isset($_POST['edit_tid'])){
	$tid = htmlentities($_POST['edit_tid']);
	$tname = htmlentities($_POST['edit_tname']);
	$ttype = htmlentities($_POST['edit_ttype']);
	$tuse = htmlentities($_POST['edit_tuse']);

	$req = $pdo->prepare("UPDATE tokens SET token = ?, token_type = ?, token_use = ? WHERE id = ?");
	$req->execute([$tname, $ttype, $tuse, $tid]);
}

if(isset($_POST['edit_points'])){
	$points = htmlentities($_POST['edit_points']);
	$uid = htmlentities($_POST['uid']);
	//die("UPDATE rewards_users SET points = $points WHERE uid = $uid");

	$req = $pdo->prepare("UPDATE rewards_users SET points = ? WHERE uid = ?");
	$req->execute([$points, $uid]);

	$_SESSION['flash']['success'] = "Points edited.";
	header("Location: memberlist.php");
	exit();
}

require_once 'inc/header.php';

?>

<h1><?= $language['admin_memberlist'] ?></h1>

<input type="text" id="myInput" class="form-control" onkeyup="myFunction()" placeholder="<?= $language['admin_memberlist_search_user'] ?>">
<br />
<table class="table table-bordered" id="myTable">
	<thead>
		<tr>
			<th><?= $language['admin_memberlist_nameid'] ?></th>
			<th><?= $language['admin_memberlist_rank'] ?></th>
			<th><?= $language['admin_memberlist_ip'] ?></th>
			<th><?= $language['admin_memberlist_banned'] ?></th>
			<th>Points</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<tr>
		<?php

			$req = $pdo->prepare('SELECT * FROM users');
			$req->execute();
			while($row = $req->fetch()){
				if($row->is_admin == 1){
					$rang = "Admin";
				}else{
					$rang = "User";
				}

				$check_ban = $pdo->query('SELECT * FROM banned_users WHERE uid = '.$row->id . ' AND expiration > CURDATE()');
				if($check_ban->rowCount() >0){
					$ban = "<i class='fa fa-check' aria-hidden='true' style='color: green;'></i> <u>".$language['admin_memberlist_banned_reason']."</u>:<i> " . $check_ban->fetch()->reason . "</i>";
				}else{
					$ban = "<i class='fa fa-times' aria-hidden='true' style='color: red;'></i>";
				}

				if(!is_null($row->steamid)){
					$pts = $pdo->query("SELECT * FROM rewards_users WHERE uid = '".$row->id."'");
					if($pts->rowCount() == 1){
						$points = $pts->fetch()->points;
						$points = '<form method="post" action=""><input name="edit_points" type="numbers" style="width: 50%;" class="form-control" value="'.$points.'"><input type="hidden" name="uid" value="'.$row->id.'"><button type="submit" class="btn btn-xs btn-info">Edit</button></form>';
					}else{
						$points = "0";
					}
					
				}else{
					$points = "0";
				}

				?>
				<td><?= $row->username ?> - <?= $row->id ?> </td>
				<td><?= $rang ?></td>
				<td><?= $row->ip ?></td>
				<td><?= $ban ?></td>
				<td><?= $points ?></td>
				<td>
					<div class="dropdown btn btn-default">
					    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Action <b class="caret"></b></a>
					    <ul class="dropdown-menu">
					    	<li><a href="#" data-toggle="modal" data-target="#viewtoken_<?= $row->id ?>">View <?= $language['admin_memberlist_token'] ?></a></li>
					    	<li><a href="#" data-toggle="modal" data-target="#token_<?= $row->id ?>">Add <?= $language['admin_memberlist_token'] ?></a></li>
					        <li><a href="#" data-toggle="modal" data-target="#edit_<?= $row->id ?>"><?= $language['admin_memberlist_edit'] ?></a></li>
					        <li><a href="#" data-toggle="modal" data-target="#delete_<?= $row->id ?>"><?= $language['admin_memberlist_delete'] ?></a></li>
					        <li><a href="#" data-toggle="modal" data-target="#ban_<?= $row->id ?>"><?= $language['admin_memberlist_ban'] ?></a></li>
					        <li><a href="#" data-toggle="modal" data-target="#unban_<?= $row->id ?>"><?= $language['admin_memberlist_unban'] ?></a></li>
					    </ul>
					</div>
				</td>
			</tr>

				<?php
			}


		?>
	</tbody>

</table>
<script>
function myFunction() {
  // Declare variables 
  var input, filter, table, tr, td, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } 
  }
}
</script>
<?php

$req = $pdo->prepare('SELECT * FROM users');
$req->execute();
while($row = $req->fetch()){
	echo '

	<div class="modal fade" id="viewtoken_'.$row->id.'" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Token list for: '.$row->username.'</h4>
        </div>
        <div class="modal-body">
          <table class="table table-bordered">
          	<thead>
          		<tr>
          			<th>ID</th>
          			<th>Token</th>
          			<th>Type</th>
          			<th>Uses</th>
          			<th></th>
          		</tr>
          	</thead>
            <tbody>';
            $g = $pdo->query("SELECT * FROM tokens WHERE token_ownerid = '".$row->id."'");
            while($t = $g->fetch()){
            echo "<form method='post'>
            	<tr>
            		<td>$t->id<input type='hidden' name='edit_tid' value='$t->id'></td>
            		<td><input type='text' class='form-control' value='$t->token' name='edit_tname'></td>
            		<td><select type='text' class='form-control'name='edit_ttype'>
            		";

            		if($t->token_type == "commend"){
					echo "<option value='commend'>Commend</option>";
					echo "<option value='report'>Report</option>";
					echo "<option value='whitelist'>Whitelist</option>";
				}elseif($t->token_type == "report"){
					echo "<option value='report'>Report</option>";
					echo "<option value='commend'>Commend</option>";
					echo "<option value='whitelist'>Whitelist</option>";
				}else{
					echo "<option value='whitelist'>Whitelist</option>";
					echo "<option value='commend'>Commend</option>";
					echo "<option value='report'>Report</option>";
				}


            		echo "
            		</select></td>
            		<td><input type='text' class='form-control' value='$t->token_use' name='edit_tuse'></td>
            		<td><a href='?del=$row->id&tid=$t->id'><button class='btn btn-xs btn-danger'>Delete</button></a> <button type='submit' class='btn btn-xs btn-info'>Edit</button></td>
            	</tr>
            	</form>";
            }
           echo '</tbody>
         </table>

        </div>
      </div>
      
    </div>
  </div>

	';
}
?>

<?php

$req = $pdo->prepare('SELECT * FROM users');
$req->execute();
while($row = $req->fetch()){
	echo '

	<div class="modal fade" id="token_'.$row->id.'" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">'.$language['admin_memberlist_token_add'].' '.$row->username.'</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="">
          	<div class="form-group">
          		<label>'.$language['admin_memberlist_token_use'].'</label>
          		<input required type="number" class="form-control" name="token_use"">
          		<input type="hidden" name="user_id" value="'.$row->id.'">
          	</div>

          	<div class="form-group">
          		<label>'.$language['admin_memberlist_token_type'].':</label>
          		<select class="form-control" name="token_type">
          			<option value="report">Report</option>
          			<option value="commend">Commend</option>
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

<?php

$req = $pdo->prepare('SELECT * FROM users');
$req->execute();
while($row = $req->fetch()){
	echo '

	<div class="modal fade" id="edit_'.$row->id.'" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">'.$language['admin_memberlist_edit'].' '.$row->username.'</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="">
          	<div class="form-group">
          		<label>'.$language['admin_memberlist_set_password'].'</label>
          		<input required type="password" class="form-control" name="edit_password">
          		<input type="hidden" name="user_id" value="'.$row->id.'">
          	</div>

          	<div class="form-group">
          		<label>'.$language['admin_memberlist_confirm_password'].'</label>
          		<input required type="password" class="form-control" name="edit_passwordconfirm">
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

<?php

$req = $pdo->prepare('SELECT * FROM users');
$req->execute();
while($row = $req->fetch()){
	echo '

	<div class="modal fade" id="delete_'.$row->id.'" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">'.$language['admin_memberlist_delete'].' '.$row->username.'</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="">
          	<div class="form-group">
          		<label>'.$language['admin_memberlist_delete_confirm'].'</label>
          		<input type="hidden" value="1" name="delete">
          		<input type="hidden" name="user_id" value="'.$row->id.'">


          	</div>
			<button type="submit" class="btn btn-danger">'.$language['admin_memberlist_confirm'].'</button>
          	</form>
        </div>
      </div>
      
    </div>
  </div>

	';
}
?>

<?php

$req = $pdo->prepare('SELECT * FROM users');
$req->execute();
while($row = $req->fetch()){
	echo '

	<div class="modal fade" id="ban_'.$row->id.'" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">'.$language['admin_memberlist_ban'].' '.$row->username.'</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="">
          	<div class="form-group">
          		<label>'.$language['admin_memberlist_ban_reason'].'</label>
          		<input type="text" value="" name="ban_reason" class="form-control">
          		<input type="hidden" name="user_id" value="'.$row->id.'">
          	</div>

          	<div class="form-group form-inline">
          		<label>'.$language['admin_memberlist_ban_duration'].'</label>
          		<br />
          		<input type="number" value="" name="ban_number" width="500px;"class="form-control">
          		<select class="form-control" name="ban_type" width="20%;">
          			<option value="days">'.$language['admin_memberlist_ban_days'].'</option>
          			<option value="month">'.$language['admin_memberlist_ban_month'].'</option>
          			<option value="year">'.$language['admin_memberlist_ban_years'].'</option>
          		</select>
          	</div>


			<button type="submit" class="btn btn-danger">'.$language['admin_memberlist_ban'].'</button>
          	</form>
        </div>
      </div>
      
    </div>
  </div>

	';
}
?>

<?php

$req = $pdo->prepare('SELECT * FROM users');
$req->execute();
while($row = $req->fetch()){
	echo '

	<div class="modal fade" id="unban_'.$row->id.'" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">'.$language['admin_memberlist_unban'].' '.$row->username.'</h4>
        </div>
        <div class="modal-body">
          <form method="post" action="">
          	<div class="form-group">
          		<label>'.$language['admin_memberlist_unban_confirm'].'</label>
          		<input type="hidden" name="unban_user_id" value="'.$row->id.'">
          	</div>
			<button type="submit" class="btn btn-success">'.$language['admin_memberlist_unban'].'</button>
          	</form>
        </div>
      </div>
      
    </div>
  </div>

	';
}
?>
<?php include('inc/footer.php');