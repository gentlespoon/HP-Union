<?php

define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include(ROOT."core/core.php");

$body['forumlist'] = DB("SELECT * FROM forum_forum WHERE visible>0");



template("forum");
