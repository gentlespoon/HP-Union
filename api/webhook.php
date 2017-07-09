<?php

echo "<pre>";
if (isset($_GET['update'])) {
  echo shell_exec("./update.sh 2>&1");
} else {
  echo "?";
}
echo "</pre>";
