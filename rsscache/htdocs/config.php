<?php
//if (!defined ('TV2_CONFIG_PHP'))
//{
//define ('TV2_CONFIG_PHP', 1);

// php apache2
//$_SERVER['SERVER_NAME'] == 'maps.pwnoogle.com'
//$_SERVER['USER'] unset
//$_SERVER["HOSTNAME"] => unset
//$_SERVER["PWD"] => unset
//$_SERVER["DOCUMENT_ROOT"] => /home/pwnoogle/htdocs/emulive/htdocs

// php cmdline
//$_SERVER['SERVER_NAME'] unset
//$_SERVER['USER'] == 'pwnoogle'
//$_SERVER["HOSTNAME"] => stan.site5.com
//$_SERVER["PWD"] => /home/pwnoogle/htdocs/emulive/htdocs
//$_SERVER["DOCUMENT_ROOT"] => ''


function
get_hostname ()
{
//  $p = 'tv2_domain';
//  if (isset ($_SERVER['SERVER_NAME']))
//    $p = $_SERVER['SERVER_NAME'];
//  else if (isset ($_SERVER['HOSTNAME']))
//    $p = $_SERVER['HOSTNAME'];
//  else
    $p = php_uname ('n');
  return $p;
//  return gethostname ();
}

$tv2_domain = get_hostname ();
// DEBUG
//echo '$tv2_domain=='.$tv2_domain."<br>\n";


function
get_subdomain ()
{
//  $p = 'tv2_subdomain';
  $p = $_SERVER['DOCUMENT_ROOT'];
  if (isset ($_SERVER['PWD']))
    $p = $_SERVER['PWD'];
  $a = explode ('/', $p); // /home/pwnoogle/htdocs/emulive/htdocs
  // DEBUG
//  print_r ($a);
  if (isset ($a[count ($a) - 2]))
    return $a[count ($a) - 2];
  return '';
}


$tv2_subdomain = get_subdomain ();
// DEBUG
//echo '$tv2_subdomain=='.$tv2_subdomain."<br>\n";


require_once ($tv2_subdomain.'_config.php');

//}


?>