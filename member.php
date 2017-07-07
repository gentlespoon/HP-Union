<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include_once(ROOT."core/core.php");


// $body['text'] = printv($_POST, true);

switch ($_GET['act']) {
  case "register":
    if (array_key_exists("username", $_POST)) {
      if (array_key_exists("password", $_POST)) {
        if ($_SESSION['uid'] < 1) {
          $r = DB("SELECT uid FROM member WHERE username= :username", [":username" => $_POST['username']]);
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
          DB("INSERT INTO member (username, password, salt, regdate) VALUES ( :username , :password , :salt , :regdate)", [":username" => $_POST['username'], ":password" => $encryptedPassword, ":salt" => $salt, ":regdate" => time()]);
          $r = DB("SELECT uid FROM member WHERE username= :username", [":username" => $_POST['username']]);
          $uid = $r[0]['uid'];
          // log this new user in
          // insert login history
          DB("INSERT INTO member_loginhistory (uid, logindate, success, ip) VALUES ( :uid, :logindate, :success, :ip)", [":uid" => $r[0]['uid'], ":logindate" => time(), ":success" => 1, ":ip" => $_SERVER['REMOTE_ADDR']]);
          // clear loginfail count
          DB("UPDATE member SET lastlogin= :lastlogin, failcount=0 WHERE uid= :uid", [":lastlogin" => time(), ":uid" => $r[0]['uid']]);
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
    $title = $lang['member']['register'];
    break;
  case "login":
    if (array_key_exists("username", $_POST)) {
      if (array_key_exists("password", $_POST)) {
        if ($_SESSION['uid'] < 1) {
          // check ipfail count
          $r = DB("SELECT lasttrial, count FROM member_failedip WHERE ip=:ip", [":ip" => $_SERVER['REMOTE_ADDR']]);
          if (!empty($r)) {
            if ($r[0]['count']>10) {
              if (($r[0]['lasttrial']+3600*24) > time()) {
                // if temp ip ban still enforce
                $body['text'] = $lang['member']['ip-ban-temp1'].(int)( (($r[0]['lasttrial']+3600*24) - time())/3600 ).$lang['member']['ip-ban-temp2'];
                $body['redirect'] = $lang['interface']['hist-back'];
                $redirect = "javascript:history.back()";
                template("common_bang");
              }
            }
          }
          // fetch userinfo
          $r = DB("SELECT uid, password, salt, failcount FROM member WHERE username= :username", [":username" => $_POST['username']]);
          if (empty($r)) {
            // User does not exist
            $body['text'] = $lang['member']['username-dne'];
            $body['redirect'] = $lang['interface']['hist-back'];
            $redirect = "javascript:history.back()";
            template("common_bang");
          }

          // calculate fail login penalty time
          $bantime = 10*pow(2, $r[0]['failcount']);

          // fetch last trial time
          $s = DB("SELECT logindate FROM member_loginhistory WHERE uid= :uid ORDER BY logindate DESC", [":uid" => $r[0]['uid']]);
          if (!empty($s)) {
            if (($s[0]['logindate']+$bantime)>time()) {
              // login failed penalty
              $body['text'] = $lang['member']['fail-penalty1'].($s[0]['logindate']+$bantime-time())." (/".$bantime.")".$lang['member']['fail-penalty2'];
              $body['redirect'] = $lang['interface']['hist-back'];
              $redirect = "javascript:history.back()";
              template("common_bang");
            }
          }

          // if no penalty, check password
          $encryptedPassword = md5($_POST['password'].$r[0]['salt']);
          if ($encryptedPassword == $r[0]['password']) {
            // credentials correct
            // log this new user in
            $_SESSION['uid'] = $r[0]['uid'];
            $_SESSION['username'] = $_POST['username'];

            // insert login history
            DB("INSERT INTO member_loginhistory (uid, logindate, success, ip) VALUES ( :uid, :logindate, :success, :ip)", [":uid" => $r[0]['uid'], ":logindate" => time(), ":success" => 1, ":ip" => $_SERVER['REMOTE_ADDR']]);
            // clear loginfail count
            DB("UPDATE member SET lastlogin= :lastlogin, failcount=0 WHERE uid= :uid", [":lastlogin" => time(), ":uid" => $r[0]['uid']]);
            // clear ipfail count
            DB("INSERT INTO member_failedip (ip, lasttrial, count) VALUES ( :ip, :lasttrial, 0) ON DUPLICATE KEY UPDATE count=0, lasttrial= :lasttrial", [":ip" => $_SERVER['REMOTE_ADDR'], ":lasttrial" => time()]);

          } else {
            // Incorrect credentials
            // insert login history
            DB("INSERT INTO member_loginhistory (uid, logindate, success, ip) VALUES ( :uid, :logindate, :success, :ip)", [":uid" => $r[0]['uid'], ":logindate" => time(), ":success" => 0, ":ip" => $_SERVER['REMOTE_ADDR']]);
            // increase loginfail count
            DB("UPDATE member SET failcount=failcount+1 WHERE uid= :uid", [":uid" => $r[0]['uid']]);
            // increase ipfail count
            DB("INSERT INTO member_failedip (ip, lasttrial) VALUES ( :ip, :lasttrial) ON DUPLICATE KEY UPDATE count=count+1, lasttrial= :lasttrial", [":ip" => $_SERVER['REMOTE_ADDR'], ":lasttrial" => time()]);

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


template("member_member");
