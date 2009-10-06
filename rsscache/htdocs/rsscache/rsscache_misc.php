<?php
if (!defined ('TV2_MISC_PHP'))
{
define ('TV2_MISC_PHP', 1);
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('tv2_sql.php');


function
config_xml_normalize ($config)
{
  $stats = tv2_sql_stats ();

  // add new variables
  $config->videos = $stats['videos'];
  $config->days = $stats['days'];

  for ($i = 0; $config->category[$i]; $i++)
    for ($j = 0; $config->category[$i]->query[$j]; $j++)
//      if ($config->category[$i]->query[$j]->name)
        if ($config->category[$i]->query[$j]->name == 'c')
          {
            $stats = tv2_sql_stats ($config->category[$i]->name);

            $config->category[$i]->videos = $stats['videos'];
            $config->category[$i]->days = $stats['days'];
          }

  return $config;
}


function
config_xml ($memcache_expire = 0)
{
  static $config = NULL;

  if ($config)
    return $config;

  if ($memcache_expire > 0)
    {
      $memcache = new Memcache;  
      $memcache->connect ('localhost', 11211) or die ("memcache: could not connect");

      // data from the cache
      $p = $memcache->get (md5 ('config.xml'));
      if ($p)
        {
          $config = // unserialize (
$p
//)
;
   
          if ($config)
            {
              // DEBUG
//              echo 'cached';
              return $config;
            } 
        }
    }

  // DEBUG
//  echo 'read config';

  $config = simplexml_load_file ('config.xml');
  $config = config_xml_normalize ($config);

  // use memcache
  if ($memcache_expire > 0)
    {
      $memcache->set (md5 ('config.xml'), // serialize (
$config
//)
)
        or die ("memcache: failed to save data at the server");
    }   

  return $config;
}


function
config_xml_by_category ($category)
{
  $config = config_xml ();

  for ($i = 0; $config->category[$i]; $i++)
    if (!strcmp ($config->category[$i]->name, $category))
      return $config->category[$i];

  return NULL;
}


// HACK
function
tv2_normalize ($category)
{
  $p = strtolower ($category);

  if ($p == 'baseq3')
    $category = 'quake3';
  else if ($p == 'baseqz')
    $category = 'quakelive';

  return $category;
}


function
tv2_rss ($d_array)
{
  global $tv2_link;
  global $tv2_name;
  global $tv2_title;

//    header ('Content-type: text/xml');
    header ('Content-type: application/xml');
//    header ('Content-type: text/xml-external-parsed-entity');
//    header ('Content-type: application/xml-external-parsed-entity');
//    header ('Content-type: application/xml-dtd');

  $rss_title_array = array ();
  $rss_link_array = array ();
  $rss_desc_array = array ();

  for ($i = 0; isset ($d_array[$i]); $i++)
    {
      $rss_title_array[$i] = $d_array[$i]['rsstool_title'];
      $rss_link_array[$i] = $d_array[$i]['rsstool_url'];
      $rss_desc_array[$i] = $d_array[$i]['rsstool_desc'];
    }

  // DEBUG
//  print_r ($rss_title_array);
//  print_r ($rss_link_array);
//  print_r ($rss_desc_array);

  echo generate_rss ($tv2_name,
                     $tv2_link,
                     $tv2_title,
                     $rss_title_array, $rss_link_array, $rss_desc_array);
}


function
tv2_get_captcha_s ($len)
{
  $t = microtime () * time ();
  $s = md5 ($t);
  return substr ($s, 0, $len);
}


function
tv2_captcha_image ($captcha_s)
{
  header ("Content-type: image/png");

  $captcha = imagecreatefrompng ('images/trans.png');
  $black = imagecolorallocate ($captcha, 0, 0, 0);
  $line = imagecolorallocate ($captcha, 233, 239, 239);
  imageline ($captcha, 0, 0, 39, 29, $line);
  imageline ($captcha, 40, 0, 64, 29, $line);
  imagestring ($captcha, 5, 20, 10, $captcha_s, $black);
  imagepng ($captcha);
}


function
tv2_captcha ($len)
{
//  $t = microtime () * time ();
//  $s = md5 ($t);
//  $s = substr ($s, 0, $len);

  // set session object or cookie with CAPTCHA
/*
  session_start ();

  $captcha = imagecreatefrompng('captcha.png');
  $black = imagecolorallocate ($captcha, 0, 0, 0);
  $line = imagecolorallocate ($captcha, 233, 239, 239);
  imageline ($captcha, 0, 0, 39, 29, $line);
  imageline ($captcha, 40, 0, 64, 29, $line);
  imagestring ($captcha, 5, 20, 10, $string, $black);

  $_SESSION['key'] = md5($string);

//header ("Content-type: image/png");
//imagepng ($captcha);
*/

/*
session_start();

//Encrypt the posted code field and then compare with the stored key

if(md5($_POST['widget_captcha']) != $_SESSION['key'])
{
  die("Error: You must enter the code correctly");
}else{
  echo 'You entered the code correctly';
}
*/

//  $p .= $s;
//  $p .= '<input type="text" size="'.$len.'" maxsize="'.$len.'" name="widget_captcha">';

//  return $p;
  return '';
}


}


?>