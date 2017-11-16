<?php
require('config.php');
require('db.php');

$req = $pdo->query("DELETE FROM tokens WHERE token_use = '0'");


$result = $pdo->query("SELECT * FROM reported_list WHERE ow=false OR vac=false ORDER BY id DESC LIMIT 5000;");
$ids;
$count   = 0;
$counter = 1;
$rows = [];

while ($row = $result->fetch()) {

    $rows[] = $row;
}

$s = sizeof($rows);
foreach ($rows as $row) {
    
    $steamid = $row->steamid;
    if ($count == 0) {
        $ids   = $steamid;
        $count = $count + 1;
    } else {
        
        if (strpos($ids, $steamid) !== false) {
            $s = $s - 1;
        } else {
            $counter = $counter + 1;
            $ids     = $ids . ',' . $steamid;
            $count   = $count + 1;
        }
    }

    
    if ($count > 10 || $counter == $s) {
        $key  = $steam_api_key;
        $link = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerBans/v1/?key=' . $key . '&steamids=' . $ids . '&format=json');
        //echo $link;
        $myarray = json_decode($link, true);
        echo '<pre>'.$link.'</pre>';
        
        
        
        for ($i = 0; $i < $count; $i++) {
            
                @$ow         = $myarray['players'][$i]['NumberOfGameBans'];
                @$vac        = $myarray['players'][$i]['VACBanned'];
                @$steamid    = $myarray['players'][$i]['SteamId'];
                @$daylastban = $myarray['players'][$i]['DaysSinceLastBan'];
               
              
                //Now, we check if user is OW
                if($ow > 0){
					if($daylastban < 10){
                    // WE CHECK IF USER IS ALREADY MARKED AS OWED
						$req = $pdo->prepare('SELECT * FROM reported_list WHERE (steamid = ? AND ow = "true")');
						$req->execute([$steamid]);
						if($req->rowCount() == 0){
							// USER NOT MARKED AS OW
							$req = $pdo->prepare('UPDATE reported_list SET ow = "true", ban_date = CURRENT_TIMESTAMP WHERE steamid = ?');
							$req->execute([$steamid]);

							$req = $pdo->prepare('UPDATE reported_list SET ow = "true", ban_date = CURRENT_TIMESTAMP WHERE steamid = ?');
							$req->execute([$steamid]);


						}
					}
                }

                // Now, we chec if user is VAC
                if($vac > 0){
					if($daylastban < 10){
						// CHECK IF USER ALREADY VACCED
						$req = $pdo->prepare('SELECT * FROM reported_list WHERE (steamid = ? AND vac = "true")');
						$req->execute([$steamid]);
						if($req->rowCount() == 0){
							// USER NOT MARKED AS VAC
							$req = $pdo->prepare('UPDATE reported_list SET vac = "true", ban_date = CURRENT_TIMESTAMP WHERE steamid = ?');
							$req->execute([$steamid]);

							$req = $pdo->prepare('UPDATE reported_list SET vac = "true", ban_date = CURRENT_TIMESTAMP WHERE steamid = ?');
							$req->execute([$steamid]);

						}
					}
                }
                
            

        }
    }
}

?>