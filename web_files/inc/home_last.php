<?php require_once 'language.php'; ?>
    <center><h1><?= $language['home_last_stats']; ?></h1></center>
    <hr/>
    <div class="col-md-6 table-responsive">
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>SteamID</th>
                    <th>Reportbot ID</th>
                    <th>Date</th>
					<th>Inventory Value</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            $req = $pdo->query('SELECT * FROM reported_list ORDER BY id DESC LIMIT 10');
			

            while($row = $req->fetch()){
				if(($row->inventory_value != 'Private') and ($row->inventory_value != "Unknown")){
					$inventory_value = "<font style='color: red;'>$row->inventory_value</font>";
				}else{
					$inventory_value = $row->inventory_value;
				}
                echo '
                <tr>
                    <center><td>'.$row->id.'</td></center>
                    <center><td>'.$row->steamid.'</td></center>
                    <center><td>'.$row->reportbot_id.'</td></center>
                    <center><td>'.$row->datum.'</td></center>
					<center><td>'.$inventory_value.'</td></center>
                </tr>';

            }
            ?>
            </tbody>
        </table>
    </div>

    <div class="col-md-6 table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>SteamID</th>
                    <th>Commendbot ID</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            <?php 
                $req = $pdo->query('SELECT * FROM commended_list ORDER BY id DESC LIMIT 10');


                while($row = $req->fetch()){
                echo '
                <tr>
                <center><td>'.$row->id.'</td></center>
                <center><td>'.$row->steamid.'</td></center>
                <center><td>'.$row->commendbot_id.'</td></center>
                <center><td>'.$row->datum.'</td></center>
                </tr>';

            }
            ?>
            </tbody>
        </table>
    </div>
