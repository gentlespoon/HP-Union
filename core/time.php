<?php

date_default_timezone_set("UTC");

function toUserTime($time, $format) {
  global $config;
  $dt = new DateTime();
  $dt->setTimestamp($time);
  $dt->setTimezone(new DateTimeZone($config['datetime']['usrtz']));
  $is = $dt->format($format);
  return $is;
}
