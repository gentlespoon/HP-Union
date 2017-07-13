<?php

$r = DB("SELECT * FROM common_settings");
$settings = [];
foreach($r as $k => $v) {
  $settings[$v['key']] = $v['value'];
}

// $settings['template'] = "discuz-replica";
