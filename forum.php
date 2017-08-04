<?php

define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
require(ROOT."core/core.php");













switch ($_GET['act']) {







  case "forum":
    if (!array_key_exists("viewforum", $member) || 
        (array_key_exists("viewforum", $member) && $member['viewforum'] == 0)) {
      $_GET['act'] = "";
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['permission-denied'];
      break;
    }
    $forum = DB("SELECT * FROM forum_forum WHERE fid = :fid AND visible > 0", [":fid" => $_GET["fid"]]);
    if (empty($forum)) {
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['invalid-forum-id'];
      break;
    }
    $forum[0]['subforum'] = DB("SELECT * FROM forum_forum WHERE parent_fid = :fid", [":fid" => $_GET["fid"]]);

    $threads = DB("SELECT forum_thread.tid, forum_thread.title, member.uid, member.username, forum_thread.sendtime, forum_thread.lasttime FROM forum_thread LEFT JOIN member ON forum_thread.author_uid = member.uid WHERE forum_thread.forum_id = :fid ORDER BY forum_thread.lasttime DESC", [":fid" => $_GET["fid"]]);

    foreach ($threads as $k => $v) {
      $threads[$k]['sendtime'] = toUserTime($v['sendtime']);
      $threads[$k]['lasttime'] = toUserTime($v['lasttime']);
    }

    $body['forum'] = $forum;
    $body['threads'] = $threads;
    break;















  case "thread":
    if (!array_key_exists("viewthread", $member) || 
        (array_key_exists("viewthread", $member) && $member['viewthread'] == 0)) {
      $_GET['act'] = "";
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['permission-denied'];
      break;
    }
    $thread = DB("SELECT * FROM forum_thread WHERE tid = :tid", [":tid" => $_GET['tid']]);
    if (empty($thread)) {
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['invalid-thread-id'];
      break;
    }

    $posts = DB("SELECT forum_post.title, forum_post.content, forum_post.author_uid, member.username, member.avatar, forum_post.sendtime FROM forum_post LEFT JOIN member ON forum_post.author_uid = member.uid WHERE forum_post.thread_tid = :tid ORDER BY forum_post.pid", [":tid" => $_GET['tid']]);
    foreach ($posts as $k => $v) {
      $posts[$k]['sendtime'] = toUserTime($v['sendtime']);
    }

    $body['thread'] = $thread[0];
    $body['posts'] = $posts;
    break;















  case "newthread":
    if (!array_key_exists("newthread", $member) || 
        (array_key_exists("newthread", $member) && $member['newthread'] == 0)) {
      $_GET['act'] = "";
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['permission-denied'];
      break;
    }
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
        DB("INSERT INTO forum_thread (author_uid, sendtime, forum_id, title, lasttime) VALUES (:uid, :sendtime, :fid, :title, :lasttime)", [":uid" => $_SESSION['uid'], ":sendtime" => $time, ":fid" => $_GET['fid'], ":title" => $_POST["title"], ":lasttime" => $time]);

        $thread = DB("SELECT tid FROM forum_thread WHERE author_uid = :uid AND sendtime = :sendtime", [":uid" => $_SESSION['uid'], ":sendtime" => $time]);
        if (empty($thread)) {
          $body['alerttype'] = "alert-danger";
          $body['alert'] = $lang['new-thread-fail'];
          break;
        }

        DB("INSERT INTO forum_post (thread_tid, author_uid, sendtime, title, content) VALUES (:tid, :uid, :sendtime, :title, :content)", [":tid" => $thread[0]['tid'], ":uid" => $_SESSION['uid'], ":sendtime" => $time, ":title" => $_POST["title"], ":content" => $_POST["content"]]);

        DB("UPDATE member_count SET threads = threads + 1, posts = posts + 1 WHERE uid = :uid", [":uid" => $_SESSION['uid']]);

        $body['alerttype'] = "alert-success";
        $body['alert'] = $lang['new-thread-success'];
        $redirect = "forum.php?act=thread&tid=". $thread[0]['tid'];
        $body['redirect'] = $lang["new-thread-redirect"];
        template("common_bang");
      }
    }
    break;

















  case "newpost":
    if (!array_key_exists("newpost", $member) || 
        (array_key_exists("newpost", $member) && $member['newpost'] == 0)) {
      $_GET['act'] = "";
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['permission-denied'];
      break;
    }
    if (empty($_GET['tid'])) {
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['invalid-thread-id'];
      break;
    }
    $thread = DB("SELECT * FROM forum_thread WHERE tid = :tid", [":tid" => $_GET["tid"]]);
    if (empty($thread)) {
      $body['alerttype'] = "alert-danger";
      $body['alert'] = $lang['invalid-thread-id'];
      break;
    }

    if ($_SESSION['uid'] > 0) {
      if (array_key_exists("content", $_POST) && $_POST["content"] && !ctype_space($_POST["content"])) {
        $time = time();
        DB("INSERT INTO forum_post (thread_tid, author_uid, sendtime, content) VALUES (:tid, :uid, :sendtime, :content)", [":tid" => $_GET['tid'], ":uid" => $_SESSION['uid'], ":sendtime" => $time, ":content" => $_POST['content']]);

        $post = DB("SELECT * FROM forum_post WHERE thread_tid = :tid AND author_uid = :uid AND sendtime = :sendtime", [":tid" => $_GET['tid'], ":uid" => $_SESSION['uid'], ":sendtime" => $time]);
        if (empty($post)) {
          $body['alerttype'] = "alert-danger";
          $body['alert'] = $lang['new-post-fail'];
          break;
        }

        DB("UPDATE forum_thread SET lasttime = :lasttime WHERE tid = :tid", [":lasttime" => $time, ":tid" => $_GET["tid"]]);
        DB("UPDATE member_count SET posts = posts + 1 WHERE uid = :uid", [":uid" => $_SESSION['uid']]);

        $body['alerttype'] = "alert-success";
        $body['alert'] = $lang['new-post-success'];
        $redirect = "forum.php?act=thread&tid=". $_GET["tid"];
        $body['redirect'] = $lang["new-thread-redirect"];
        template("common_bang");
      }
    }
    break;
}

template("forum");
