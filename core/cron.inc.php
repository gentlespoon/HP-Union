<?php

$crontab = DB::query("SELECT * FROM common_crontab");

$cronjobs = [];
foreach ($crontab as $crontask) {
  if ($crontask['enabled']) {
    $lastExec = DB::query("SELECT * FROM common_cronhistory WHERE cronid=%i ORDER BY `datetime` DESC LIMIT 1", $crontask['id']);
    // if never executed
    if (empty($lastExec)) {
      array_push($cronjobs, $crontask);
      continue;
    } else{

      if (($lastExec[0]['datetime'] - $now) > (24*3600)) {
      }
    }
  }
}
if (empty($cronjobs)) {
  // echo "All crontask executed";
} else {
  // echo "Crontasks to be executed:<br>";
  // printv($unExecutedCron);
}
