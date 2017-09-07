<?php

$crontab = DB("SELECT * FROM common_crontab");

$unExecutedCron = [];
foreach ($crontab as $crontask) {
  if ($crontask['enabled']) {
    $isExecuted = DB("SELECT * FROM common_cronhistory WHERE cronid=".$crontask['id']);
    if (empty($isExecuted)) {
      array_push($unExecutedCron, $crontask);
    }
  }
}
if (empty($unExecutedCron)) {
  // echo "All crontask executed";
} else {
  // echo "Crontasks to be executed:<br>";
  // printv($unExecutedCron);
}
