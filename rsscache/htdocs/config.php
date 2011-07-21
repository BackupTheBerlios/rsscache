<?php
if (!defined ('_TV2_CONFIG_PHP'))
{
define ('_TV2_CONFIG_PHP', 1);

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
  $p = 'tv2_domain';

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
  $p = 'tv2_subdomain';

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


$tv2_domain = get_hostname ();
$tv2_subdomain = get_subdomain ();
// DEBUG
//echo '$tv2_domain=='.$tv2_domain."<br>\n";
//echo '$tv2_subdomain=='.$tv2_subdomain."<br>\n";

// HACK

if (in_array ($tv2_subdomain, array ('', 'www', 'pwnoogle')))
  $tv2_subdomain = 'videos';
if (isset ($_SERVER['SERVER_NAME']))
  if ($_SERVER['SERVER_NAME'] == 'pwnoogle.com')
    $tv2_subdomain = 'videos';

if (isset ($_SERVER['PWD']))
  $pwd = $_SERVER['PWD']; 
else  
  $pwd = $_SERVER['DOCUMENT_ROOT'];

if (!file_exists ($pwd.'/'.$tv2_subdomain.'_config.php'))
  $tv2_subdomain = 'videos';

require_once ($tv2_subdomain.'_config.php');

}


?>