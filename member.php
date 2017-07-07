<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include_once(ROOT."core/core.php");


// $body['text'] = printv($_POST, true);

switch ($_GET['act']) {
  case "register":
    if (array_key_exists("username", $_POST)) {
      if (array_key_exists("password", $_POST)) {
        if ($_SESSION['uid'] < 1) {
          $r = DB("SELECT uid FROM common_member WHERE username= :username", [":username" => $_POST['username']]);
          if (!empty($r)) {
            // Username already registered
            $body['text'] = $lang['member']['username-dup'];
            $body['redirect'] = $lang['interface']['hist-back'];
            $redirect = "javascript:history.back()";
            template("common_bang");
          }
          // register this new user
          $salt = random_str(6);
          $encryptedPassword = md5($_POST['password'].$salt);
          DB("INSERT INTO common_member (username, password, salt, regdate) VALUES ( :username , :password , :salt , :regdate)", [":username" => $_POST['username'], ":password" => $encryptedPassword, ":salt" => $salt, ":regdate" => time()]);
          $r = DB("SELECT uid FROM common_member WHERE username= :username", [":username" => $_POST['username']]);
          $uid = $r[0]['uid'];
          // log this new user in
          $_SESSION['uid'] = $uid;
          $_SESSION['username'] = $_POST['username'];
        } else {
          // Already logged in, do not allow re-register
          $body['text'] = $lang['member']['logged-in'];
          $body['redirect'] = $lang['interface']['hist-back'];
          $redirect = "member.php";
          template("common_bang");
        }
      } else {
        // Some fields do not exist
        $body['text'] = $lang['member']['empty-field'];
        $body['redirect'] = $lang['interface']['hist-back'];
        $redirect = "javascript:history.back()";
        template("common_bang");
      }
    }
    break;
  case "login":
    if (array_key_exists("username", $_POST)) {
      if (array_key_exists("password", $_POST)) {
        if ($_SESSION['uid'] < 1) {
          $r = DB("SELECT uid, password, salt FROM common_member WHERE username= :username", [":username" => $_POST['username']]);
          if (empty($r)) {
            // Username is not registered
            $body['text'] = $lang['member']['username-dne'];
            $body['redirect'] = $lang['interface']['hist-back'];
            $redirect = "javascript:history.back()";
            template("common_bang");
          }
          // check password
          $encryptedPassword = md5($_POST['password'].$r[0]['salt']);
          if ($encryptedPassword == $r[0]['password']) {
            // log this new user in
            $_SESSION['uid'] = $r[0]['uid'];
            $_SESSION['username'] = $_POST['username'];
          } else {
            // Already logged in, do not allow re-register
            $body['text'] = $lang['member']['invalid-cred'];
            $body['redirect'] = $lang['interface']['hist-back'];
            $redirect = "javascript:history.back()";
            template("common_bang");
          }
        } else {
          // Already logged in, do not allow re-register
          $body['text'] = $lang['member']['logged-in'];
          $body['redirect'] = $lang['interface']['hist-back'];
          $redirect = "member.php";
          template("common_bang");
        }
      } else {
        // Some fields do not exist
        $body['text'] = $lang['member']['empty-field'];
        $body['redirect'] = $lang['interface']['hist-back'];
        $redirect = "javascript:history.back()";
        template("common_bang");
      }
    }
    break;
  case "logout":
    $_SESSION['username'] = "";
    $_SESSION['uid'] = 0;

    break;
  default:


}

$title = $lang['site']['name'];
template("member_member");
