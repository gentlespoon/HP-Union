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
            $body['text'] = $lang['member']['dup-username'];
            template("error");
          }
          // register this new user
          $salt = random_str(6);
          $encryptedPassword = md5($_POST['password'].$salt);
          DB("INSERT INTO common_member (username, password, salt, regdate) VALUES ( :username , :password , :salt , :regdate)", [":username" => $_POST['username'], ":password" => $encryptedPassword, ":salt" => $salt, ":regdate" => time()]);
          $r = DB("SELECT uid FROM common_member WHERE username= :username", [":username" => $_POST['username']]);
          $uid = $r[0]['uid'];
          // log this new user in
          $_SESSION['uid'] = $uid;
          $_SESSION['uid'] = $uid;
        } else {
          // Already logged in, do not allow re-register
          $body['text'] = $lang['member']['logged-in'];
          template("error");
        }
      } else {
        // Some fields do not exist
        $body['text'] = $lang['member']['empty-field'];
        template("error");
      }
    }
    break;
  case "login":
    break;
  default:


}

$title = $lang['site']['name'];
template("member_member");
