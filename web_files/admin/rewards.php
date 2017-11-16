<?php
require '../inc/config.php';
require '../inc/functions.php';
require 'inc/functions.php';
require 'inc/db.php';
require '../inc/language.php';
is_admin();

if(isset($_POST['update_tag'])){
	$id = $_POST['update_hiddenid'];

	if(!is_numeric($id)){
		//invalid id
	}

	$tag = htmlentities($_POST['update_tag']);
	$tag_minconsecutivedays = htmlentities($_POST['update_tagmindays']);
	$points_per_day = htmlentities($_POST['update_ptsperday']);
	$points_per_report = htmlentities($_POST['update_ptsperreport']);
	$points_per_commend = htmlentities($_POST['update_ptspercommend']);
	$steamgroup_url = htmlentities($_POST['update_steamgroup_url']);
	$steamgroup_points = htmlentities($_POST['update_steamgroup_points']);

	$points_per_day = str_replace(",", ".", $points_per_day);
	$points_per_commend = str_replace(",", ".", $points_per_commend);
	$points_per_report = str_replace(",", ".", $points_per_report);

	if(!is_numeric($steamgroup_points)){
		die("Invalid Steam Group Points");
	}

	
		
	if(is_numeric($points_per_day)){
		//valid pts per day
		if(is_numeric($points_per_report)){
			//valid pts per report
			if(is_numeric($points_per_commend)){
				//valid pts per command
				// everything is fine
				$pdo->query("UPDATE rewards_config SET tag = '$tag', tag_minconsecutivedays = '$tag_minconsecutivedays', tag_pointsperday = '$points_per_day', pointsper_report = '$points_per_report', pointsper_commend = '$points_per_commend', steamgroup_url = '$steamgroup_url', steamgroup_points = '$steamgroup_points' WHERE id = '$id'");
				header("Location: rewards.php");
				exit();
			}else{
				// invlid pts per commend
			}
		}else{
			// invalid pts per report
		}
	}else{
		// invalid pts per day
	}
	
}

if(isset($_POST['reward_price'])){
	$reward_price = htmlentities($_POST['reward_price']);
	$reward_type = htmlentities($_POST['reward_type']);
	$reward_uses = htmlentities($_POST['reward_uses']);
	$reward_description = htmlentities($_POST['reward_description']);

	if(!is_null($reward_price) AND is_numeric($reward_price)){
		// valid price
		if(!is_null($reward_uses) AND is_numeric($reward_uses)){
			//valid uses
			if(!is_null($reward_type) AND ($reward_type == "commend" OR $reward_type == "report") OR $reward_type == "whitelist"){
				// valid type
				if(!is_null($reward_description)){
					// valid description, we can add this reward
					$req = $pdo->prepare("INSERT INTO rewards_list SET points_cost = :cost, reward_type = :type, reward_uses = :uses, description = :description ");

					$req->bindParam(':cost', $reward_price);
					$req->bindParam(':type', $reward_type);
					$req->bindParam(':uses', $reward_uses);
					$req->bindParam(':description', $reward_description);

					$req->execute();

					$_SESSION['flash']['success'] = $language['admin_rewards_created'];
					header("Location: rewards.php");
					exit();
				}else{
					// invalid reward description
				}
			}else{
				// invalid type
			}
		}else{
			// invalid uses
		}
	}else{
		// invalid price
	}
}

if(isset($_POST['edit_id'])){
	$id = htmlentities($_POST['edit_id']);
	$description = htmlentities($_POST['edit_description']);
	$cost = htmlentities($_POST['edit_cost']);
	$uses = htmlentities($_POST['edit_uses']);
	$type = htmlentities($_POST['edit_type']);

	$req = $pdo->prepare("UPDATE rewards_list SET points_cost = :cost, reward_type = :type, reward_uses = :uses, description = :description WHERE id = :id");
	$req->bindParam(':cost', $cost);
	$req->bindParam(':type', $type);
	$req->bindParam(':uses', $uses);
	$req->bindParam(':description', $description);
	$req->bindParam(':id', $id);
	
	$req->execute();
	
	$_SESSION['flash']['success'] = "Reward edited.";
	header("Location: rewards.php");
	exit();
}

require_once 'inc/header.php';

?>

<button class="btn btn-info pull-right" type="button" data-toggle="modal" data-target="#add_reward"><?= $language['admin_rewards_add']?></button>
<button class="btn btn-warning pull-right" type="button" data-toggle="modal" data-target="#rewards_history"><?= $language['admin_rewards_history']?></button>

<h2><?= $language['admin_rewards_configuration']?></h2>

