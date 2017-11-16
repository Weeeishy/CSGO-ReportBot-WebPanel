<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once '../inc/functions.php';
require_once 'inc/header.php';
require_once 'inc/db.php';
is_admin();?>

<h1>Token history</h1>
<div class="row">
	<div class="col-md-6 col-xs-12">
		<h3>Report</h3>
		<table class="table table-responsive">
			<thead>
				<tr>
					<th>ID</th>
					<th>Token</th>
					<th>user IP</th>
					<th>User</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$req = $pdo->query("SELECT * FROM tokens_activation WHERE type = 'report' ORDER BY id DESC");
					while($row = $req->fetch()){
						if(!empty($row->uid)){
							$r = $pdo->query("SELECT * FROM users WHERE id = '$row->uid'");
							$username = $r->fetch()->username;
						}else{
							$username = "Unknown";
						}
					?>
					<tr>
						<td><?= $row->id ?></td>
						<td><?= $row->token ?></td>
						<td><?= $row->uip ?></td>
						<td><?= $username ?></td>
						<td><?= $row->date ?></td>
					</tr>
					<?php }

					?>
			</tbody>
		</table>
	</div>

	<div class="col-md-6 col-xs-12">
		<h3>Whitelist</h3>
		<table class="table table-responsive">
			<thead>
				<tr>
					<th>ID</th>
					<th>Token</th>
					<th>user IP</th>
					<th>User</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$req = $pdo->query("SELECT * FROM tokens_activation WHERE type = 'whitelist' ORDER BY id DESC");
					while($row = $req->fetch()){
						if(!empty($row->uid)){
							$r = $pdo->query("SELECT * FROM users WHERE id = '$row->uid'");
							$username = $r->fetch()->username;
						}else{
							$username = "Unknown";
						}
					?>
					<tr>
						<td><?= $row->id ?></td>
						<td><?= $row->token ?></td>
						<td><?= $row->uip ?></td>
						<td><?= $username ?></td>
						<td><?= $row->date ?></td>
					</tr>
					<?php }

					?>
			</tbody>
		</table>
	</div>

	<div class="col-md-12 col-xs-12">
	<hr />
	</div>

	<div class="col-md-6 col-xs-12">
		<h3>Commend</h3>
		<table class="table table-responsive">
			<thead>
				<tr>
					<th>ID</th>
					<th>Token</th>
					<th>user IP</th>
					<th>User</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$req = $pdo->query("SELECT * FROM tokens_activation WHERE type = 'commend' ORDER BY id DESC");
					while($row = $req->fetch()){
						if(!empty($row->uid)){
							$r = $pdo->query("SELECT * FROM users WHERE id = '$row->uid'");
							$username = $r->fetch()->username;
						}else{
							$username = "Unknown";
						}
					?>
					<tr>
						<td><?= $row->id ?></td>
						<td><?= $row->token ?></td>
						<td><?= $row->uip ?></td>
						<td><?= $username ?></td>
						<td><?= $row->date ?></td>
					</tr>
					<?php }

					?>
			</tbody>
		</table>
	</div>

	<div class="col-md-6 col-xs-12">
		<h3>Account</h3>
		<table class="table table-responsive">
			<thead>
				<tr>
					<th>ID</th>
					<th>Token</th>
					<th>user IP</th>
					<th>User</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$req = $pdo->query("SELECT * FROM tokens_activation WHERE type = 'account' ORDER BY id DESC");
					while($row = $req->fetch()){
						if(!empty($row->uid)){
							$r = $pdo->query("SELECT * FROM users WHERE id = '$row->uid'");
							$username = $r->fetch()->username;
						}else{
							$username = "Unknown";
						}
					?>
					<tr>
						<td><?= $row->id ?></td>
						<td><?= $row->token ?></td>
						<td><?= $row->uip ?></td>
						<td><?= $username ?></td>
						<td><?= $row->date ?></td>
					</tr>
					<?php }

					?>
			</tbody>
		</table>
	</div>
</div>

<?php require_once 'inc/footer.php'; ?>