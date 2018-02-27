/*
 * Copyright (c) 2018.
 * All rights reserved.
 * GentleSpoon
 * me@gentlespoon.com
 */


$(document).ready(function (){

  $("#footer-copyright-year").text((new Date()).getFullYear());


  // 登出
  $("#btn_logout").click(function () {
    $.post('/member/logout.api', function() {
      location.reload();
    });
  });


  function loginDoneCB(data) {
    data = $.parseJSON(data);
    if (data.ok) {
      location.reload();
    } else {
      $("#lb_loginAlert").text(data.data);
    }
  }

  function loginFailCB() {
    $("#lb_loginAlert").text("网络异常");
  }

  // 登入
  $("#form_login").submit(function () {
    $("#lb_loginAlert").text("正在登录");
    $("#div_loginAlert").css("display", "block");
    $.post('/member/login.api',
      {
        username: $("#tb_loginUsername").val(),
        password: $.md5($("#tb_loginPassword").val())
      })
      .done(function (data) { loginDoneCB(data); })
      .fail(function () { loginFailCB(); });
    return false;
  });


  // 注册
  $("#btn_register").click(function () {
    $("#lb_loginAlert").text("正在注册");
    $("#div_loginAlert").css("display", "block");
    $.post('/member/register.api',
      {
        username: $("#tb_loginUsername").val(),
        password: $.md5($("#tb_loginPassword").val())
      })
      .done(function (data) { loginDoneCB(data); })
      .fail(function () { loginFailCB(); });
    return false;
  });










});


