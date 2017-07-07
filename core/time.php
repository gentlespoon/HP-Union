<?php

date_default_timezone_set("UTC");

function toUserTime($time, $format=false) {
  global $config;
  if (!$format) {
    $format = $config['datetime']['iso'];
  }
  $dt = new DateTime();
  $dt->setTimestamp($time);
  $dt->setTimezone(new DateTimeZone($config['datetime']['usrtz']));
  $is = $dt->format($format);
  return $is;
}
