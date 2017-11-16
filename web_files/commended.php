<?php $page = "Commended players"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/header.php'; ?>

<h1>Commended players</h1>
<table class="table table-bordered">
	<thead>
		<tr>
			<th>ID</th>
			<th>SteamID</th>
			<th>Date</th>
			<th>Logs</th>
		</tr>
	</thead>
	<tbody>
		<?php
		require 'inc/db.php';
		$req = $pdo->prepare('SELECT * FROM commended_list');
		$req->execute();
		$result = $req->fetchall();

		foreach($result as $row){
			echo "
			<tr>
				 <td>$row->id</td>
				 <td>$row->steamid</td>
				 <td>$row->datum</td>
				 <td><a href='./commend.php?l=$row->steamid' target='_blank'>Commend log</a></td>
			 </tr>
			 ";
		}

		?>
	</tbody>
</table>


<?php require 'inc/footer.php'; ?>