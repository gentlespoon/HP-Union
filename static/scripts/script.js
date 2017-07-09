$(document).ready(function() {
  $("#loginform").submit(function() {
    $("#loginform_password").val($.md5($("#loginform_password").val()));
  });


  $("#regform").submit(function() {
    if ($("#regform_password").val() != $("#regform_password2").val()) {
      alert("两次输入的密码不同");
      return false;
    }
    $("#regform_password").val($.md5($("#regform_password").val()));
  });



  $("#modpwdform").submit(function() {
    if ($("#modpwdform_password").val() != $("#modpwdform_password2").val()) {
      alert("两次输入的新密码不同");
      return false;
    }
    $("#modpwdform_currpwd").val($.md5($("#modpwdform_currpwd").val()));
    $("#modpwdform_password").val($.md5($("#modpwdform_password").val()));
  });

  // // getUserAvatar( user_qq, dom_obj_id_to_place_avatar );
  // function getUserAvatar(qq, obj) {
  //   $.ajax({
  //     url: "http://localhost/api/avatar.php?qq="+$("#user_qq").text(),
  //   }).done(function(done) {
  //     var avatar = done.replace(/\\/g, "");
  //     $(obj).attr('src', avatar);
  //     // alert(avatar);
  //   });
  // }
  //
  // getUserAvatar($("#user_qq").text(), "#nav_avatar");

});
