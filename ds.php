<?php

$time = 3;
$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
/*
  function
*/

include_once './check_mysql_status.php';

sleep($time);
file_get_contents($url);
