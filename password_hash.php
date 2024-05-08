<?php

include "_includes/functions.inc";


if ($_GET["password"]) {
   $pass = $_GET["password"];
   echo password_hash($pass, PASSWORD_DEFAULT);
}

?>