<table class="table table-bordered table-responsive">
	<thead>
		<tr>
			<th>Tag</th>
			<th><?= $language['admin_rewards_mindays'] ?></th>
			<th><?= $language['admin_rewards_tagperday'] ?></th>
			<th><?= $language['admin_rewards_pointsperreport'] ?></th>
			<th><?= $language['admin_rewards_pointspercommend'] ?></th>
			<th>Steam Group URL <br /><i>(e.g: http://steamcommunity.com/group/potato/)</i></th>
			<th>Points earned by joining group</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<?php
			$req = $pdo->query('SELECT * FROM rewards_config');
			while($r = $req->fetch()){
				?>
				<form method="post">
					<td><input type="hidden" name="update_hiddenid" value="<?=$r->id ?>">
					<input class="form-control" name="update_tag" value="<?= $r->tag ?>"></td>
					<td><input class="form-control" name="update_tagmindays" value="<?= $r->tag_minconsecutivedays ?>"></td>
					<td><input class="form-control" name="update_ptsperday" value="<?= $r->tag_pointsperday ?>"></td>
					<td><input class="form-control" name="update_ptsperreport" value="<?= $r->pointsper_report ?>"></td>
					<td><input class="form-control" name="update_ptspercommend" value="<?= $r->pointsper_commend ?>"></td>
					<td><input class="form-control" name="update_steamgroup_url" value="<?= $r->steamgroup_url ?>"></td>
					<td><input class="form-control" name="update_steamgroup_points" value="<?= $r->steamgroup_points ?>"></td>
					<td><button type="post" class="btn btn-info"><i class="fa fa-check" aria-hidden="true"></i></button></td>
				</form>
				<?php
			}

			?>
		</tr>
	</tbody>
</table>

<h2><?= $language['admin_rewards_list'] ?></h2>
<table class="table table-bordered table-responsive">
	<thead>
		<tr>
			<th><?= $language['admin_rewards_description'] ?></th>
			<th><?= $language['rewards_price']?></th>
			<th><?= $language['admin_rewards_type']?></th>
			<th><?= $language['admin_rewards_uses']?></th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>

<?php 
$req = $pdo->query('SELECT * FROM rewards_list ORDER BY id ASC');
while($row = $req->fetch()){
	$claimed = $pdo->query("SELECT * FROM rewards_history WHERE reward_id = '$row->id'");
	$claimed = $claimed->rowCount();
?>


		<tr>
			<form method="post">
			<input type="hidden" name="edit_id" value="<?= $row->id?>">
			<td><textarea class="form-control" name="edit_description" rows="3"><?= $row->description ?></textarea></td>
			<td><input type="text" class="form-control" name="edit_cost" value="<?= $row->points_cost ?>"?></td>
			<td><select name="edit_type" class="form-control">
				<?php 
				if($row->reward_type == "commend"){
					echo "<option value='commend'>Commend</option>";
					echo "<option value='report'>Report</option>";
					echo "<option value='whitelist'>Whitelist</option>";
				}elseif($row->reward_type == "report"){
					echo "<option value='report'>Report</option>";
					echo "<option value='commend'>Commend</option>";
					echo "<option value='whitelist'>Whitelist</option>";
				}else{
					echo "<option value='whitelist'>Whitelist</option>";
					echo "<option value='commend'>Commend</option>";
					echo "<option value='report'>Report</option>";
				}
				?>
			</select></td>
			<td><input type="text" name="edit_uses" class="form-control" value="<?= $row->reward_uses ?>"?></td>
			<td><?= $language['admin_rewards_claimed']?> <?= $claimed ?> <?= $language['admin_rewards_times']?></td>
			<td><center><a href="?get=<?=$row->id?>"><button class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button></a> <a href="?get=<?=$row->id?>"> <button class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></button></a></center></td></form>
		</tr>


<?php
} // end while
?>
		</tbody>
</table>

<!-- Modal -->
<div class="modal fade " id="rewards_history" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title"><?= $language['admin_rewards_history']?></h4>
	    </div>
	    <div class="modal-body">
	    	<table class="table table-bordered table-reponsive">
	    		<thead>
	    			<tr>
	    				<th><?= $language['admin_rewards_user']?></th>
	    				<th><?= $language['rewards_price']?></th>
	    				<th><?= $language['admin_rewards_type'] ?></th>
	    				<th><?= $language['admin_rewards_uses']?></th>
	    				<th><?= $language['admin_rewards_date']?></th>
	    			</tr>
	    		</thead>
	    		<tbody>
	    			<?php
	    			$req = $pdo->query('SELECT * FROM rewards_history ORDER BY id DESC');
	    			while($r = $req->fetch()){
	    			$username = $pdo->query("SELECT * FROM users WHERE id = '$r->uid'")->fetch()->username; ?>
	    			<tr>
	    				<td><?= $username ?></td>
	    				<td><?= $r->cost ?></td>
	    				<td><?= $r->type ?></td>
	    				<td><?= $r->uses ?></td>
	    				<td><?= $r->date ?></td>
	    			<?php }
	    			?>
	    		</tbody>
	    	</table>
	    </div>
	  </div>
	  
	</div>
</div>

<div class="modal fade " id="add_reward" role="dialog">
	<div class="modal-dialog">

	  <!-- Modal content-->
	  <div class="modal-content">
	    <div class="modal-header">
	      <button type="button" class="close" data-dismiss="modal">&times;</button>
	      <h4 class="modal-title"><?= $language['admin_rewards_add']?></h4>
	    </div>
	    <div class="modal-body">
	    	<form method="post">
	    		<div class="form-group">
	    			<label><?= $language['admin_rewards_add_price']?></label>
	    			<input type="number" name="reward_price" class="form-control">
	    		</div>

	    		<div class="form-group">
	    			<label><?= $language['admin_rewards_add_type']?></label>
	    			<select name="reward_type" class="form-control">
	    				<option value="commend">Commend</option>
	    				<option value="report">Report</option>
	    			</select>
	    		</div>

	    		<div class="form-group">
	    			<label><?= $language['admin_rewards_add_uses']?></label>
	    			<input type="number" name="reward_uses" class="form-control">
	    		</div>

				<div class="form-group">
	    			<label><?= $language['admin_rewards_add_description']?></label>
	    			<textarea rows="5" name="reward_description" class="form-control"></textarea>
	    		</div>

	    		<hr />

	    		<button type="sumbit" class="btn btn-success"><?= $language['admin_rewards_create']?></button>
	    	</form>
	    </div>
	  </div>
	  
	</div>
</div>
<?php
require 'inc/footer.php'; ?>

