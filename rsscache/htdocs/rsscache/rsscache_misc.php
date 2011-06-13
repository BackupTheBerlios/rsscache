<?php
if (!defined ('TV2_MISC_PHP'))
{
define ('TV2_MISC_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('misc/wikipedia.php');
require_once ('phpqrcode/qrlib.php');
require_once ('tv2_sql.php');


function
tv2_get_request_value ($name)
{
  // wrapper for get_request_value() with hacks
  global $tv2_default_category;

  $v = get_request_value ($name);

  if ($name == 'c')
    {
      if ($v == '')
        $v = $tv2_default_category;
    }

  return $v;
}


function
tv2_get_category ()
{
  return tv2_get_request_value ('c');
}


function
tv2_f_local ()
{
  $c = tv2_get_request_value ('c');
  $config = config_xml_by_category ($c);
//  return widget_embed ($config->embed, WIDGET_EMBED_AUTO);
  return widget_embed ($config->local, WIDGET_EMBED_LOCAL);
}


function
tv2_f_iframe ()
{
  $c = tv2_get_request_value ('c');
  $config = config_xml_by_category ($c);
//  return widget_embed ($config->embed, WIDGET_EMBED_AUTO);
  return widget_embed ($config->iframe, WIDGET_EMBED_IFRAME);
}


function
tv2_f_proxy ()
{
  $c = tv2_get_request_value ('c');        
  $config = config_xml_by_category ($c);      
  return widget_embed ($config->proxy, WIDGET_EMBED_PROXY);
}


function
tv2_f_wiki ()
{
  $c = tv2_get_request_value ('c');        
  $config = config_xml_by_category ($c);      
//  return widget_wikipedia ($config->wiki);
  return wikipedia_get_html ($config->wiki);
}


function
tv2_f_localwiki ()
{
  $c = tv2_get_request_value ('c');        
  $config = config_xml_by_category ($c);      
  return widget_embed ($config->localwiki, WIDGET_EMBED_PROXY);
}


/*
    [0] => Array
        (
            [rsstool_url] => http://www.own3d.tv/watch/83483
            [rsstool_url_crc32] => 2358663608
            [rsstool_title] => CptWipe [id:32728] Archive (2011-03-07 00:08:21 - 00:11:10)
            [rsstool_desc] => 
									 							
            [rsstool_dl_date] => 1299456810
            [rsstool_date] => 1299453060
            [tv2_category] => wow
            [tv2_moved] => wow
            [rsstool_media_duration] => 0
            [rsstool_keywords] => cptwipe 32728 archive 2011
            [tv2_demux] => 12
        )
*/
function
//tv2_stripdir ($url, $start, $num)
tv2_stripdir ($url)
{
  global $tv2_tor_enabled;

  $v = array ();

  if (widget_media_demux ($url) != 0)
    {
      $v[] = $url;
      return $v;
    }

  if ($tv2_tor_enabled)
    $s = tor_get_contents ($url);
  else
    $s = file_get_contents ($url);

  $count = 0;
  $html = str_get_html ($s);
  $a = $html->find ('a');
  if ($a)
    foreach ($html->find('a') as $tag)
      if (widget_media_demux ($url.'/'.$tag->href) != 0)
        {
//          if ($count > $start)
            $v[] = $url.'/'.$tag->href;
          $count++;
//          if ($count - $start > $num)
//            break;
        }

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($v);

  return $v;
}


//function
//tv2_f_index ()
//{
//  $c = tv2_get_request_value ('c');        
//  $config = config_xml_by_category ($c);      
//  return widget_embed ($config->index, WIDGET_EMBED_INDEX);
//}


//function
//tv2_f_stripdir ()
//{
//  $c = tv2_get_request_value ('c');        
//  $config = config_xml_by_category ($c);      
//  return widget_embed ($config->index, WIDGET_EMBED_INDEX);
//}


function
config_xml_normalize ($config)
{
  global $tv2_use_database;

  if ($tv2_use_database == 1)
    {
      $stats = tv2_sql_stats ();

      // add new variables
      $config->items = $stats['items'];
      $config->items_today = $stats['items_today'];
      $config->items_7_days = $stats['items_7_days'];
      $config->items_30_days = $stats['items_30_days'];
      $config->days = $stats['days'];

      for ($i = 0; isset ($config->category[$i]); $i++)
        if ($config->category[$i]->query)
          {
            $a = array();
            parse_str ($config->category[$i]->query, $a);
            if (isset ($a['c']))
              {
                $stats = tv2_sql_stats ($config->category[$i]->name);
    
                $config->category[$i]->items = $stats['items'];
                $config->category[$i]->items_today = $stats['items_today'];
                $config->category[$i]->items_7_days = $stats['items_7_days'];
                $config->category[$i]->items_30_days = $stats['items_30_days'];
                $config->category[$i]->days = $stats['days'];
              }
          }
    }

  for ($i = 0; isset ($config->category[$i]); $i++)
    {
      $category = $config->category[$i];
      $category->tooltip = 
                 ($category->tooltip ? $category->tooltip : $category->title)
                .($category->items ? ', '.$category->items.' <!-- lang:items -->' : '')
                .($category->days ? ', '.$category->days.' <!-- lang:days -->' : '');
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
  global $tv2_use_database;
  global $tv2_config_xml;
  static $config = NULL;

  if ($config)
    return $config;

if ($memcache_expire > 0)
  {
    $memcache = new Memcache;
    if ($memcache->connect ('localhost', 11211) == TRUE)
      {
        // data from the cache
        $p = $memcache->get (md5 ($tv2_config_xml));

        if ($p != FALSE)
          {
            $p = unserialize ($p);

            // DEBUG
//            echo 'cached';

            echo $p;

            if ($tv2_use_database)
              tv2_sql_close ();

            exit;
          }
      }
    else
      {
        echo 'ERROR: could not connect to memcached';

        if ($tv2_use_database)
          tv2_sql_close ();

        exit;
      }
  }

  // DEBUG
//  echo 'read config';

  $config = simplexml_load_file ($tv2_config_xml);
  $config = config_xml_normalize ($config);

  // use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ($tv2_config_xml), serialize ($config), 0, $memcache_expire);
  }

  return $config;
}


function
config_xml_by_category ($category)
{
  $config = config_xml ();

  for ($i = 0; isset ($config->category[$i]); $i++)
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
tv2_robots ()
{
  header ('Content-type: text/plain');
  $p .= '';
  $p .= 'Sitemap: http://'.$_SERVER['SERVER_NAME'].'/sitemap.xml'."\n"
       .'User-agent: *'."\n"
       .'Allow: /'."\n";

  return $p;
}


function
tv2_sitemap_video_func ($category_name, $d_array)
{
  global $tv2_link;
  global $tv2_thumbnails_prefix;
  $p = '';

  for ($i = 0; isset ($d_array[$i]); $i++)
    if ($category_name == $d_array[$i]['tv2_moved'])
    {
      $d = $d_array[$i];
      $p .= '<video:video>'."\n";
      $p .= ''
           .'<video:thumbnail_loc>'
           .tv2_link_normalize ($tv2_link.'/thumbnails/'.$tv2_thumbnails_prefix.'tv2/'.$d['rsstool_url_crc32'].'.jpg')
           .'</video:thumbnail_loc>'."\n"
           .'<video:title>'.$d['rsstool_title'].'</video:title>'."\n"
           .'<video:description>'.$d['rsstool_desc'].'</video:description>'."\n"
           .'<video:duration>'.$d['rsstool_media_duration'].'</video:duration>'."\n"
;
      $p .= '</video:video>'."\n";
    }

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
       .' xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"'
       .'>'."\n";

  for ($i = 0; isset ($config_xml->category[$i]); $i++)
    if (trim ($config_xml->category[$i]->name) != '')
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
         .tv2_sitemap_video_func ($config_xml->category[$i]->name, $d_array)
         .'</url>'."\n";
  $p .= '</urlset>';

  return $p;
}


function
tv2_qrcode ($data, $size = 2, $level = 'L')
{
  global $tv2_cache_dir;
  global $tv2_cache_web;

  // error correction level
  //   L - smallest
  //   M
  //   Q
  //   H - best
  if (!in_array ($level, array ('L', 'M', 'Q', 'H')))
    $level = 'L';

  // matrix point size
  $size = min (max ((int) $size, 1), 10);

  $data = trim ($data);

  $f = 'qrcode_'.md5 ($data.'_'.$level.'_'.$size).'.png';

  if (!file_exists ($tv2_cache_dir.'/'.$f))
    QRcode::png ($data, $tv2_cache_dir.'/'.$f, $level, $size, 2);    

  header ('Content-type: image/png');
  echo file_get_contents ($tv2_cache_web.'/'.$f);
}


}


?>