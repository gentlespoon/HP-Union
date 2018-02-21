<?php
/**
 * Copyright (c) 2018.
 * All rights reserved.
 * GentleSpoon
 * me@gentlespoon.com
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'].'/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}

$headVars = initHtmlHeadVars();

$htmlTemplates = [];
array_push($htmlTemplates, ROOT.'app/view/home.html');
include_once(ROOT.'app/view/mainTemplate.html');
