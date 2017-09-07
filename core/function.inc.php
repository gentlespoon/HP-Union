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
  $body['nav'] = getNavitem();
  $_endtime = microtime(true);
  $_runtime = $_endtime - $_starttime;
  // echo "END".$_endtime;
  // echo "RUN".$_runtime;
  foreach ($body['nav']['main'] as $k => $v) {
    $filenames = explode(" ", $v['filename']);
    if (in_array($file, $filenames)) {
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
  $nav = [];
  $nav['main'] = DB("SELECT * FROM common_navigation WHERE category='main' ORDER BY displayorder ASC");
  foreach ($nav['main'] as $k => $v) {
    $nav['main'][$k]['active'] = "";
  }
  $nav['topleft'] =  DB("SELECT * FROM common_navigation WHERE category='topleft' ORDER BY displayorder ASC");
  $nav['topright'] =  DB("SELECT * FROM common_navigation WHERE category='topright' ORDER BY displayorder ASC");
  return $nav;
}


function sendEmail($email,$title,$content) {
  include 'core/email.class.php';
  //$smtpserver = "SMTP.163.com";　//您的smtp服务器的地址
  $smtpserver="smtp.exmail.qq.com";
  $port =25; //smtp服务器的端口，一般是 25
  $smtpuser = "test@lyx.name"; //您登录smtp服务器的用户名
  $smtppwd = "Test122456"; //您登录smtp服务器的密码
  $mailtype = "HTML"; //邮件的类型，可选值是 TXT 或 HTML ,TXT 表示是纯文本的邮件,HTML 表示是 html格式的邮件
  $sender = "test@lyx.name";
  //发件人,一般要与您登录smtp服务器的用户名($smtpuser)相同,否则可能会因为smtp服务器的设置导致发送失败
  $smtp = new unionemail($smtpserver,$port,true,$smtpuser,$smtppwd,$sender);
  //$smtp->debug = true; //是否开启调试,只在测试程序时使用，正式使用时请将此行注释
  $to = $email; //收件人
  $subject = iconv("UTF-8","GB2312//IGNORE",$title);
  $body = $content;
  $send=$smtp->sendmail($to,$sender,$subject,$body,$mailtype);
  return $send;
}


function sendSMSCode($phone) {
  global $config;
  $code = rand(100000,999999);
  $post_data=[
    'sms_token' => $config['sms']['sms_token'],
    'content' => '猫头鹰信箱：您本次威森加摩巫师法庭的验证码为'.$code.'。30分钟内有效。',
    'phone' => $phone,
  ];
  $ch = curl_init();
  $url = 'http://api.lyx.name/api/send_sms';
  curl_setopt($ch , CURLOPT_URL , $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_HEADER, false);
  $res = curl_exec($ch);
  curl_close($ch);
  $result = json_decode($res);
  if ($result->errcode == 0) {
    return true;
  }
  return false;
}
