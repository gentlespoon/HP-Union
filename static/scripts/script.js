$(document).ready(function() {
  $("#loginform").submit(function() {
    if ($("#loginform_username").val() == "") {
      alert("用户名不能为空");
      return false;
    }
    if ($("#loginform_password").val() == "") {
      alert("密码不能为空");
      return false;
    }
    $("#loginform_password").val($.md5($("#loginform_password").val()));
  });


  $("#regform").submit(function() {
    if ($("#regform_username").val() == "") {
      alert("用户名不能为空");
      return false;
    }
    if ($("#regform_username").val().length > 12) {
      alert("用户名不能超过12个字");
      return false;
    }
    if ($("#regform_password").val() == "") {
      alert("密码不能为空");
      return false;
    }
    if ($("#regform_password").val() != $("#regform_password2").val()) {
      alert("两次输入的密码不同");
      return false;
    }
    $("#regform_password").val($.md5($("#regform_password").val()));
  });



  $("#modpwdform").submit(function() {
    if ($("#modpwdform_password").val() == "") {
      alert("密码不能为空");
      return false;
    }
    if ($("#modpwdform_password").val() != $("#modpwdform_password2").val()) {
      alert("两次输入的密码不同");
      return false;
    }
    $("#modpwdform_currpwd").val($.md5($("#modpwdform_currpwd").val()));
    $("#modpwdform_password").val($.md5($("#modpwdform_password").val()));
  });







  $("#newthreadform").submit(function() {
    if ($("#newthreadform_title").val() == "") {
      alert("主题帖标题不能为空");
      return false;
    }
    if ($("#newthreadform_content").val().length < 6) {
      alert("帖子内容不能少于6个字");
      return false;
    }
  });


  $("#newpostform").submit(function() {
    if ($("#newpostform_content").val().length < 6) {
      alert("帖子内容不能少于6个字");
      return false;
    }
  });




  function getUserAvatar(qq, uid, obj) {
    $.ajax({
      url: "http://localhost/api/avatar2.php?qq="+qq+"&uid="+uid,
    }).done(function(done) {
      var avatar = done.replace(/\\/g, "");
      $(obj).attr('src', avatar);
    });
  }

  $(".avatar").each(function() {
    return false;  // disable this function
    getUserAvatar($(this).attr('qq'), $(this).attr('uid'), "#"+$(this).attr('id'));
  });




  $(".nav_main_item").click(function() {
    if ($(this).attr('href') != "") {
      location.href = $(this).attr('href');
    }
  });


});
