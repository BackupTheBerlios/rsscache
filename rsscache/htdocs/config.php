<?php
if (!defined ('_RSSCACHE_CONFIG_PHP'))
{
define ('_RSSCACHE_CONFIG_PHP', 1);

// apache2
//$_SERVER['SERVER_NAME'] == 'maps.pwnoogle.com'
//$_SERVER['USER'] unset
//$_SERVER["HOSTNAME"] => unset
//$_SERVER["PWD"] => unset
//$_SERVER["DOCUMENT_ROOT"] => /home/pwnoogle/htdocs/emulive/htdocs

// cmdline
//$_SERVER['SERVER_NAME'] unset
//$_SERVER['USER'] == 'pwnoogle'
//$_SERVER["HOSTNAME"] => stan.site5.com
//$_SERVER["PWD"] => /home/pwnoogle/htdocs/emulive/htdocs
//$_SERVER["DOCUMENT_ROOT"] => ''


function
get_hostname ()
{
  // default
  $p = 'config_domain';

//  if (isset ($_SERVER['SERVER_NAME']))
//    $p = $_SERVER['SERVER_NAME'];
//  else if (isset ($_SERVER['HOSTNAME']))
//    $p = $_SERVER['HOSTNAME'];
//  else
    $p = php_uname ('n');
  return $p;
//  return gethostname ();
}


function
get_subdomain ()
{
  // default
  $p = 'config_subdomain';

  // apache2
  if (isset ($_SERVER['SERVER_NAME']))
    if (trim ($_SERVER['SERVER_NAME']) != '')
    {
      $p = substr ($_SERVER['SERVER_NAME'], 0, strpos ($_SERVER['SERVER_NAME'], '.'));
      // DEBUG
//      echo $p;
      return $p;
    }

  // cmdline
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


$config_domain = get_hostname ();
$config_subdomain = get_subdomain ();
// DEBUG
//echo '$config_domain=='.$config_domain."<br>\n";
//echo '$config_subdomain=='.$config_subdomain."<br>\n";

// HACK
if (in_array ($config_subdomain, array ('', 'www', 'pwnoogle')))
  $config_subdomain = 'videos';
if (isset ($_SERVER['SERVER_NAME']))
  if ($_SERVER['SERVER_NAME'] == 'pwnoogle.com')
    $config_subdomain = 'videos';

if (isset ($_SERVER['PWD']))
  $pwd = $_SERVER['PWD'].'/../htdocs/'; 
else  
  $pwd = $_SERVER['DOCUMENT_ROOT'];

if (!file_exists ($pwd.'/'.$config_subdomain.'_config.php'))
  $config_subdomain = 'videos';


$config_subdomain = 'videos';
require_once ($config_subdomain.'_config.php');

}


?>