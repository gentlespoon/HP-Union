<?php
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
if (array_key_exists("qq", $_GET)) {
  $url = 'http://ptlogin2.qq.com/getface?appid=1006102&uin='.$_GET['qq'].'&imgtype=4';
  $avatar = file_get_contents($url);
  $start_avatar = strpos($avatar, "http:");
  if ($start_avatar === false) {
    $avatar = "Failed"; // failed to get avatar;
  } else {
    $avatar = substr($avatar, $start_avatar, -4);
  }
  echo $avatar;
} else {
  echo "?";
}
