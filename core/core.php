<?php

error_reporting(E_ALL);
if(!isset($_SESSION)) {
  session_start();
}

if (!defined("ROOT")) {
  define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
}

$_starttime = microtime(true);
// echo "START".$_starttime;


include_once(ROOT."config/config.php");
include_once(ROOT."core/time.php");
include_once(ROOT."core/func.php");
include_once(ROOT."language.php");
include_once(ROOT."develop.php");

//  $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];


// Connect to database
if (!isset($db)) {
  $dsn = "mysql:host=".$config['db']['host'].";dbname=".$config['db']['dtbs'].";charset=".$config['db']['char'];
  $username = $config['db']['user'];
  $password = $config['db']['pass'];
  $db = new PDO($dsn, $username, $password);
}

include_once(ROOT."core/settings.php");

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
}


// Retrieve current user information
if ($_SESSION['uid'] > 0) {
  $member = DB("SELECT username, qq FROM member WHERE uid=:uid", [":uid" => $_SESSION['uid']]);
  if (isset($member[0])) {
    $member = $member[0];
  } else {
   $member = ["username" => $lang['not-logged-in'], "qq" => 0];
  }
} else {
  $member = ["username" => $lang['not-logged-in'], "qq" => 0];
}
