<?php

error_reporting(E_ALL);

if (!defined("ROOT")) {
  define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
}
include_once(ROOT."config/config.php");
include_once("time.php");
include_once("func.php");
include_once(ROOT."lang.php");

//  $_SERVER['REQUEST_SCHEME']."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$dsn = "mysql:host=".$config['db']['host'].";dbname=".$config['db']['dtbs'].";charset=".$config['db']['char'];
$username = $config['db']['user'];
$password = $config['db']['pass'];

$db = new PDO($dsn, $username, $password);

function dbquery($sql, $param) {
  global $db;
  if ($sth = $db->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY])) {
    $sth->execute($param);
    $rs = $sth->fetchAll();
    return $rs;
  } else {
    echo $db->error;
  }
}

// $rs = dbquery("SELECT * FROM common_member WHERE username= :username OR uid= :uid", [":username" => "尖头勺子", ":uid" => 2]);
// print_r($rs);


function template($file) {
  global $title;
  include_once(ROOT."templates/common_header_html.htm");
  include_once(ROOT."templates/common_header_visual.htm");
  include_once(ROOT."templates/".$file.".htm");
  include_once(ROOT."templates/common_footer_visual.htm");
  include_once(ROOT."templates/common_footer_html.htm");
}
