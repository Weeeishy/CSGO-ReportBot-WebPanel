<?php
require '../inc/config.php';
require '../inc/functions.php';
require 'inc/functions.php';
require 'inc/db.php';
require '../inc/language.php';
is_admin();

require_once 'inc/header.php';
?>

<div class="row">
	<h1>Referral - Admin</h1>

	<table class="table-responsive table table-bordered">
		<thead>
			<tr>
				<th>#</th>
				<th>Username</th>
				<th>Code</th>
				<th>Number of referral</th>
				<th>Number of purchased token by all refered users</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$req = $pdo->query("SELECT * FROM referral_codes ORDER BY id ASC");
			while($row = $req->fetch()) :

				$r = $pdo->query("SELECT * FROM users WHERE id = '$row->uid'");
				$username = $r->fetch()->username;

				$r = $pdo->query("SELECT * FROM referral_users WHERE owner_userid = '$row->uid'");
				$referral_numbers = $r->rowCount();

				$token_used = "0";
				while($g = $r->fetch()){
					$q = $pdo->query("SELECT * FROM tokens_activation WHERE uid = '$g->user_id'");
					$token_used =  $token_used + $q->rowCount();
				}

			?>
				<tr>
					<td><?=$row->id?></td>
					<td><?=$username ?></td>
					<td><?=$row->code?></td>
					<td><?=$referral_numbers?></td>
					<td><?=$token_used?></td>
				</tr>
			<?php endwhile;
			?>
		</tbody>
	</table>
</div>

<?php include('inc/footer.php');