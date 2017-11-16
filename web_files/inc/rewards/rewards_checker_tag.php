<?php

require('../config.php');
require('../db.php');
$gcfg = $pdo->query("SELECT * FROM rewards_config");
$tag = $gcfg->fetch()->tag;

$gpts = $pdo->query("SELECT * FROM rewards_config");
$pts = $gpts->fetch()->tag_pointsperday;

$req = $pdo->query("SELECT * FROM rewards_config");
$minconsecutif = $req->fetch()->tag_minconsecutivedays; 

echo "TAG: $tag <br />";
echo "PTS PER DAY: $pts<br />";
echo "MIN CONSECUTIVE DAYS: $minconsecutif<br />";
$req = $pdo->query('SELECT * FROM users WHERE (steamid IS NOT NULL OR steamid > 0)');
$count = $req->rowCount();
$rows = $req->fetchAll();

$req = $pdo->query('SELECT * FROM users WHERE (steamid IS NOT NULL OR steamid > 0)');


foreach ($rows as $key => $value) {
	$username_db = $value->{'username'};
	$uid = $value->{'id'};
	$steamid = $value->{'steamid'};

	$api_key  = $steam_api_key;
    $link = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$api_key.'&steamids='.$steamid.'?json=1');
    //echo $link;
    $myarray = json_decode($link, true);
    //var_dump($link);
	$username = $myarray['response']['players'][0]['personaname'];

	if(preg_match('/'.$tag.'/i', $username)){
		//USER HAVE TAG
		// WE NEED SOME INFO ABOUT HIM
		$q = $pdo->query("SELECT * FROM rewards_users WHERE uid = '$uid'");
		while($row = $q->fetch()){
			$tag_date_begin = $row->tag_datebegin;
			$tag_consecutivedays = $row->tag_consecutivedays;

			$date_now = date('Y-m-d');
			$date_now_ts = strtotime($date_now);

			// WE CONVERT OUR DATE INTO TIMESTAMP
			$date_begin = strtotime($tag_date_begin);
			$consecutivedays = $tag_consecutivedays * 24 * 60 * 60;

			$date_check = date('Y-m-d', $date_begin + $consecutivedays);
			
			$date_minus = $date_now_ts - (60*60*24);
			$date_minus = date('Y-m-d', $date_minus);
			
			if($date_now == $date_check){
				// Already added the day
				echo "<br />---------------<br /><b><p style='color: green;'>[OK] $username_db ($username) have the tag: $tag </p></b><br />---------------<br />";
			}else{
				if($date_check == $date_minus){
					if($tag_consecutivedays >= $minconsecutif){
						// We need to add 1 consecutive day
						// We need to add the points
						$pdo->query("UPDATE rewards_users SET tag_consecutivedays = tag_consecutivedays + 1, points = points + $pts WHERE uid = '$uid'");
						echo "$username +1 day, +$pts points<br />";
						echo "$minconsecutif >= $tag_consecutivedays<br />";
					}else{
						$pdo->query("UPDATE rewards_users SET tag_consecutivedays = tag_consecutivedays + 1 WHERE uid = '$uid'");
						echo "$username +1 day<br />";
					}
				}else{
					// We need to reset consecutive day

					$pdo->query("UPDATE rewards_users SET tag_consecutivedays = 0, tag_datebegin = CURDATE() WHERE uid = '$uid'");
					echo "$username consecutive days reseted<br />";
				}
				
			}
		}
		
	}else{
		// USER DOESNT HAVE THE TAG
		// WE RESET HIM
		echo "$username_db ($username) doesn't have the tag: $tag <br />" ;
		$pdo->query("UPDATE rewards_users SET tag_consecutivedays = 0, tag_datebegin = CURDATE() WHERE uid = '$uid'");
	}
}