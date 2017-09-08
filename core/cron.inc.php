<?php

$crontab = DB("SELECT * FROM common_crontab");

$cronjobs = [];
foreach ($crontab as $crontask) {
  if ($crontask['enabled']) {
    $lastExec = DB("SELECT * FROM common_cronhistory WHERE cronid=".$crontask['id']." ORDER BY `datetime` DESC LIMIT 1");
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
