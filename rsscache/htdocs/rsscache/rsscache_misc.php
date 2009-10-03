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
  for ($i = 0; $config->category[$i]; $i++)
    for ($j = 0; $config->category[$i]->query[$j]; $j++)
//      if ($config->category[$i]->query[$j]->name)
        if ($config->category[$i]->query[$j]->name == 'c')
          {
            // add new variables
            $config->category[$i]->videos = tv2_get_num_videos ($config->category[$i]->name);
            $config->category[$i]->days = tv2_get_num_days ($config->category[$i]->name);
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



}


?>