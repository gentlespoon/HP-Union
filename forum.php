<?php

define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include(ROOT."core/core.php");

switch ($_GET['act']) {


  case "forum":
    $forum = DB("SELECT * FROM forum_forum WHERE fid = :fid and visible > 0", [":fid" => $_GET["fid"]]);
    if (empty($forum)) {
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['invalid-forum-id'];
      break;
    }
    $forum[0]['subforum'] = DB("SELECT * FROM forum_forum WHERE parent_fid = :fid", [":fid" => $_GET["fid"]]);

    $threads = DB("SELECT forum_thread.tid, forum_thread.title, member.uid, member.username, forum_thread.sendtime FROM forum_thread LEFT JOIN member ON forum_thread.author_uid = member.uid WHERE forum_thread.forum_id = :fid", [":fid" => $_GET["fid"]]);

    foreach ($threads as $k => $v) {
      $threads[$k]['sendtime'] = toUserTime($v['sendtime']);
    }
    
    $body['forum'] = $forum;
    $body['threads'] = $threads;
    break;

  case "thread":
    $thread = DB("SELECT * FROM forum_thread WHERE tid = :tid", [":tid" => $_GET['tid']]);
    if (empty($thread)) {
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['invalid-thread-id'];
      break;
    }

    $posts = DB("SELECT forum_post.title, forum_post.content, forum_post.author_uid, member.username, forum_post.sendtime FROM forum_post LEFT JOIN member ON forum_post.author_uid = member.uid WHERE forum_post.thread_tid = :tid ORDER BY forum_post.pid", [":tid" => $_GET['tid']]);
    foreach ($posts as $k => $v) {
      $posts[$k]['sendtime'] = toUserTime($v['sendtime']);
    }

    $body['thread'] = $thread;
    $body['posts'] = $posts;
    break;
}

template("forum");
