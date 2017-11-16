<?php

if(isset($_GET['l'])){
	 $steamid = $_GET['l'];
            if (strlen($steamid) != 17) {
                echo '<br><font color=red>Error: SteamID not Valid?</font>';
            } else {
                if (!ctype_digit($steamid)) {
                    echo '<br><font color=red>Error: SteamID not Valid?</font>';
                } else {
					echo '<h2>Output of "'.$steamid.'"</h2><br>';
                    $filename = $commend_log_path.$steamid.'.txt';
                    if (file_exists($filename)) {
                        if (is_readable($filename)) {
                            $handle = fopen($filename, 'r');
                            if (filesize($filename) > 0) {
                                $contents = fread($handle, filesize($filename));
                                fclose($handle);
                                $contents = str_replace('\r\n', "\r\n", $contents);
								echo '<div class="panel-body">';
								echo '<div class="row">';
                                echo ' <textarea data-autoresize class="form-control vresize" id="info" rows="20">'.$contents.'</textarea>'; 
								echo '</div>';
								echo '</div>';
								echo '<meta http-equiv="refresh" content="5">';

                            } else {
								echo '<div class="panel-body">';
								echo '<div class="row">';
								echo ' <textarea data-autoresize class="form-control vresize" id="info" rows="20"></textarea>'; 
								echo '</div>';
								echo '</div>';
								echo '<meta http-equiv="refresh" content="5">';

								
							}    
                        }
                    } else {
                        echo '<br><font color=red>Cant get any Logs for SteamID: "'.$steamid.'"</font>';
                    }
                }
            }
        
    }
