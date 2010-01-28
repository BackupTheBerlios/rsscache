<?php
//phpinfo();
//error_reporting(E_ALL | E_STRICT);

if (!strncmp ($_SERVER['HTTP_HOST'], 'demos.', 6) ||
    !strncmp ($_SERVER['SERVER_NAME'], 'demos.', 6))
  require_once ('demos.php');
else
  require_once ('tv2.php');

?>