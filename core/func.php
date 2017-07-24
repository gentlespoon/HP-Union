<?php





// Database wrappers

function DB($sql, $param=[]) {
  global $db;
  if ($sth = $db->prepare($sql, [PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY])) {
    $sth->execute($param);
    $rs = $sth->fetchAll();
    // remove numeric indices
    foreach ($rs as $k_ => $v_) {
      foreach ($v_ as $k => $v) {
        if (is_int($k)) {
          unset($rs[$k_][$k]);
        }
      }
    }
    return $rs;
  } else {
    exit("Database Error: ".$db->error);
  }
}


// Output wrapper

function printv($arr, $ret=false) {
  $buf = "";
  if (is_array($arr)) {
    $buf .= "<pre>";
    $buf .= print_r($arr, true);
    $buf .= "</pre>";
    if ($ret) {
      return $buf;
    } else {
      echo $buf;
    }
  } else {
    echo $arr;
  }
}

function template($file, ...$extrafiles) {
  // render the html template
  // this function will terminate the php execution
  global $_SESSION;
  global $_GET;
  global $_starttime;
  global $body;
  global $lang;
  global $redirect;
  global $settings;
  global $title;
  global $member;
  getNavitem();
  $_endtime = microtime(true);
  $_runtime = $_endtime - $_starttime;
  // echo "END".$_endtime;
  // echo "RUN".$_runtime;
  foreach ($body['nav']['main'] as $k => $v) {
    if ($v['filename'] == $file) {
      $body['nav']['main'][$k]['active'] = "nav_main_active";
    }
  }
  include_once(ROOT."templates/".$settings['template']."/common_header_html.htm");
  include_once(ROOT."templates/".$settings['template']."/common_header_visual.htm");
  include_once(ROOT."templates/".$settings['template']."/".$file.".htm");
  foreach ($extrafiles as $k => $v) {
    include_once(ROOT."templates/".$settings['template']."/".$v.".htm");
  }
  include_once(ROOT."templates/".$settings['template']."/common_footer_visual.htm");
  include_once(ROOT."templates/".$settings['template']."/common_footer_html.htm");
  exit();
}





function randomStr($length) {
    $keyspace = '0123456789abcdefghijklmnopqrstuvwxyz';
    $str = '';
    $max = strlen($keyspace) - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}

function usernameMarkCensor($string, $sensorlist) {
  foreach ($sensorlist as $k => $v) {
    if (strpos($string, $v) !== false) {
      return $v;
    }
  }
  return false;
}


function getAvatarUrl($qq) {
  // default url
  $avatar_url = "http://hp-union.com/static/images/default_avatar.jpg";
  $url = 'http://ptlogin2.qq.com/getface?appid=1006102&uin='.$qq.'&imgtype=4';
  $avatar_response = file_get_contents($url);
  $start_avatar = strpos($avatar_response, "http:");
  if ($start_avatar !== false) {
    $avatar_url = str_replace("\\", "", substr($avatar_response, $start_avatar, -4));
  }
  return $avatar_url;
}



function getUserInfo() {
  global $lang;
  if ($_SESSION['uid'] > 0) {
    $member = DB("SELECT username, qq, groupid, avatar FROM member WHERE uid=:uid", [":uid" => $_SESSION['uid']]);
    if (isset($member[0])) {
      $member = $member[0];
      $usergroup = DB("SELECT * FROM member_groups WHERE groupid=:groupid", [":groupid" => $member['groupid']]);
      if (!empty($usergroup)) {
        foreach ($usergroup[0] as $k => $v) {
          $member[$k] = $v;
        }
      }
      unset($member['gid']);
    } else {
     $member = ["username" => $lang['not-logged-in'], "qq" => 0];
    }
  } else {
    $member = ["username" => $lang['not-logged-in'], "qq" => 0];
  }
  return $member;
}


function getNavitem() {
  global $body;
  $body['nav']['main'] = DB("SELECT * FROM common_navigation WHERE category='main' ORDER BY displayorder ASC");
  foreach ($body['nav']['main'] as $k => $v) {
    $body['nav']['main'][$k]['active'] = "";
  }
  $body['nav']['topleft'] =  DB("SELECT * FROM common_navigation WHERE category='topleft' ORDER BY displayorder ASC");
  $body['nav']['topright'] =  DB("SELECT * FROM common_navigation WHERE category='topright' ORDER BY displayorder ASC");
}
