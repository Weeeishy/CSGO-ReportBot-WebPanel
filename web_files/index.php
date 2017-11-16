<?php $page = "Homepage"; ?>
<?php require 'inc/functions.php'; ?>
<?php require 'inc/config.php'; ?>
<?php require 'inc/header.php'; ?>
<?php require 'inc/db.php'; ?>
<?php require 'inc/language.php';

require_once "inc/jbbcode/Parser.php";

$parser = new JBBCode\Parser();
$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());

?>

<div class="container">
   
<div class="row">
  <center><h1>Last news</h1></center>
  <hr />
  <div id="myCarousel" class="carousel slide" data-ride="carousel">

    <div class="carousel-inner">
      
      <?php
      $req = $pdo->query("SELECT * FROM news ORDER BY id DESC LIMIT 5;");
      $news = $req->fetchAll();
      if(empty($news)){
        echo "<blockquote>
        <p>No news at the moment.</p>
        </blockquote>";
      }
      
    foreach ($news as $key => $data):?>
        <?php

        $req = $pdo->query("SELECT * FROM users WHERE id = '$data->addedby_uid'");
        
        $author = $req->fetchAll();
        
        
        if(empty($author)){
            $author = "Unknown";
        }else{
            $author = (string) $author[0]->username;
        }
        $parser->parse($data->message);
        $message = html_entity_decode($parser->getAsHtml())
        //$author = $author->username;

        //debug($author);
        ?>
        <?php if($key == 0): ?>
        <div class="item active">
           
                <blockquote>
                    <center>
                        <a><?= $data->title?></a>
                        <p><?= $message ?></p>
                        <!--<footer>By <?= $author ?> - <?= $data->date ?></footer>-->
                    </center>
                </blockquote>
             
        </div>
        <?php else: ?>
            <div class="item">
                <blockquote>
                    <center>
                        <a><?= $data->title?></a>
                        <p><?= $message ?></p>
                        <!--<footer>By <?= $author ?> - <?= $data->date ?></footer>-->
                    </center>
                </blockquote>
            </div>
        <?php endif; ?>
      <?php endforeach;
      ?>

      
    </div>

    
  </div>
</div>
<?php include 'inc/home_last.php'; // Include last commended/reported ?>

    <div class="row text-center">
        <div class="col-lg-10 col-lg-offset-1">
            <h2><?= $language['home_our_service']; ?></h2>
            <hr class="small">
            <div class="row">
                <?php
                $req = $pdo->prepare('SELECT * FROM homepage');
                $req->execute();
                $introduction_element = $req->rowCount();
                while($row = $req->fetch()){
                  echo '
                  <div class="col-md-'. 12 / $introduction_element .' col-sm-6">
                    <div class="service-item">
                        <span class="fa-stack fa-4x">
                        <i class="fa '.$row->icon.' fa-stack-1x text-primary"></i>
                    </span>
                        <h4>
                            <strong>'.$row->title.'</strong>
                        </h4>
                        <p>'.$row->text.'</p>
                        
                    </div>
                </div>';
                }

                ?>
            </div>
        </div>
    </div>
</div>




<?php require 'inc/footer.php'; ?>

<script>
    $('.carousel').carousel({
  interval: 5000
})

</script>