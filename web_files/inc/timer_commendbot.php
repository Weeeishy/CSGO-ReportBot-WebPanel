<?php
require 'config.php';
require 'db.php';
for($i = 1; $i <= $commendbot_number; $i++){
	$req = $pdo->prepare('SELECT * FROM commended_list WHERE (commendbot_id = :id AND NOW() < datum + interval :timer hour)');
	$req->bindParam(':id', $i);
	$req->bindParam(':timer', $commend_timer);
	$req->execute();
	if($req->rowCount() == 0){
		// we can use a commendbot
		$ready_to_report = '1';
		break;
	}else{
		$ready_to_report = '0';
	}

	
}
if($commendbot_number == 0){
	
	echo '<div class="alert alert-danger">There is 0 commendbot in the config file</div>';
	require 'inc/footer.php';
	die();
}
if($ready_to_report == '0'){
	$commendbot = array(); // We define commendbot as an array to store all of our commendbot which are not ready
	
	for($i = 1; $i <= $commendbot_number; $i++){
		$req = $pdo->prepare('SELECT * FROM commended_list WHERE (commendbot_id = :id AND NOW() < datum + interval :timer hour)');
		$req->bindParam(':id', $i);
		$req->bindParam(':timer', $commend_timer);
		$req->execute();
		while($row = $req->fetch()){
			$commendbot[$i] = $row->datum;		
		}
	}


	// We get current date
	$req = $pdo->query("SELECT NOW()");
	$req->execute();
	$date_now = $req->fetchColumn();

	$date_now = new Datetime($date_now);
	$date_now = $date_now->getTimestamp();

	$cooldown_commend = array_search(min($commendbot), $commendbot); // Report Cooldown
	$cooldown_commend = $commendbot[$cooldown_commend];
	// Now we get the cooldown of this commendbot
	$cooldown_commend = date('Y-m-d H:i:s', strtotime('+'.$commend_timer . ' hours', strtotime($cooldown_commend)));

	$cooldown_commend = new Datetime($cooldown_commend);
	$cooldown_commend = $cooldown_commend->getTimestamp();
	$diff_commendbot = $date_now - $cooldown_commend;


	$init = $diff_commendbot;
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
} ?>

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