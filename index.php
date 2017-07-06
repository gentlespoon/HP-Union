<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include_once(ROOT."core/core.php");










$title = $lang['site']['name'];
template("index");
