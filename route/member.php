<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : member.php [fGame]
 * Date   : 2018-01-31
 * Time   : 18:24
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . "/");
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


// you can also make another router...
$memberRouter = new router(ROOT.'app/route/member/');
$memberRouter->setInvalidHttpCode(400);
echo $memberRouter->run();
