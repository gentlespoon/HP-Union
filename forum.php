<?php

define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include(ROOT."core/core.php");

$forum_hierarchy = [];
$forumlist = DB("SELECT * FROM forum_forum WHERE visible>0");

// construct top level forum hierarchy
foreach ($forumlist as $k => $forum) {
  if ($forum['parent_fid'] == 0) {
    $forum_hierarchy[$forum['fid']] = $forum;
    $forum_hierarchy[$forum['fid']]['subforum'] = [];
  }
}

// construct second level forum hierarchy
foreach ($forumlist as $k => $forum) {
  if ($forum['parent_fid'] > 0) {
    $forum_hierarchy[$forum['parent_fid']]['subforum'][$forum['fid']] = $forum;
  }
}


printv($forum_hierarchy);

$body['forumlist'] = $forum_hierarchy;
template("forum");
