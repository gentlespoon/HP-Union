<?php

error_reporting(E_ALL);
session_start();

if (!defined("ROOT")) {
  define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
}
include_once(ROOT."config/config.php");
include_once("time.php");
include_once("func.php");
include_once(ROOT."lang.php");

//  $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];


// Connect to database
$dsn = "mysql:host=".$config['db']['host'].";dbname=".$config['db']['dtbs'].";charset=".$config['db']['char'];
$username = $config['db']['user'];
$password = $config['db']['pass'];

$db = new PDO($dsn, $username, $password);

// Initialize output
$body = [
  "text" => "",
];

// Process submitted info
foreach ($_GET as $k => $v) {
  $v = trim($v);
}
foreach ($_GET as $k => $v) {
  $v = trim($v);
}

if (!array_key_exists("act", $_GET)) {
  $_GET['act'] = "";
}

// Initialize user session
if (!array_key_exists("uid", $_SESSION)) {
  $_SESSION['uid'] = 0;
  $_SESSION['username'] = 0;
}
