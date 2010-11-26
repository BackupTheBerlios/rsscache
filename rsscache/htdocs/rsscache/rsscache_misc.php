<?php
if (!defined ('TV2_MISC_PHP'))
{
define ('TV2_MISC_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('tv2_sql.php');


function
tv2_f_local ()
{
  $c = get_request_value ('c');
  $config = config_xml_by_category ($c);
//  return widget_embed ($config->embed, WIDGET_EMBED_AUTO);
  return widget_embed ($config->local, WIDGET_EMBED_LOCAL);
}


function
tv2_f_iframe ()
{
  $c = get_request_value ('c');
  $config = config_xml_by_category ($c);
//  return widget_embed ($config->embed, WIDGET_EMBED_AUTO);
  return widget_embed ($config->iframe, WIDGET_EMBED_IFRAME);
}


function
tv2_f_proxy ()
{
  $c = get_request_value ('c');        
  $config = config_xml_by_category ($c);      
  return widget_embed ($config->proxy, WIDGET_EMBED_PROXY);
}


function
tv2_get_category ()
{
//  global $config;
  $c = get_request_value ('c'); // category

//  if (!($c)) // default category
//    for ($i = 0; isset ($config->category[$i]); $i++)
//      if ($config->category[$i]->default == 1)
//        return $config->category[$i]->name;
//  return NULL;
  return $c;
}


function
config_xml_normalize ($config)
{
  global $tv2_use_database;
  global $tv2_videos_s;

  if ($tv2_use_database == 1)
    {
      $stats = tv2_sql_stats ();

      // add new variables
      $config->videos = $stats['videos'];
      $config->videos_today = $stats['videos_today'];
      $config->videos_7_days = $stats['videos_7_days'];
      $config->videos_30_days = $stats['videos_30_days'];
      $config->days = $stats['days'];

      for ($i = 0; isset ($config->category[$i]); $i++)
        if ($config->category[$i]->query)
          {
            $a = array();
            parse_str ($config->category[$i]->query, $a);
            if (isset ($a['c']))
              {
                $stats = tv2_sql_stats ($config->category[$i]->name);
    
                $config->category[$i]->videos = $stats['videos'];
                $config->category[$i]->videos_today = $stats['videos_today'];
                $config->category[$i]->videos_7_days = $stats['videos_7_days'];
                $config->category[$i]->videos_30_days = $stats['videos_30_days'];
                $config->category[$i]->days = $stats['days'];
              }
          }
    }

  for ($i = 0; isset ($config->category[$i]); $i++)
    {
      $category = $config->category[$i];
      $category->tooltip = 
                 ($category->tooltip ? $category->tooltip : $category->title)
                .($category->videos ? ', '.$category->videos.' '.$tv2_videos_s : '')
                .($category->days ? ', '.$category->days.' days' : '');
//      if ($category->query)
//        {
//          $b = array ();
//          parse_str ($category->query, $b);
//          $n = array_merge ($a, $b);
//          $category->query = htmlentities (http_build_query2 ($n, false));
//        }
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
    if ($memcache->connect ('localhost', 11211) == TRUE)
      {
        // data from the cache
        $p = $memcache->get (md5 ('config.xml'));

        if ($p != FALSE)
          {
            $p = unserialize ($p);

            // DEBUG
//            echo 'cached';

            echo $p;

            exit;
          }
      }
    else
      {
        echo 'ERROR: could not connect to memcached';
        exit;
      }
  }

  // DEBUG
//  echo 'read config';

  $config = simplexml_load_file ('config.xml');
  $config = config_xml_normalize ($config);

  // use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ('config.xml'), serialize ($config), 0, $memcache_expire);
  }

  return $config;
}


function
config_xml_by_category ($category)
{
  $config = config_xml ();

  for ($i = 0; $config->category[$i]; $i++)
    if ($config->category[$i]->name == $category)
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
//      $rss_link_array[$i] = $d_array[$i]['rsstool_url'];
      if (substr (tv2_link ($d_array[$i]), 0, 7) == 'http://')
        $rss_link_array[$i] = tv2_link ($d_array[$i]);
      else
        $rss_link_array[$i] = $tv2_link.'?'.tv2_link ($d_array[$i]);

      $rss_desc_array[$i] = ''
                           .tv2_thumbnail ($d_array[$i], 120, 1)
                           .'<br>'
                           .$d_array[$i]['rsstool_desc'];
    }

  // DEBUG
//  print_r ($rss_title_array);
//  print_r ($rss_link_array);
//  print_r ($rss_desc_array);

  return generate_rss ($tv2_name,
                     $tv2_link,
                     $tv2_title,
                     $rss_title_array, $rss_link_array, $rss_desc_array);
}


