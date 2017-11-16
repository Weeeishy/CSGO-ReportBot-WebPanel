<meta charset="UTF-8">
<link href="../css/default/bootstrap.min.css" rel="stylesheet">
<?php
session_start();

if(!isset($_SESSION['auth']) OR $_SESSION['auth']->is_admin != 1){
	$_SESSION['flash']['danger'] = "You are not admin.";
	header("Location: login.php");
	exit();	
}

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

	$version = json_decode(file_get_contents("http://api.cs-report.me/version.php"))->{'version'};
	
		require 'inc/config.php';
		require 'inc/db.php';

		$req = $pdo->prepare("INSERT INTO config SET current_version = :current_version, licence_key = :licence_key, steam_api_key = :steam_api_key, website_navtitle = :website_navtitle, website_title = :website_title, captcha_secret_key = :secret_key, captcha_website_key = :website_key, reportbot_number = :reportbot_number, commendbot_number = :commendbot_number, log_prefix = :log_prefix, report_path = :report_path, report_log_path = :report_log_path, commend_path = :commend_path, commend_log_path = :commend_log_path, report_timer = :report_timer, commend_timer = :commend_timer");

		$req->bindParam(":current_version", $version);
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

		die("<b>Configuration edited. Click <a href='index.php'>here</a> to redirect to your website.</b>");


}

?>
<div class="container">
	<div class="row">
		
		<form method="post">

			<h1>Website configuration</h1>
			<button class="pull-right btn btn-success" type="submit">Save</button>
			<h4>Here, you can edit your website settings.</h4>

			<div class="form-group">
				<label>Licence Key</label>
				<input required type="text" name="licence_key" class="form-control" >

				<br />

				<label>Steam API Key</label> <a href="https://steamcommunity.com/dev/apikey" target="_blank">Get your API key</a>
				<input required type="text" name="steam_api_key" class="form-control" >
			</div>

			<hr />

			<div class="form-group">
				<label>Website Nav Title</label> <small>Name of your website in the navbar</small>
				<input required type="text" name="website_navtitle" class="form-control" >

				<br />
				
				<label>Website Title</label>	<small>Name of your website in the webbrowser tab</small>
				<input required type="text" name="website_title" class="form-control" >
			</div>

			<hr />

			<div class="form-group">
				<label>Captcha Website Key</label> <small>Google recaptcha v2 <a href="https://www.google.com/recaptcha/admin" target='_blank'>Click here</a></small>
				<input required type="text" name="website_key" class="form-control" >
			</div>

			<hr />

			<div class="form-group">
				<label>Captcha Secret Key</label>
				<input required type="text" name="secret_key" class="form-control" >
			</div>

			<hr />

			<div class="form-group">
				<label>Commendbot Number</label> <small>How many commendbot do you want to set</small>
				<input required type="number" name="commendbot_number" class="form-control" >

				<br />
				
				<label>Reportbot Number</label> <small>How many reportbot do you want to set</small>
				<input required type="number" name="reportbot_number" class="form-control">
			</div>

			<hr />

			<div class="form-group">
				<label>Report script path</label>
				<input required type="text" name="report_path" value="/var/report-bot/" class="form-control" >

				<br />
				
				<label>Commend Script Path</label>
				<input required type="text" name="commend_path" value="/var/report-bot/" class="form-control" >
			</div>

			<hr />

			<div class="form-group">
				<label>Log Prefix</label> <small>In log, it will display [mybot.com - (1)] if set to mybot.com</small>
				<input required type="text" name="log_prefix" placeholder="mybot.com" class="form-control" >

				<br />
				
				<label>Log Report Path</label>
				<input required type="text" name="report_log_path" value="/var/report-bot/logs_report/" class="form-control" >

				<br />
				
				<label>Log Commend Path</label>
				<input required type="text" name="commend_log_path" value="/var/report-bot/logs_commend/" class="form-control" >
			</div>

			<hr />

			<div class="form-group">
				<label>Reportbot Timer</label> <small>Cooldown between each report</small>
				<input required type="number" name="report_timer" class="form-control" >

				<br />
				
				<label>Commendbot Timer</label> <small>Cooldown between each commends</small>
				<input required type="number" name="commend_timer" class="form-control" >
			</div>

			<hr />

			<button class="pull-right btn btn-success" type="submit">Save</button>
		</form>

	</div>
</div>