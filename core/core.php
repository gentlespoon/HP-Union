<?php

error_reporting(E_ALL);
if(!isset($_SESSION)) {
  session_start();
}

if (!defined("ROOT")) {
  define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
}

if (!isset($_starttime)) {
  $_starttime = microtime(true);
}


require_once(ROOT."language.php");
require_once(ROOT."config/config.php");
require_once(ROOT."core/time.php");
require_once(ROOT."core/func.php");
require_once(ROOT."develop.php");

//  $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];


// Connect to database
if (!isset($db)) {
  $dsn = "mysql:host=".$config['db']['host'].";dbname=".$config['db']['dtbs'].";charset=".$config['db']['char'];
  $username = $config['db']['user'];
  $password = $config['db']['pass'];
  try {
    $db = new PDO($dsn, $username, $password);
  } catch (Exception $error) {
    die("Cannot connect to database.");
  }
}

// Get site settings
$r = DB("SELECT * FROM common_settings");
$settings = [];
foreach($r as $k => $v) {
  $settings[$v['key']] = $v['value'];
}


// Initialize output
if (!isset($body)) {
  $body = [
    "text" => "",
  ];
}

if (!array_key_exists("act", $_GET)) {
  $_GET['act'] = "";
}

// Initialize user session
if (!array_key_exists("uid", $_SESSION)) {
  $_SESSION['uid'] = 0;
}


// Retrieve current user information
$member = getUserInfo();
