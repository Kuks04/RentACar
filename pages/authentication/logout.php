<?php
session_start();
// Brisemo sve sesije
session_destroy();

// Brisemo cookies u slucaju da je stisnuo remember me
setcookie('id', "", time() - 3600, "/");
setcookie('fname', "", time() - 3600, "/");
setcookie('lname', "", time() - 3600, "/");
setcookie('email', "", time() - 3600, "/");
setcookie('pass', "", time() - 3600, "/");
setcookie('img', "", time() - 3600, "/");
setcookie('admin', "", time() - 3600, "/");

echo "<script>window.location='../../index.php'</script>";
?>