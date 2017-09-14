<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/");
require(ROOT."core/core.php");


switch ($_GET['act']) {







  case "register":
    $title = $lang['register'];
    if (array_key_exists("username", $_POST)) {
      if (array_key_exists("password", $_POST)) {
        if ($_SESSION['uid'] < 1) {
          // begin registration

          // if empty username
          if ($_POST['username'] == "") {
            // Invalid username with marks
            $body['alerttype'] = "alert-danger";
            $body['alert'] = $lang['blank-username'];
            break;
          }

          // check for marks in username
          $list = "!@#$%^&*{[(<>)]};'\" `~?/\\|=+";
          $list = str_split($list);
          $unamecheck = usernameMarkCensor($_POST['username'], $list);
          if ($unamecheck) {
            // Invalid username with marks
            $body['alerttype'] = "alert-danger";
            $body['alert'] = $lang['invalid-username-1'].$unamecheck.$lang['invalid-username-3'];
            break;
          }

          // check for restricted usernames
          $r = DB::query("SELECT word FROM member_restrictname");
          $list = [];
          foreach ($r as $k => $v) {
            array_push($list, $v['word']);
          }
          $unamecheck = usernameMarkCensor($_POST['username'], $list);
          if ($unamecheck) {
            // Invalid username with marks
            $body['alerttype'] = "alert-danger";
            $body['alert'] = $lang['invalid-username-2'].$unamecheck.$lang['invalid-username-3'];
            break;
          }

          // check for duplicate usernames
          $r = DB::query("SELECT uid FROM member WHERE username=%s", $_POST['username']);
          if (!empty($r)) {
            // Username already registered
            $body['alerttype'] = "alert-danger";
            $body['alert'] = $lang['username-dup'];
            break;
          }

          // register this new user
          $salt = randomStr(6);
          $encryptedPassword = md5($_POST['password'].$salt);
          $avatar = getAvatarUrl($_POST['qq']);
          DB::query("INSERT INTO member (username, password, salt, regdate, qq, avatar) VALUES ( %s , %s , %s , %s, %s, %s)", $_POST['username'], $encryptedPassword, $salt, time(), $_POST['qq'], $avatar);
          $uid = DB::query("SELECT uid FROM member WHERE username=%s", $_POST['username'])[0]['uid'];
          DB::query("INSERT INTO member_count (uid) VALUES (%i)", $uid);
          // log this new user in
          // insert login history
          DB::query("INSERT INTO member_loginhistory (uid, logindate, success, ip) VALUES ( %i, %s, %s, %s)", $uid, time(), 1, $_SERVER['REMOTE_ADDR']);
          // clear loginfail count
          DB::query("UPDATE member SET lastlogin= %s, failcount=0 WHERE uid= %i", time(), $uid);
          $_SESSION['uid'] = $uid;
          // refresh userinfo
          $member = getUserInfo();
          $body['text'] = "<p>".$settings['registered-welcome']."</p>";
          $body['text'] .= "<p>".$settings['login-welcome']."</p>";
          $body['alerttype'] = "alert-success";
          $body['alert'] = $settings['registered-welcome'];
          $body['redirect'] = $lang['continue-browsing'];
          $redirect = "member.php";
          template("common_bang");
          break;
        } else {
          // Already logged in, do not allow re-register
          $body['alerttype'] = "alert-success";
          $body['alert'] = $lang['logged-in'];
          $body['redirect'] = $lang['hist-back'];
          $redirect = "member.php";
          template("common_bang");
          break;
        }
      } else {
        // Some fields do not exist
        $body['alerttype'] = "alert-danger";
        $body['alert'] = $lang['empty-field'];
        break;
      }
    }
    break;







  case "login":
    $title = $lang['login'];
    if (array_key_exists("username", $_POST)) {
      if (array_key_exists("password", $_POST)) {
        if ($_SESSION['uid'] < 1) {
          // check ipfail count
          $r = DB::query("SELECT lasttrial, count FROM member_failedip WHERE ip=%s", $_SERVER['REMOTE_ADDR']);
          if (!empty($r)) {
            if ($r[0]['count']>10) {
              if (($r[0]['lasttrial']+3600*24) > time()) {
                // if temp ip ban still enforce
                $body['alerttype'] = "alert-danger";
                $body['alert'] = $lang['ip-ban-temp1'].(int)( (($r[0]['lasttrial']+3600*24) - time())/3600 ).$lang['ip-ban-temp2'];
                break;
              }
            }
          }
          // fetch userinfo
          $r = DB::query("SELECT uid, password, salt, failcount FROM member WHERE username= %s", $_POST['username']);
          if (empty($r)) {
            // User does not exist
            $body['alerttype'] = "alert-danger";
            $body['alert'] = $lang['username-dne'];
            break;
          }

          // calculate fail login penalty time
          $bantime = 10*pow(2, $r[0]['failcount']);

          // fetch last trial time
          $s = DB::query("SELECT logindate FROM member_loginhistory WHERE uid=%i ORDER BY logindate DESC", $r[0]['uid']);
          if (!empty($s)) {
            if (($s[0]['logindate']+$bantime)>time()) {
              // login failed penalty
              $body['alerttype'] = "alert-danger";
              $body['alert'] = $lang['fail-penalty1'].($s[0]['logindate']+$bantime-time())." (/".$bantime.")".$lang['fail-penalty2'];
              break;
            }
          }

          // if no penalty, check password
          $encryptedPassword = md5($_POST['password'].$r[0]['salt']);
          if ($encryptedPassword == $r[0]['password']) {
            // credentials correct
            // log this new user in
            $_SESSION['uid'] = $r[0]['uid'];
            // refresh userinfo
            $member = getUserInfo();

            // insert login history
            DB::query("INSERT INTO member_loginhistory (uid, logindate, success, ip) VALUES (%i, %s, %i, %s)", $_SESSION['uid'], time(), 1, $_SERVER['REMOTE_ADDR']);
            // clear loginfail count
            DB::query("UPDATE member SET lastlogin=%s, failcount=0 WHERE uid=%i", time(), $_SESSION['uid']);
            // clear ipfail count
            $t = DB::query("SELECT ip, lasttrial, count, attempted FROM member_failedip WHERE ip=%s", $_SERVER['REMOTE_ADDR']);
            if (empty($t)) {
              DB::query("INSERT INTO member_failedip (ip, lasttrial, count) VALUES (%s, %s, 0) ON DUPLICATE KEY UPDATE count=0, lasttrial=%s", $_SERVER['REMOTE_ADDR'], time(), time());
            }
            $body['alerttype'] = "alert-success";
            $body['alert'] = $lang['logged-in'];
            break;
          } else {
            // Incorrect credentials
            // insert login history
            DB::query("INSERT INTO member_loginhistory (uid, logindate, success, ip) VALUES (%i, %s, %i, %s)", $_SESSION['uid'], time(), 0, $_SERVER['REMOTE_ADDR']);
            // increase loginfail count
            DB::query("UPDATE member SET failcount=failcount+1 WHERE uid=%i", $_SESSION['uid']);
            // increase ipfail count
            $t = DB::query("SELECT ip, lasttrial, count, attempted FROM member_failedip WHERE ip=%s", $_SERVER['REMOTE_ADDR']);
            if (empty($t)) {
              DB::query("INSERT INTO member_failedip (ip, lasttrial, count) VALUES (%s, %s, 0) ON DUPLICATE KEY UPDATE count=0, lasttrial=%s", $_SERVER['REMOTE_ADDR'], time(), time());
            } else {
              $attempted = $t[0]['attempted'];
              if (strlen($attempted)!=0) {
                $attempted .= ", ";
              }
              $attempted .= $_POST['username'];
              DB::query("UPDATE member_failedip SET lasttrial=".time().", count=count+1, attempted=%s WHERE ip=%s", $attempted, $_SERVER['REMOTE_ADDR']);
            }
            $body['alerttype'] = "alert-danger";
            $body['alert'] = $lang['invalid-cred'];
            break;
          }
        } else {
          // Already logged in, do not allow re-register
          $body['alerttype'] = "alert-success";
          $body['alert'] = $lang['logged-in'];
          break;
        }
      } else {
        // Some fields do not exist
        $body['alerttype'] = "alert-danger";
        $body['alert'] = $lang['empty-field'];
        break;
      }
    }
    break;








  case "modpwd":
    $title = $lang['modpwd'];
    if (array_key_exists("currpwd", $_POST)) {
      if (array_key_exists("password", $_POST)) {
        if ($_SESSION['uid'] > 0) {
          // check old password
          $r = DB::query("SELECT password, salt FROM member WHERE uid=%i", $_SESSION['uid']);
          if (! $r) {
            // User does not exist
            break;
          }
          $encryptedPassword = md5($_POST['currpwd'].$r[0]['salt']);
          if ($encryptedPassword == $r[0]['password']) {
            // Authenticated
            $encryptedPassword = md5($_POST['password'].$r[0]['salt']);
            DB::query("UPDATE member SET password=%s WHERE uid=%i", $encryptedPassword, $_SESSION['uid']);
            // Modified password
            $body['alerttype'] = "alert-success";
            $body['alert'] = $lang['modpwd'].$lang['success'];
            break;
          } else {
            // Incorrect password
            $body['alerttype'] = "alert-danger";
            $body['alert'] = $lang['modpwd'].$lang['fail']."：".$lang['invalid-cred'];
            break;
          }
        }
      } else {
        // Some fields do not exist
        $body['alerttype'] = "alert-danger";
        $body['alert'] = $lang['empty-field'];
        break;
      }
    }
    break;








  case "modprofile":
    $title = $lang['modprofile'];
    if (array_key_exists("username", $_POST)) {
      $_POST['avatar'] = getAvatarUrl($_POST['qq']);
      // fetch table keys
      $tablekeys = DB::query("SELECT * FROM member WHERE uid=%i", $_SESSION['uid']);
      foreach ($_POST as $k => $v) {
        // verify key existence
        if(array_key_exists($k, $tablekeys[0])) {
          // it is safe to execute
          DB::query("UPDATE member SET ".$k."=%s WHERE uid=%i", $v, $_SESSION['uid']);
        } else {
          // $_POST contains illegal keys
          exit("??????");
        }
      }
      $body['alerttype'] = "alert-success";
      $body['alert'] = $lang['modprofile'].$lang['success'];
      // refresh userinfo
      $member = getUserInfo();
    }
    // select columns that are allowed to modify
    $member_fields = DB::query("SELECT username, qq, email FROM member WHERE uid=%i",$_SESSION['uid']);
    $member['fields'] = $member_fields[0];
    break;









  case "logout":
    $_SESSION['username'] = "";
    $_SESSION['uid'] = 0;
    $body['alerttype'] = "alert-success";
    $body['alert'] = $lang['logged-out'];
    break;





  default:
    if ($_SESSION['uid'] > 0) {
      // Fetch user info
      if (!isset($_GET['uid'])) {
        $_GET['uid'] = $_SESSION['uid'];
      }

      $userinfo = DB::query("SELECT *FROM member WHERE uid=%i", $_GET['uid'])[0];
      $userinfo['regdate'] = toUserTime($userinfo['regdate']);
      $userinfo['lastlogin'] = toUserTime($userinfo['lastlogin']);
      $body['userinfo'] = $userinfo;

      $usercount = DB::query("SELECT * FROM member_count WHERE uid=%i", $_GET['uid']);
      $usercount = $usercount[0];
      $usercount['threads-count'] = $usercount['threads'];
      $usercount['posts-count'] = $usercount['posts'];
      $body['usercount'] = $usercount;

      $userperm = DB::query("SELECT * FROM member_groups WHERE groupid=%i",  $body['userinfo']['groupid'])[0];
      $body['userperm'] = $userperm;

      unset($body['userinfo']['password'], $body['userinfo']['salt']);
      unset($body['userinfo']['groupid'], $body['userinfo']['failcount']);
      unset($body['usercount']['threads'], $body['usercount']['posts']);
      unset($body['usercount']['uid']);
      unset($body['userperm']['groupid']);


    }
}


template("member");
