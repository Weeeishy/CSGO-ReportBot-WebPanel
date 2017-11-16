 <?php 

 require_once('../inc/config.php');
 require_once('../inc/db.php'); ?> 
<hr />

<style>
.language {
    display: inline-block;
}
</style>

	<footer>        
        <p>&copy; 2017 <?= $website_navtitle ?> - v<?=$version?><br />
        <p class="language">Language:</p>
        <?php
        $req = $pdo->prepare("SELECT * FROM language");
        $req->execute();
        $language_number = $req->rowCount();
        $id = 0;
        while($row = $req->fetch()){
            $id = $id + 1;
           if($id > 1){
            echo ' | ';
           }
            echo '
            <form class="language" method="post" name="'.$row->language_name.'" action="">
        
            <input type="hidden" name="lang" value="'.$row->lang_code.'">
            <img src="../img/lang/'.$row->lang_icon.'.png" width="20" height="20" onclick="document.forms[\''.$row->language_name.'\'].submit();" title="'.$row->language_name.'">
        </form>';
        }


        ?>
		
        </p>
		<p class="pull-right"><i>Created with &hearts; by <a href="http://steamcommunity.com/profiles/76561198138326581/" target="_blank">Yellow</a></i></p>

    </footer>
    </div>
    <!-- /.container -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  
<?php
if(isset($_SESSION['auth']) AND empty($_SESSION['auth']->email)) : ?> 
<script type="text/javascript">
    $(window).on('load',function(){
        $('#email_option').modal('show');
    });
</script>
<?php endif; ?>
</body>
 
</html>