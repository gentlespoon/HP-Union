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


// your code here..

// you decide how to output...
// echo json_encode(['success' => 1, 'data' => 'sampledata']);

// you can also make another router...
$apiRouter = new router(ROOT.'app/route/api');
$apiRouter->setInvalidHttpCode(400);
echo $apiRouter->run();
