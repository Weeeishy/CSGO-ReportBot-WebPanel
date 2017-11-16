<?php $page = "Whitelisted players"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/header.php';
require 'admin/inc/functions.php';
is_admin(); ?>

<h1>Whitelisted players</h1>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>ID</th>
			<th>SteamID</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>
		<?php
		require 'inc/db.php';
		$req = $pdo->prepare('SELECT * FROM whitelist');
		$req->execute();
		$result = $req->fetchall();

		foreach($result as $row){
			echo "
			<tr>
				 <td>$row->id</td>
				 <td>$row->steamid</td>
				 <td>$row->added_date</td>
			 </tr>
			 ";
		}

		?>
	</tbody>
</table>


<?php require 'inc/footer.php'; ?>