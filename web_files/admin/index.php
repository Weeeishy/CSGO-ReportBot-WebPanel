<?php
require_once '../inc/config.php';
require_once '../inc/functions.php';
require_once '../inc/functions.php';
require_once 'inc/header.php';
require_once 'inc/db.php';
is_admin();
?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<div class="col-lg-3">
<h4>Administration</h4>
<ul>

	<li><a href="configuration.php">Website config</a></li>
	<hr />
	
	<li><a href="faq.php">Frequently Asked Questions</a></li>
	<li><a href="rewards.php">Rewards manager</a></li>
	<li><a href="news.php">News</a></li>
	
	<hr />
	
	<li><a href="history.php">Token history</a></li>
    <li><a href="token.php">Token Generation</a></li>
	<li><a href="mass_token.php">Mass Token Generator </a></li>
	<li><a href="whitelist.php">Whitelist </a></li>
	
	<hr />
	
    <li><a href="language.php">Language</a></li>
    <li><a href="home.php">Edit home Page </a></li>
    <li><a href="nav.php">Edit Nav </a></li>
	<li><a href="theme.php">Theme editor</a></li>
	
	<hr />
    <li><a href="support.php">Support center</a></li>
	<li><a href="support_category.php">Support Category</a></li>
	
	<hr />
    <li><a href="sell_categories.php">Product categories</a></li>
	<li><a href="sell.php">Products</a></li>

	<hr />
   
	<li><a href="contact.php">Contact editor</a></li>
	<li><a href="memberlist.php">Memberlist</a></li>
	<li><a href="referral.php">Referral list</a></li>


	




</ul>
</div>

<div class="col-lg-9">
    <center><h1>Graph</h1></center>

    <?php include 'inc/graph_report.php'; ?>

    <?php include 'inc/graph_commend.php'; ?>
</div>

<div class="col-md-12">
<hr/>
</div>

<?php include 'inc/footer.php'; ?>