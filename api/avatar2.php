<?php
error_reporting(E_ALL);
$avatar_url = "http://hp-union.com/static/images/default_avatar.jpg";

$url = 'http://ptlogin2.qq.com/getface?appid=1006102&uin='.$_GET['qq'].'&imgtype=4';
$avatar_response = file_get_contents($url);
$start_avatar = strpos($avatar_response, "http:");
if ($start_avatar !== false) {
  $avatar_url = str_replace("\\", "", substr($avatar_response, $start_avatar, -4));
}
// $avatar = file_get_contents($avatar_url);
echo $avatar_url;