function
tv2_link_normalize ($link)
{
  // checks is file is on local server or on static server and returns correct link
  global $tv2_root,
         $tv2_link,
         $tv2_link_static;
  $p = $link; // $d['rsstool_url']

  if (strncmp ($p, $tv2_link, strlen ($tv2_link)) || // extern link
      !$tv2_link_static) // no static server
    return $link;

  $p = str_replace ($tv2_link, $tv2_root, $link); // file on local server?
  if (file_exists ($p))
    return $link;

  return str_replace ($tv2_link, $tv2_link_static, $link); // has to be on static server then
}


function
tv2_sitemap_video ($d_array)
{
  $p = '';
//  $p .= '<video:video>'."\n"
//       .'<video:thumbnail_loc>'.''.'</video:thumbnail_loc>'."\n"
//       .'<video:title>'.''.'</video:title>'."\n"
//       .'<video:description>'.''.'</video:description>'."\n"
//       .'</video:video>'."\n"
//;
  return $p;
}


function
tv2_sitemap ($d_array)
{
//    header ('Content-type: text/xml');
  header ('Content-type: application/xml');
//    header ('Content-type: text/xml-external-parsed-entity');
//    header ('Content-type: application/xml-external-parsed-entity');
//    header ('Content-type: application/xml-dtd');
  $config_xml = config_xml ();

//  echo '<pre>';
//  print_r ($config_xml);

  $p = '';
  $p .= '<?xml version="1.0" encoding="UTF-8"?>'."\n"
       .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
//       .' xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"'
       .'>'."\n";

  for ($i = 0; isset ($config_xml->category[$i]); $i++)
    if ($config_xml->category[$i]->name[0])
    $p .= '<url>'."\n"
         .'  <loc>http://'.$_SERVER['SERVER_NAME'].'/?c='.$config_xml->category[$i]->name.'</loc>'."\n"
/*
The formats are as follows. Exactly the components shown here must be present, with exactly this punctuation. Note that the "T" appears literally in the string, to indicate the beginning of the time element, as specified in ISO 8601.

   Year:
      YYYY (eg 1997)
   Year and month:
      YYYY-MM (eg 1997-07)
   Complete date:
      YYYY-MM-DD (eg 1997-07-16)
   Complete date plus hours and minutes:
      YYYY-MM-DDThh:mmTZD (eg 1997-07-16T19:20+01:00)
   Complete date plus hours, minutes and seconds:
      YYYY-MM-DDThh:mm:ssTZD (eg 1997-07-16T19:20:30+01:00)
   Complete date plus hours, minutes, seconds and a decimal fraction of a second
      YYYY-MM-DDThh:mm:ss.sTZD (eg 1997-07-16T19:20:30.45+01:00)
*/
         .'<lastmod>'.strftime ('%F' /* 'T%T%Z' */).'</lastmod>'."\n"
         .'<changefreq>always</changefreq>'."\n"
         .tv2_sitemap_video ($d_array)
         .'</url>'."\n";
  $p .= '</urlset>';

  return $p;
}


}


?>