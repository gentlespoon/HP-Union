<?php

define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
require(ROOT."core/core.php");

if (!array_key_exists('crontask', $_GET)) {
  header('HTTP/1.0 403 Forbidden');
  die();
}

require_once(ROOT."plugin/crontask/".$_GET['crontask'].".php");
