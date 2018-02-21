<?php
/**
 * Author : GentleSpoon <me.github@gentlespoon.com>
 *
 * File   : 00_helper.php [localhost]
 * Date   : 2018-02-09
 * Time   : 01:26
 */


if (!defined('ROOT')) {
  define('ROOT', $_SERVER['DOCUMENT_ROOT'] . '/');
  require_once(ROOT . 'framework/http.php');
  HTTP::error(403);
}


/**
 * Print human readable array in a <pre>
 * @param  array
 * @param  boolean, true = return as string, false = echo
 * @return string or echo
 */
function print_array($arr, $echo=1) {
  if (is_array($arr)) {
    $buf = "<pre>";
    ob_start();
    print_r($arr);
    // var_export($arr);
    $buf .= ob_get_clean();
    $buf .= "</pre>";
    if ($echo) {
      echo $buf;
    }
    return $buf;
  } else {
    echo "Parameter is not an array.";
  }
}



function apiPrint($ok, $var) {
  $a = [
    'ok' => $ok,
    'data' => $var,
  ];
  echo json_encode($a);
}


/**
 * Add prefix to database table
 */
function DBtable($table) {
  return dbConfig['prefix'].$table;
}


function initHtmlHeadVars() {
  return [
    'title' => '',
    'keywords' => $GLOBALS['settings']['global_keywords'],
    'description' => $GLOBALS['settings']['global_description']
  ];
}

