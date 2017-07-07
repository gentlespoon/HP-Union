$(document).ready(function() {
  $(".userform").submit(function() {
    $("#loginform_password").val($.md5($("#loginform_password").val()));
    $("#regform_password").val($.md5($("#regform_password").val()));
  })
});
