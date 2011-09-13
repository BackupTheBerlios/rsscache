<?php
/*
rsscache.php - rsscache engine miscellaneous functions

Copyright (c) 2009 - 2011 NoisyB 


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of   
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
  
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
*/
if (!defined ('RSSCACHE_MISC_PHP'))
{
define ('RSSCACHE_MISC_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
//require_once ('misc/misc.php');
//require_once ('misc/wikipedia.php');
//require_once ('misc/rss.php');
//require_once ('misc/sql.php');
//require_once ('misc/youtube.php');
require_once ('rsscache_sql.php');
require_once ('rsscache_output.php');


function
rsscache_get_request_value ($name)
{
  // wrapper for get_request_value() 
  global $rsscache_default_category;
  global $rsscache_default_function;

  $v = get_request_value ($name);

  if ($name == 'c')
    {
      if ($v == '')
        $v = $rsscache_default_category;
    }
  else if ($name == 'f')
    {
      if ($v == '')  
        $v = $rsscache_default_function;
    }

  return $v;
}


function
config_xml_by_category ($c)
{
  $config = config_xml ();

  for ($i = 0; isset ($config['item'][$i]); $i++)
    if (isset ($config['item'][$i]['category']))
      if (trim ($config['item'][$i]['category']) == $c)
        return $config['item'][$i];
  return NULL;
}


function
config_xml_normalize ($config)
{
  // TODO: merge multiple configs
//  $config = $config[0];

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($config);
//exit;

  // turn XML into array
  for ($i = 0; isset ($config[$i]); $i++)
    $a[] = rss2array ($config[$i]);

  // merge multiple config XML
  for ($i = 1; isset ($a[$i]); $i++)
    {
      for ($j = 0; isset ($a[$i]['item'][$j]); $j++)
        $a[0]['item'][] = $a[$i]['item'][$j];
//      unset ($a[$i]);
    }

  $a = $a[0];
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);
//exit;

  // sanity check
/*
  $insane = 0;
  for ($i = 0; isset ($a['item'][$i]); $i++)
    for ($j = $i + 1; isset ($a['item'][$j]); $j++)
      if (trim ($a['item'][$i]['category']) == trim ($a['item'][$j]['category']) &&  trim ($a['item'][$j]['category']) != '')
        {
          echo 'ERROR: duplicate category: '.$a['item'][$j]['category']."<br>";
          $insane = 1;
        }
  if ($insane == 1)
    exit;
*/

  // add db statistics
  $a['channel']['rsscache:stats_items'] = 0;
  $a['channel']['rsscache:stats_items_today'] = 0;
  $a['channel']['rsscache:stats_items_7_days'] = 0;
  $a['channel']['rsscache:stats_items_30_days'] = 0;
  $a['channel']['rsscache:stats_days'] = 0;

  // DEBUG
//echo count ($a['item']);
//exit;
//rsscache_sql ($c, $q, $f, $v, $start, $num)
  $stats = rsscache_sql (NULL, NULL, 'stats', NULL, 0, count ($a['item']));
  for ($j = 0; isset ($stats[$j]); $j++)
    {
      for ($i = 0; isset ($a['item'][$i]); $i++)
        if (isset ($a['item'][$i]['category']))
          if ($stats[$j]['stats_category'] == $a['item'][$i]['category'])
          {
            $a['item'][$i] = array_merge ($a['item'][$i],
              misc_prefixate_array ($stats[$j], 'rsscache:'));
            break;
          }

      $a['channel']['rsscache:stats_items'] += $stats[$j]['stats_items'];
      $a['channel']['rsscache:stats_items_today'] += $stats[$j]['stats_items_today'];
      $a['channel']['rsscache:stats_items_7_days'] += $stats[$j]['stats_items_7_days'];
      $a['channel']['rsscache:stats_items_30_days'] += $stats[$j]['stats_items_30_days'];
      $a['channel']['rsscache:stats_days'] += $stats[$j]['stats_days'];
    }

  // DEBUG
//  echo generate_rss2 ($a['channel'], $a['item'], 1, 1);
//  exit;

  return $a;
}


function
config_xml ($memcache_expire = 0)
{
  global $rsscache_config_xml;

  static $config = NULL;

  if ($config)
    return $config;

if ($memcache_expire > 0)
  {
    $memcache = new Memcache;
    if ($memcache->connect ('localhost', 11211) == TRUE)
      {
        // data from the cache
        $p = $memcache->get (md5 ($rsscache_config_xml[0]));

        if ($p != FALSE)
          {
            $p = unserialize ($p);

            // DEBUG
//            echo 'cached';

            echo $p;

            rsscache_sql_close ();

            exit;
          }
      }
    else
      {
        echo 'ERROR: could not connect to memcached';

        rsscache_sql_close ();

        exit;
      }
  }

  // DEBUG
//  echo 'read config';
  $config = array ();
  if (!is_array ($rsscache_config_xml))
    $config[] = simplexml_load_file ($rsscache_config_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
  else
    for ($i = 0; isset ($rsscache_config_xml[$i]); $i++)
      $config[] = simplexml_load_file ($rsscache_config_xml[$i], 'SimpleXMLElement', LIBXML_NOCDATA);

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($config);
//exit;
  $config = config_xml_normalize ($config);

  // use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ($rsscache_config_xml[0]), serialize ($config), 0, $memcache_expire);
  }

  return $config;
}


function
rsscache_download_thumbnails ($xml)
{
  global $wget_path;
  global $wget_opts;
  global $rsscache_domain;
//  global $debug;
  $debug = 1;
  
  for ($i = 0; isset ($xml->item[$i]); $i++)
    {
      // DEBUG
//  print_r ($xml->item[$i]);
//  exit;
      $s = NULL;
      if (trim ($xml->item[$i]->media_image) != '')
        $s = $xml->item[$i]->media_image;

      if (strstr ($xml->item[$i]->url, '.youtube.')) // HACK: prefer smaller yt thumbnails
        {
          $a = youtube_get_thumbnail_urls ($xml->item[$i]->url);
          $s = $a[0];
        }

      if ($s == NULL)
        {
//          unset ($xml->item[$i]); // drop this item
          continue; // no thumbnail url
        }

      $result = 0;
      $noclobber = 1;

      $p = '../htdocs/thumbnails/rsscache/'.$xml->item[$i]->url_crc32.'.jpg';
//      echo 'media image: '.$s.' ('.$p.')'."\n";
      $result = misc_exec_wget ($s, $p, $noclobber, $wget_path, $wget_opts, $debug);
//      $result = misc_download_noclobber ($s, $p);
//      0 = ok, 1 = thumbnail did exist download skipped, -1 = error

//      if ($rsscache_domain != 'debian2')
        {
          if ($result == 1)
            {
              $xml->item[$i]->url = ''; // drop this item

              // copy thumbnails to a different directory
//              $s = 'cp '.$p.' ../htdocs/thumbnails/rsscache_/';
              // DEBUG
//              echo $s."\n";
//              echo misc_exec ($s, $debug);
            }   
//          if ($result == -1)
//            $xml->item[$i]->url = ''; // drop this item
        }
    }

//  $xml->item = misc_array_unique_merge ($xml->item);

  return $xml;
}


function
rsscache_feed_get ($client = NULL, $opts, $url)
{
  global $rsscache_user_agent;
  global $rsstool_path;
  global $rsstool_opts;
  $debug = 0;

  $tmp = tempnam (sys_get_temp_dir (), 'rsscache_');

  // DEBUG
//  echo $tmp."\n";

  if ($client)
    $p = $client.' '.$opts.' "'.$url.'" > '.$tmp;
  else  // default: download and parse feed with rsstool and write proprietary XML
    $p = $rsstool_path.' '.$rsstool_opts.' -u "'.$rsscache_user_agent.'" '.$opts.' --xml "'.$url.'" -o '.$tmp;

  // DEBUG
  echo $p."\n";
  echo misc_exec ($p, $debug);

  $xml = simplexml_load_file ($tmp, 'SimpleXMLElement', LIBXML_NOCDATA);

  // DEBUG
//  print_r ($xml);
//  exit;
   
  unlink ($tmp);

  return $xml;
}


function
rsscache_download_feeds_by_category ($c)
{
  global $rsscache_sql_db;
  global $rsscache_user_agent;

  $c = trim ($c);
  $category = config_xml_by_category ($c);

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($category);
//exit;

  if ($category == NULL)
    return;

  for ($j = 0; isset ($category['rsscache:feed_'.$j.'_link']); $j++)
    {
      // rsstool options
      $opts = '';
      if (isset ($category['rsscache:feed_'.$j.'_opts']))
        $opts = $category['rsscache:feed_'.$j.'_opts'];

      $p = '';
      $p .= 'category: '.$c."\n"
           .'client: '.(isset ($category['rsscache:feed_'.$j.'_client']) ? $category['rsscache:feed_'.$j.'_client'] : '')."\n"
           .'opts: '.$opts."\n"
           .'url: '.$category['rsscache:feed_'.$j.'_link']."\n"
           .'table_suffix: '.(isset ($category['rsscache:table_suffix']) ? $category['rsscache:table_suffix'] : '')."\n"
; 
      echo $p;

      // get feed
      $xml = rsscache_feed_get ((isset ($category['rsscache:feed_'.$j.'_client']) ? $category['rsscache:feed_'.$j.'_client'] : ''),
                                $opts, $category['rsscache:feed_'.$j.'_link']);
      // download thumbnails
      $xml = rsscache_download_thumbnails ($xml);
      // xml to sql
      $sql_queries_s = rsstool_write_ansisql ($xml, $c, 
        (isset ($category['rsscache:table_suffix']) ? $category['rsscache:table_suffix'] : ''),
        $rsscache_sql_db->conn);

      rsscache_sql_queries ($sql_queries_s);
    }
}


function
rsscache_title ($d = NULL)
{
  global $rsscache_title;
  global $rsscache_time;
  $v = rsscache_get_request_value ('v');
  $c = rsscache_get_request_value ('c');
  $a = array ();
  if (trim ($rsscache_title) != '')
    $a[] = $rsscache_title
          .' 0.9.6beta-'.sprintf ("%u", $rsscache_time)
;

  if (trim ($c) != '')
    {
      $category = config_xml_by_category ($c);
      if ($category)
        if (trim ($category['title']) != '')
          $a[] = $category['title'];
    }

  if ($v && $d != NULL)
    $a[] = $d['rsstool_title'];

  return implode (' - ', $a);
}


function
rsscache_link ($d)
{
  // checks is file is on local server or on static server and returns correct link
  global $rsscache_root,
         $rsscache_link,
         $rsscache_link_static;
  $link = urldecode ($d['rsstool_url']);
  $p = $link;

  if (strncmp ($p, $rsscache_link, strlen ($rsscache_link)) || // extern link
      !$rsscache_link_static) // no static server
    return $link;

  $p = str_replace ($rsscache_link, $rsscache_root, $link); // file on local server?
  if (file_exists ($p))
    return $link;

  return str_replace ($rsscache_link, $rsscache_link_static, $link); // has to be on static server then
}


function
rsscache_duration ($d)
{
  if ($d['media_duration'] > 0)
    return gmstrftime ($d['media_duration'] > 3599 ? '%H:%M:%S' : '%M:%S', (int) $d['media_duration']);
  return '';
}


function
rsscache_thumbnail ($d)
{
  global $rsscache_link_static,
         $rsscache_thumbnails_prefix;
  return $rsscache_link_static.'/thumbnails/'.$rsscache_thumbnails_prefix.'/rsscache/'.$d['rsstool_url_crc32'].'.jpg';
}


function
rsscache_default_channel ()
{
  global $rsscache_link;
  global $rsscache_time;
  global $rsscache_logo;
  global $rsscache_results;

  $config = config_xml ();

  $p = ''
      .'&amp;q=SEARCH&nbsp;&nbsp;   SEARCH query<br>'
      .'&amp;start=N&nbsp;&nbsp;    start from result N<br>'
      .'&amp;num=N&nbsp;&nbsp;      show N results (default: '.$rsscache_results.')<br>'
      .'&amp;c=NAME&nbsp;&nbsp;     category (leave empty for all categories)<br>'
      .'&amp;item=URL_CRC32&nbsp;&nbsp; show single item<br>'
      .'&amp;f=FUNC&nbsp;&nbsp;     execute FUNCtion<br>'
      .'&amp;output=FORMAT&nbsp;&nbsp; output in "rss", "mediawiki", "json", "playlist" (admin) or "html" (default: rss)<br>'
//      .'&amp;prefix=SUBDOMAIN&nbsp;&nbsp; prefix or SUBDOMAIN (leave empty for current subdomain)<br>'
      .'<br>'           
      .'*** functions ***<br>'
      .'&amp;f=author&nbsp;&nbsp;   find user/author/channel (requires &amp;q=SEARCH)<br>'
      .'&amp;<a href="?f=0_5min&output=html">f=0_5min</a>&nbsp;&nbsp;   media with duration 0-5 minutes<br>'
      .'&amp;<a href="?f=5_10min&output=html">f=5_10min</a>&nbsp;&nbsp;  media with duration 5-10 minutes<br>'
      .'&amp;<a href="?f=10_30min&output=html">f=10_30min</a>&nbsp;&nbsp; media with duration 10-30 minutes<br>'
      .'&amp;<a href="?f=30_60min&output=html">f=30_60min</a>&nbsp;&nbsp; media with duration 30-60 minutes<br>'
      .'&amp;<a href="?f=60min&output=html">f=60_min</a>&nbsp;&nbsp;   media with duration 60+ minutes<br>'
      .'&amp;<a href="?f=new&output=html">f=new</a>&nbsp;&nbsp;      show only newly created items (default: download time)<br>'
      .'&amp;f=related&nbsp;&nbsp;  find related items (requires &amp;q=RELATED_ID)<br>'
      .'&amp;<a href="?f=stats&output=html">f=stats</a>&nbsp;&nbsp;    statistics<br>'
//      .'&f=error404&nbsp;&nbsp;    <br>'
//      .'&f=error304&nbsp;&nbsp;    <br>'
//      .'&f=error300&nbsp;&nbsp;    <br>'
      .'<br>'
      .'*** admin functions ***<br>'
      .'&amp;<a href="?f=sitemap">f=sitemap</a>&nbsp;&nbsp;  sitemap.xml<br>'  
      .'&amp;<a href="?f=robots">f=robots</a>&nbsp;&nbsp;  robots.txt<br>'
      .'<br>'
      .'requires access to <a href="admin.php?output=html">admin.php</a>:<br>'
      .'&amp;<a href="?f=cache&output=html">f=cache</a>&nbsp;&nbsp;    cache (new) items into database (requires &amp;c=CATEGORY)<br>'
      .'&amp;<a href="?f=config&output=html">f=config</a>&nbsp;&nbsp;  indent and dump config.xml<br>'
      .'&amp;<a href="?output=playlist">output=playlist</a>&nbsp;&nbsp;  generate playlist.txt<br>'
      .'<br>'
      .'*** install ***<br>'
      .'see apache2/sites-enabled/rsscache<br>'
;
  $channel = array ('title' => rsscache_title (),
                    'link' => $rsscache_link,
                    'description' => $p,
                    'image' => $rsscache_logo,
                    'lastBuildDate' => $rsscache_time,
                    'docs' => $rsscache_link,
                    'rsscache:stats_items' => ($config['channel']['rsscache:stats_items'] * 1),
                    'rsscache:stats_days' => ($config['channel']['rsscache:stats_days'] * 1),
                    'rsscache:stats_items_today' => ($config['channel']['rsscache:stats_items_today'] * 1),
                    'rsscache:stats_items_7_days' => ($config['channel']['rsscache:stats_items_7_days'] * 1),
                    'rsscache:stats_items_30_days' => ($config['channel']['rsscache:stats_items_30_days'] * 1),
);
  return $channel;
}


}


?>