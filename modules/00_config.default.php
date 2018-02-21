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


const dbConfig = [
  'host'      => '',
  'port'      => 3306,
  'username'  => '',
  'password'  => '',
  'dbname'    => '',
  'charset'   => 'utf8',
  'prefix'    => '',
];

const dtFormat = [
  'timezone'  => 'America/Los_Angeles',
  'iso'       => DateTime::ISO8601,
  'mysql'     => 'Y-m-d H:i:s',
  'date'      => [
    'zn-cn'     => 'Y-m-d',
    'en-us'     => 'm/d/Y',
  ],
  'time'      => [
    'zh-cn'     => 'H:i:s',
    'en-us'     => 'h:i:s A',
  ],
];


// use MeekroDB in procedural way
DB::$host = dbConfig['host'];
DB::$port = dbConfig['port'];
DB::$user = dbConfig['username'];
DB::$password = dbConfig['password'];
DB::$dbName = dbConfig['dbname'];
DB::$encoding = dbConfig['charset'];