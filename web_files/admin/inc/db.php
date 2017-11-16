<?php

$pdo = new PDO("mysql:dbname=$DB_DATABASE;host=$DB_HOSTNAME;charset=UTF8", "$DB_USER", "$DB_PASSWORD");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);