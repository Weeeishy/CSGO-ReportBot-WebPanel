<?php

if(isset($_POST['lang'])){
	$language = htmlentities($_POST['lang']);
	$_SESSION['lang'] = $language;

}

if(!isset($_SESSION['lang'])){
	$dir = dirname(__FILE__);
	$dir = str_replace('inc','',$dir);
    include($dir .'/lang/en.php');
    $language = $lang['en'];	
}

if(isset($_SESSION['lang'])){
	$dir = dirname(__FILE__);
	$dir = str_replace('inc','',$dir);
  	include($dir . '/lang/' . $_SESSION['lang'] . '.php');
    $language = $lang[$_SESSION['lang']];

}else{
	$dir = dirname(__FILE__);
	$dir = str_replace('inc','',$dir);
    include($dir .'/lang/en.php');
    $language = $lang['en'];

}
