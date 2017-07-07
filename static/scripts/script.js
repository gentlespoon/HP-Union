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




});
