<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
include(ROOT."core/core.php");


// $body['text'] = printv($_POST, true);

switch ($_GET['act']) {







  case "register":
    $title = $lang['register'];
    if (array_key_exists("username", $_POST)) {
      if (array_key_exists("password", $_POST)) {
        if ($_SESSION['uid'] < 1) {
          // begin registration

          // check for marks in username
          $list = "!@#$%^&*{[(<>)]};'\" `~?/\\|=+";
          $list = str_split($list);
          $unamecheck = usernameMarkCensor($_POST['username'], $list);
          if ($unamecheck) {
            // Invalid username with marks
            $body['text'] = $lang['invalid-username-1'].$unamecheck.$lang['invalid-username-3'];
            $body['redirect'] = $lang['hist-back'];
            $redirect = "javascript:history.back()";
            template("common_bang");
          }

          // check for restricted usernames
          $r = DB("SELECT word FROM member_restrictname");
          $list = [];
          foreach ($r as $k => $v) {
            array_push($list, $v['word']);
          }
          $unamecheck = usernameMarkCensor($_POST['username'], $list);
          if ($unamecheck) {
            // Invalid username with marks
            $body['text'] = $lang['invalid-username-2'].$unamecheck.$lang['invalid-username-3'];
            $body['redirect'] = $lang['hist-back'];
            $redirect = "javascript:history.back()";
            template("common_bang");
          }

          // check for duplicate usernames
          $r = DB("SELECT uid FROM member WHERE username= :username", [":username" => $_POST['username']]);
          if (!empty($r)) {
            // Username already registered
            $body['text'] = $lang['username-dup'];
            $body['redirect'] = $lang['hist-back'];
            $redirect = "javascript:history.back()";
            template("common_bang");
          }

          // register this new user
          $salt = randomStr(6);
          $encryptedPassword = md5($_POST['password'].$salt);
          DB("INSERT INTO member (username, password, salt, regdate, qq) VALUES ( :username , :password , :salt , :regdate, :qq)", [":username" => $_POST['username'], ":password" => $encryptedPassword, ":salt" => $salt, ":regdate" => time(), ":qq" => $_POST['qq']]);
          $r = DB("SELECT uid FROM member WHERE username= :username", [":username" => $_POST['username']]);
          $uid = $r[0]['uid'];
          // log this new user in
          // insert login history
          DB("INSERT INTO member_loginhistory (uid, logindate, success, ip) VALUES ( :uid, :logindate, :success, :ip)", [":uid" => $r[0]['uid'], ":logindate" => time(), ":success" => 1, ":ip" => $_SERVER['REMOTE_ADDR']]);
          // clear loginfail count
          DB("UPDATE member SET lastlogin= :lastlogin, failcount=0 WHERE uid= :uid", [":lastlogin" => time(), ":uid" => $r[0]['uid']]);
          $_SESSION['uid'] = $uid;
          // refresh userinfo
          include(ROOT."core/core.php");
        } else {
          // Already logged in, do not allow re-register
          $body['text'] = $lang['logged-in'];
          $body['redirect'] = $lang['hist-back'];
          $redirect = "member.php";
          template("common_bang");
        }
      } else {
        // Some fields do not exist
        $body['text'] = $lang['empty-field'];
        $body['redirect'] = $lang['hist-back'];
        $redirect = "javascript:history.back()";
        template("common_bang");
      }
    }
    break;







  case "login":
    $title = $lang['login'];
    if (array_key_exists("username", $_POST)) {
      if (array_key_exists("password", $_POST)) {
        if ($_SESSION['uid'] < 1) {
          // check ipfail count
          $r = DB("SELECT lasttrial, count FROM member_failedip WHERE ip=:ip", [":ip" => $_SERVER['REMOTE_ADDR']]);
          if (!empty($r)) {
            if ($r[0]['count']>10) {
              if (($r[0]['lasttrial']+3600*24) > time()) {
                // if temp ip ban still enforce
                $body['text'] = $lang['ip-ban-temp1'].(int)( (($r[0]['lasttrial']+3600*24) - time())/3600 ).$lang['ip-ban-temp2'];
                $body['redirect'] = $lang['hist-back'];
                $redirect = "javascript:history.back()";
                template("common_bang");
              }
            }
          }
          // fetch userinfo
          $r = DB("SELECT uid, password, salt, failcount FROM member WHERE username= :username", [":username" => $_POST['username']]);
          if (empty($r)) {
            // User does not exist
            $body['text'] = $lang['username-dne'];
            $body['redirect'] = $lang['hist-back'];
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
              $body['text'] = $lang['fail-penalty1'].($s[0]['logindate']+$bantime-time())." (/".$bantime.")".$lang['fail-penalty2'];
              $body['redirect'] = $lang['hist-back'];
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
            // refresh userinfo
            include(ROOT."core/core.php");

            // insert login history
            DB("INSERT INTO member_loginhistory (uid, logindate, success, ip) VALUES ( :uid, :logindate, :success, :ip)", [":uid" => $r[0]['uid'], ":logindate" => time(), ":success" => 1, ":ip" => $_SERVER['REMOTE_ADDR']]);
            // clear loginfail count
            DB("UPDATE member SET lastlogin= :lastlogin, failcount=0 WHERE uid= :uid", [":lastlogin" => time(), ":uid" => $r[0]['uid']]);
            // clear ipfail count
            $t = DB("SELECT ip, lasttrial, count, attempted FROM member_failedip WHERE ip=:ip", [":ip" => $_SERVER['REMOTE_ADDR']]);
            if (empty($t)) {
              DB("INSERT INTO member_failedip (ip, lasttrial, count) VALUES ( :ip, :lasttrial, 0) ON DUPLICATE KEY UPDATE count=0, lasttrial= :lasttrial", [":ip" => $_SERVER['REMOTE_ADDR'], ":lasttrial" => time()]);
            }
            $body['text'] = $lang['logged-in'];
            $body['redirect'] = $lang['continue-browsing'];
            $redirect = "";
            template("common_bang");
          } else {
            // Incorrect credentials
            // insert login history
            DB("INSERT INTO member_loginhistory (uid, logindate, success, ip) VALUES ( :uid, :logindate, :success, :ip)", [":uid" => $r[0]['uid'], ":logindate" => time(), ":success" => 0, ":ip" => $_SERVER['REMOTE_ADDR']]);
            // increase loginfail count
            DB("UPDATE member SET failcount=failcount+1 WHERE uid= :uid", [":uid" => $r[0]['uid']]);
            // increase ipfail count
            $t = DB("SELECT ip, lasttrial, count, attempted FROM member_failedip WHERE ip=:ip", [":ip" => $_SERVER['REMOTE_ADDR']]);
            if (empty($t)) {
              DB("INSERT INTO member_failedip (ip, lasttrial, count) VALUES ( :ip, :lasttrial, 0) ON DUPLICATE KEY UPDATE count=0, lasttrial= :lasttrial", [":ip" => $_SERVER['REMOTE_ADDR'], ":lasttrial" => time()]);
            } else {
              $attempted = $t[0]['attempted'];
              if (strlen($attempted)!=0) {
                $attempted .= ", ";
              }
              $attempted .= $_POST['username'];
              DB("UPDATE member_failedip SET lasttrial=:lasttrial, count=count+1, attempted=:attempted WHERE ip=:ip", [":ip" => $_SERVER['REMOTE_ADDR'], ":lasttrial" => time(), ":attempted" => $attempted]);
            }
            $body['text'] = $lang['invalid-cred'];
            $body['redirect'] = $lang['hist-back'];
            $redirect = "javascript:history.back()";
            template("common_bang");
          }
        } else {
          // Already logged in, do not allow re-register
          $body['text'] = $lang['logged-in'];
          $body['redirect'] = $lang['hist-back'];
          $redirect = "member.php";
          template("common_bang");
        }
      } else {
        // Some fields do not exist
        $body['text'] = $lang['empty-field'];
        $body['redirect'] = $lang['hist-back'];
        $redirect = "javascript:history.back()";
        template("common_bang");
      }
    }
    break;








  case "modpwd":
    $title = $lang['modpwd'];
    if (array_key_exists("currpwd", $_POST)) {
      if (array_key_exists("password", $_POST)) {
        if ($_SESSION['uid'] > 0) {
          // check old password
          $r = DB("SELECT password, salt FROM member WHERE uid=:uid", [":uid" => $_SESSION['uid']]);
          $encryptedPassword = md5($_POST['currpwd'].$r[0]['salt']);
          if ($encryptedPassword == $r[0]['password']) {
            // Authenticated
            $encryptedPassword = md5($_POST['password'].$r[0]['salt']);
            DB("UPDATE member SET password=:password WHERE uid=:uid", [":password" => $encryptedPassword, ":uid" => $_SESSION['uid']]);
            // Modified password
            $body['text'] = $lang['modpwd'].$lang['success'];
            $body['redirect'] = $lang['hist-back'];
            $redirect = "member.php";
            template("common_bang");
          } else {
            // Incorrect password
            $body['text'] = $lang['modpwd'].$lang['fail']."：".$lang['invalid-cred'];
            $body['redirect'] = $lang['hist-back'];
            $redirect = "javascript: history.back()";
            template("common_bang");
          }
        }
      } else {
        // Some fields do not exist
        $body['text'] = $lang['empty-field'];
        $body['redirect'] = $lang['hist-back'];
        $redirect = "javascript:history.back()";
        template("common_bang");
      }
    }
    break;








  case "modprofile":
    $title = $lang['modprofile'];
    if (array_key_exists("username", $_POST)) {
      // fetch table keys
      $r = DB("SELECT * FROM member WHERE uid=:uid", [":uid" => $_SESSION['uid']]);
      foreach ($_POST as $k => $v) {
        // verify key existence
        if(array_key_exists($k, $r[0])) {
          // it is safe to execute
          DB("UPDATE member SET ".$k."=:v WHERE uid=:uid", [":v" => $v, ":uid" => $_SESSION['uid']]);
        } else {
          // $_POST contains illegal keys
          exit("??????");
        }
      }
    }
    if ($_SESSION['uid'] > 0) {
      $r = DB("SELECT username, qq, email FROM member WHERE uid=:uid", [":uid" => $_SESSION['uid']]);
      $member = $r[0];
    }
    break;









  case "logout":
    $_SESSION['username'] = "";
    $_SESSION['uid'] = 0;

    break;
  default:
    if ($_SESSION['uid'] > 0) {
      // Fetch user info
      $member = DB("SELECT * FROM member WHERE uid=:uid", [":uid" => $_SESSION['uid']]);
      $member = $member[0];
      unset($member['password']);
      unset($member['salt']);
      $member['usergroup'] = $member['group_id'];
      unset($member['group_id']);
      $member['lastlogin'] = toUserTime($member['lastlogin']);
      $member['regdate'] = toUserTime($member['regdate']);
      // Fetch member count info
      $member_count = DB("SELECT * FROM member_count WHERE uid=:uid", [":uid" => $_SESSION['uid']]);
      if (empty($member_count)) {
        DB("INSERT INTO member_count (uid) VALUES (:uid)", [":uid" => $_SESSION['uid']]);
      } else {
        $member_count = $member_count[0];
        foreach ($member_count as $k => $v) {
          $member["count-".$k] = $v;
        }
        unset($member['count-uid']);
      }

    }


}


template("member_member");
