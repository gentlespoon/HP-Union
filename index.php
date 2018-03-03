<?php
/**
 * Copyright (c) 2018.
 * All rights reserved.
 * GentleSpoon
 * me@gentlespoon.com
 */


/**
 * Entry of the app
 */

error_reporting(E_ALL);

// measure running time
$runtime['start'] = microtime(true);
$GLOBALS['now'] = time();

// import framework
require_once($_SERVER['DOCUMENT_ROOT'] . '/framework/core.php');

// import the app files
foreach (get_file_list(ROOT.'app/modules/') as $filename) {
  if (strpos($filename, 'default') === false)
    require_once(ROOT.'app/modules/'.$filename);
}

// load site settings from database
$results = DB::query('SELECT name, content FROM '.DBtable('settings').' WHERE autoload=1');
$GLOBALS['settings'] = [];
foreach($results as $settingsEntry) {
  $GLOBALS['settings'][$settingsEntry['name']] = $settingsEntry['content'];
}

// set debug mode
if ($GLOBALS['settings']['debug_mode']) {
//  DB::debugMode();
  $_POST = $_REQUEST;
  error_reporting(E_ALL);
} else {
  error_reporting(E_ERROR | E_WARNING);
}

// set server timezone
date_default_timezone_set($GLOBALS['settings']['timezone']);

// router
$path = isset($_GET['path']) ? $_GET['path'] : 'home';
$rootRouter = new router(ROOT . 'app/route/');

$output = $rootRouter->run();

// runtime
$runtime['end'] = microtime(true);
$runtime['span'] = $runtime['end'] - $runtime['start'];
$runtime['human'] = number_format($runtime['span'], 4);

$output = str_replace('{{ CONSOLELOGS }}', LOG::printConsoleLogs(), $output);
$output = str_replace('{{ RUNTIME }}', $runtime['human'], $output);

HTTP::send($output);
