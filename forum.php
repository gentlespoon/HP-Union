<?php

define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include(ROOT."core/core.php");

switch ($_GET['act']) {


  case "forum":
    $forum = DB("SELECT * FROM forum_forum WHERE fid = :fid AND visible > 0", [":fid" => $_GET["fid"]]);
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


  case "newthread":
    if (empty($_GET['fid'])) {
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['invalid-forum-id'];
      break;
    }
    if ($_SESSION['uid'] > 0) {
      if (array_key_exists("title", $_POST) && array_key_exists("content", $_POST) &&
          $_POST["title"] != "" && $_POST["content"] &&
          !ctype_space($_POST["title"]) && !ctype_space($_POST["content"])) {
        $time = time();
        DB("INSERT INTO forum_thread (author_uid, sendtime, forum_id, title) VALUES (:uid, :sendtime, :fid, :title)", [":uid" => $_SESSION['uid'], ":sendtime" => $time, ":fid" => $_GET['fid'], ":title" => $_POST["title"]]);

        $thread = DB("SELECT tid FROM forum_thread WHERE author_uid = :uid AND sendtime = :sendtime", [":uid" => $_SESSION['uid'], ":sendtime" => $time]);
        if (empty($thread)) {
          $body['alerttype'] = "alert-danger";
          $body['alert'] = $lang['new-thread-fail'];
          break;
        }

        DB("INSERT INTO forum_post (thread_tid, author_uid, sendtime, title, content) VALUES (:tid, :uid, :sendtime, :title, :content)", [":tid" => $thread[0]['tid'], ":uid" => $_SESSION['uid'], ":sendtime" => $time, ":title" => $_POST["title"], ":content" => $_POST["content"]]);

        DB("UPDATE member_count set threads = threads + 1, posts = posts + 1 WHERE uid = :uid", [":uid" => $_SESSION['uid']]);

        $body['alerttype'] = "alert-success";
        $body['alert'] = $lang['new-thread-success'];
        $redirect = "forum.php?act=thread&tid=". $thread[0]['tid'];
        $body['redirect'] = $lang["new-thread-redirect"];
        template("common_bang");
      }
    }
    break;
}

template("forum");
