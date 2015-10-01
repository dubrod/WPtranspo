<?php
$mysqli = new mysqli("localhost", "USERNAME", "PASSWROD", "DB_NAME");
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
} else {

}
?>
