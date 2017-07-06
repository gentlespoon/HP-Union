<?php




// No longer needed. PHP has built-in md5

// # Hash functions
// def md5encode(str):
//     return hashlib.md5(str.encode()).hexdigest()






// # Member functions
function pwdgen($pwd, $salt) {
  return md5(md5($pwd)+$salt);
}
