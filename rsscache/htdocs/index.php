<?php
//phpinfo();
//error_reporting(E_ALL | E_STRICT);

if (!strncmp ($_SERVER['HTTP_HOST'], 'demos.', 6) ||
    !strncmp ($_SERVER['SERVER_NAME'], 'demos.', 6))
  require_once ('pwnoogle_demos.php');
else
  require_once ('tv2.php');

?>