<?php $page = "Reported players"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/header.php';
require 'admin/inc/functions.php';
is_admin();

$req = $pdo->prepare("SELECT * FROM reported_list WHERE vac = 'true'");
$req->execute();
$num_vac = $req->rowCount();

$req = $pdo->prepare("SELECT * FROM reported_list WHERE ow = 'true'");
$req->execute();
$num_ow = $req->rowCount();

$req = $pdo->prepare("SELECT * FROM reported_list");
$req->execute();
$num_report = $req->rowCount();
 ?>

<h1>Reported players</h1>
<div class="alert alert-info"><center>REPORTED: <?= $num_report ?> | OW BANNED: <?= $num_ow ?> | VAC BANNED: <?= $num_vac ?></center></div>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>ID</th>
			<th>SteamID</th>
			<th>Date</th>
			<th>Inventory Value</th>
			<th>Logs</th>
			<th><center>OW</center></th>
			<th><center>VAC</center></th>
		</tr>
	</thead>
	<tbody>
		<?php
		require 'inc/db.php';
		$req = $pdo->prepare('SELECT * FROM reported_list ORDER BY id DESC');
		$req->execute();
		$result = $req->fetchall();

		foreach($result as $row){
					
			if($row->vac == "true"){
				$vac = "<center><i class='fa fa-check fa-2x' aria-hidden='true' style='color: green;'></i></center>";
			}else{
				$vac = "<center><i class='fa fa-times fa-2x' aria-hidden='true' style='color: red;'></i></center>";
			}
			
			if($row->ow == "true"){
				$ow = "<center><i class='fa fa-check fa-2x' aria-hidden='true' style='color: green;'></i></center>";
			}else{
				$ow = "<center><i class='fa fa-times fa-2x' aria-hidden='true' style='color: red;'></i></center>";
			}

			if(ctype_digit($row->inventory_value)){
				$inventory_value = "<font style='color: red;'>$row->inventory_value</font>";
			}else{
				$inventory_value = $row->inventory_value;
			}
			

			echo "
			<tr>
				 <td>$row->id</td>
				 <td><a href='http://steamcommunity.com/profiles/$row->steamid' target='_blank'>$row->steamid</a></td>
				 <td>$row->datum</td>
				 <td>$inventory_value</td>
				 <td><a href='./report.php?l=$row->steamid' target='_blank'>Report log</a></td>
				 <td>$ow</td>
				 <td>$vac</td>
			 </tr>
			 ";
		}

		?>
	</tbody>
</table>


<?php require 'inc/footer.php'; ?>