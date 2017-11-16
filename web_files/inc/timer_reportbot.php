<?php
require 'config.php';
require 'db.php';
require 'language.php';
for($i = 1; $i <= $reportbot_number; $i++){
	$req = $pdo->prepare('SELECT * FROM reported_list WHERE (reportbot_id = :id AND NOW() < datum + interval :timer hour)');
	$req->bindParam(':id', $i);
	$req->bindParam(':timer', $report_timer);
	$req->execute();
	if($req->rowCount() == 0){
		// we can use a reportbot
		$reportbot_to_use = $i;
		$ready_to_report = '1';
		break;
	}else{
		$ready_to_report = '0';
	}

	
}
if($ready_to_report == '0'){
	$reportbot = array(); // We define reportsbot as an array to store all of our reportbot which are not ready
	
	for($i = 1; $i <= $reportbot_number; $i++){
		$req = $pdo->prepare('SELECT * FROM reported_list WHERE (reportbot_id = :id AND NOW() < datum + interval :timer hour)');
		$req->bindParam(':id', $i);
		$req->bindParam(':timer', $report_timer);
		$req->execute();
		while($row = $req->fetch()){
			if($req->rowCount() == 1){
				$reportbot[$i] = $row->datum;
			}
		}
	}


	// We get current date
	$req = $pdo->query("SELECT NOW()");
	$req->execute();
	$date_now = $req->fetchColumn();

	$date_now = new Datetime($date_now);
	$date_now = $date_now->getTimestamp();

	$cooldown_report = array_search(min($reportbot), $reportbot); // Report Cooldown
	$cooldown_report = $reportbot[$cooldown_report];
	// Now we get the cooldown of this reportbot
	$cooldown_report = date('Y-m-d H:i:s', strtotime('+'.$report_timer . ' hours', strtotime($cooldown_report)));

	$cooldown_report = new Datetime($cooldown_report);
	$cooldown_report = $cooldown_report->getTimestamp();
	$diff_reportbot = $date_now - $cooldown_report;


	$init = $diff_reportbot;
	$hours = floor($init / 3600);
	$minutes = floor(($init/60) % 60);
	$seconds = $init % 60;

	$hours = abs($hours) - 1;
	$minutes = abs($minutes);
	$seconds = abs($seconds);
}else{
	$hours = 0;
	$minutes = 0;
	$seconds = 1;
}
?>

<span id="cooldown"></span>

<script>



var hours = <?=$hours?>;
var minutes = <?=$minutes?>;
var seconds = <?=$seconds?>;

var hours_text = "<?=$language['hours']?>";
var minutes_text = "<?=$language['minutes']?>";
var seconds_text = "<?=$language['seconds']?>";
var to_wait = "<?=$language['to_wait']?>";

var x = document.getElementById('cooldown');

x.innerHTML = "<center><div class='alert alert-warning'>Loading information...</div></center>";



var timer = setInterval(function(){
	seconds--;

	if(seconds === 0){
		if(seconds === 0 && minutes === 0){
			// hours -1
			hours = hours - 1;
			minutes = 60;
			seconds = 60;
		}

		// mins -1
		minutes = minutes - 1;
		seconds = 60;

	}

	if(hours < 0){
		clearInterval(timer);
		seconds = 0;
		hours = 0;
		minutes = 0;
		x.innerHTML = "<center><div class='alert alert-success'>Ready</div></center>";

	}else{
		x.innerHTML = "<center><div class='alert alert-danger'>" + hours + " " + hours_text + " " + minutes + " " + minutes_text + " " + seconds + " " + seconds_text + " " +to_wait + "</div></center>";
	}




}, 1000);

</script>