<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : heartbeat.api.php [HP-Union]
 * Date   : 2018-03-02
 * Time   : 23:59
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


switch(rand(1,6)) {
  case 1:
    apiPrint(1, "如果消息变了的话说明你眼花了…");
    break;
  case 2:
    apiPrint(1, "魁地奇什么的，不存在的！我们才刚做完心跳包呢！");
    break;
  case 3:
    apiPrint(1, "也可能是你手机抽了…");
    break;
  case 4:
    apiPrint(1, "如果你看到了这条消息…那说明你成功连接上服务器了…");
    break;
  case 5:
    apiPrint(1, "消息会变哦╮(╯▽╰)╭");
    break;
  default:
    apiPrint(1, "可能服务器出问题了…");
    break;
}

