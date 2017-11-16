<?php 
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once 'inc/functions.php';
require_once 'inc/db.php';
require '../inc/language.php';
is_admin();

if(isset($_POST['licence_key'])){
	$licence_key = htmlentities($_POST['licence_key']);
	$steam_api_key = htmlentities($_POST['steam_api_key']);


	$website_navtitle = htmlentities($_POST['website_navtitle']);
	$website_title = htmlentities($_POST['website_title']);

	$secret_key = htmlentities($_POST['secret_key']);
	$website_key = htmlentities($_POST['website_key']);

	$reportbot_number = htmlentities($_POST['reportbot_number']);
	$commendbot_number = htmlentities($_POST['commendbot_number']);

	$log_prefix = htmlentities($_POST['log_prefix']);

	$report_path = htmlentities($_POST['report_path']);
	$report_log_path = htmlentities($_POST['report_log_path']);

	$commend_path = htmlentities($_POST['commend_path']);
	$commend_log_path = htmlentities($_POST['commend_log_path']);

	$commend_timer = htmlentities($_POST['commend_timer']);
	$report_timer =  htmlentities($_POST['report_timer']);

	$values = $_POST;


	if(array_search("", $values) !== false){
		$_SESSION['flash']['danger'] = "One or more fields are empty.";
		header("Location: configuration.php");
		exit();
	}

	$req = $pdo->prepare("UPDATE config SET licence_key = :licence_key, steam_api_key = :steam_api_key, website_navtitle = :website_navtitle, website_title = :website_title, captcha_secret_key = :secret_key, captcha_website_key = :website_key, reportbot_number = :reportbot_number, commendbot_number = :commendbot_number, log_prefix = :log_prefix, report_path = :report_path, report_log_path = :report_log_path, commend_path = :commend_path, commend_log_path = :commend_log_path, report_timer = :report_timer, commend_timer = :commend_timer WHERE id = '1'");

	$req->bindParam(":licence_key", $licence_key);
	$req->bindParam(":steam_api_key", $steam_api_key);
	$req->bindParam(":website_navtitle", $website_navtitle);
	$req->bindParam(":website_title", $website_title);
	$req->bindParam(":secret_key", $secret_key);
	$req->bindParam(":website_key", $website_key);
	$req->bindParam(":reportbot_number", $reportbot_number);
	$req->bindParam(":commendbot_number", $commendbot_number);
	$req->bindParam(":log_prefix", $log_prefix);
	$req->bindParam(":report_path", $report_path);
	$req->bindParam(":report_log_path", $report_log_path);
	$req->bindParam(":commend_path", $commend_path);
	$req->bindParam(":commend_log_path", $commend_log_path);
	$req->bindParam(":report_timer", $report_timer);
	$req->bindParam(":commend_timer", $commend_timer);

	$req->execute();

	require 'inc/header.php';
	die("<b>Configuration edited. Click <a href='configuration.php'>here</a> to go back.</b>");

}

require 'inc/header.php'; ?>

<div class="row">
	<form method="post">

		<h1>Website configuration</h1>
		<button class="pull-right btn btn-success" type="submit">Save</button>
		<h4>Here, you can edit your website settings.</h4>

		<div class="form-group">
			<label>Licence Key</label>
			<input required type="text" name="licence_key" class="form-control" value="<?= $licence_key ?>">

			<br />

			<label>Steam API Key</label>
			<input required type="text" name="steam_api_key" class="form-control" value="<?= $steam_api_key ?>">
		</div>

		<hr />

		<div class="form-group">
			<label>Website Nav Title</label>
			<input required type="text" name="website_navtitle" class="form-control" value="<?= $website_navtitle ?>">

			<br />
			
			<label>Website Title</label>
			<input required type="text" name="website_title" class="form-control" value="<?= $website_title ?>">
		</div>

		<hr />

		<div class="form-group">
			<label>Captcha Website Key</label>
			<input required type="text" name="website_key" class="form-control" value="<?= $website_key ?>">
		</div>

		<hr />

		<div class="form-group">
			<label>Captcha Secret Key</label>
			<input required type="text" name="secret_key" class="form-control" value="<?= $secret_key ?>">
		</div>

		<hr />

		<div class="form-group">
			<label>Commendbot Number</label>
			<input required type="number" name="commendbot_number" class="form-control" value="<?= $commendbot_number ?>">

			<br />
			
			<label>Reportbot Number</label>
			<input required type="number" name="reportbot_number" class="form-control" value="<?= $reportbot_number ?>">
		</div>

		<hr />

		<div class="form-group">
			<label>Report script path</label>
			<input required type="text" name="report_path" class="form-control" value="<?= $report_path ?>">

			<br />
			
			<label>Commend Script Path</label>
			<input required type="text" name="commend_path" class="form-control" value="<?= $commend_path ?>">
		</div>

		<hr />

		<div class="form-group">
			<label>Log Prefix</label>
			<input required type="text" name="log_prefix" class="form-control" value="<?= $log_prefix ?>">

			<br />
			
			<label>Log Report Path</label>
			<input required type="text" name="report_log_path" class="form-control" value="<?= $report_log_path ?>">

			<br />
			
			<label>Log Commend Path</label>
			<input required type="text" name="commend_log_path" class="form-control" value="<?= $commend_log_path ?>">
		</div>

		<hr />

		<div class="form-group">
			<label>Reportbot Timer</label>
			<input required type="number" name="report_timer" class="form-control" value="<?= $report_timer ?>">

			<br />
			
			<label>Commendbot Timer</label>
			<input required type="number" name="commend_timer" class="form-control" value="<?= $commend_timer ?>">
		</div>

		<hr />

		<button class="pull-right btn btn-success" type="submit">Save</button>
	</form>
</div>


<?php require 'inc/footer.php'; ?>