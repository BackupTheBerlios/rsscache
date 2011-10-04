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
rsscache_item_has_feed ($item, $j = 0)
{
  return (isset ($item['rsscache:feed_'.$j.'_link']) ||
      isset ($item['rsscache:feed_'.$j.'_exec']));
}


function
rsscache_add_stats ($a)
{

  // add db statistics
  $a['channel']['rsscache:stats_items'] = 0;
  $a['channel']['rsscache:stats_items_today'] = 0;
  $a['channel']['rsscache:stats_items_7_days'] = 0;
  $a['channel']['rsscache:stats_items_30_days'] = 0;
  $a['channel']['rsscache:stats_days'] = 0;

  $stats = rsscache_sql_stats ();

// DEBUG
//echo '<pre><tt>';
//print_r ($stats);
//exit;

  for ($i = 0; isset ($stats[$i]); $i++)
    {
      for ($j = 0; isset ($a['item'][$j]); $j++)
        if (isset ($a['item'][$j]['category']))
          if ($a['item'][$j]['category'] == $stats[$i]['stats_category'])
            {
              $a['item'][$j] = array_merge ($a['item'][$j], misc_prefixate_array ($stats[$i], 'rsscache:'));
              break;
            }

        $a['channel']['rsscache:stats_items'] += $stats[$i]['stats_items'];
        $a['channel']['rsscache:stats_items_today'] += $stats[$i]['stats_items_today'];
        $a['channel']['rsscache:stats_items_7_days'] += $stats[$i]['stats_items_7_days'];
        $a['channel']['rsscache:stats_items_30_days'] += $stats[$i]['stats_items_30_days'];
        $a['channel']['rsscache:stats_days'] += $stats[$i]['stats_days'];
      }

  return $a;
}

function
rsscache_tablename ($table_prefix, $table_suffix = NULL)
{
  if ($table_suffix)
    if (trim ($table_suffix) != '')
      return $table_prefix.'_table_'.$table_suffix;
  return $table_prefix.'_table';
}


function
rsscache_tablename_by_category ($table_prefix, $c = NULL)
{
  $category = NULL;
  if ($c)
    {
      $category = config_xml_by_category ($c);
// DEBUG
//echo '<pre><tt>';
//print_r ($category);
//exit;
      if ($category)
        if (isset ($category['rsscache:table_suffix']))
          return rsscache_tablename ($table_prefix, $category['rsscache:table_suffix']);
   }

  return rsscache_tablename ($table_prefix, NULL);
}


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
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);
//  echo generate_rss2 ($a['channel'], $a['item'], 1, 1);
//  exit;

  return array ('channel' => rsscache_default_channel (), 'item' => $a['item']);
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
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($config);
//exit;

  // use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ($rsscache_config_xml[0]), serialize ($config), 0, $memcache_expire);
  }

  return $config;
}


function
rsscache_download_thumbnails ($a)
{
  global $wget_path;
  global $wget_opts;
  global $rsscache_domain;
//  global $debug;
  $debug = 1;
  
  for ($i = 0; isset ($a['item'][$i]); $i++)
    {
      // DEBUG
//  print_r ($a['item'][$i]);
//  exit;
      $s = NULL;
      if (trim ($a['item'][$i]->media_image) != '')
        $s = $a['item'][$i]->media_image;

      if (strstr ($a['item'][$i]['link'], '.youtube.')) // HACK: prefer smaller yt thumbnails
        {
          $a = youtube_get_thumbnail_urls ($a['item'][$i]['link']);
          $s = $a[0];
        }

      if ($s == NULL)
        {
//          unset ($a['item'][$i]); // drop this item
          continue; // no thumbnail url
        }

      $result = 0;
      $noclobber = 1;

      $p = '../htdocs/thumbnails/rsscache/'.$a['item'][$i]['rsscache:url_crc32'].'.jpg';
//      echo 'media image: '.$s.' ('.$p.')'."\n";
      $result = misc_exec_wget ($s, $p, $noclobber, $wget_path, $wget_opts, $debug);
//      $result = misc_download_noclobber ($s, $p);
//      0 = ok, 1 = thumbnail did exist download skipped, -1 = error

//      if ($rsscache_domain != 'debian2')
        {
          if ($result == 1)
            {
              $a['item'][$i]['link'] = ''; // drop this item

              // copy thumbnails to a different directory
//              $s = 'cp '.$p.' ../htdocs/thumbnails/rsscache_/';
              // DEBUG
//              echo $s."\n";
//              echo misc_exec ($s, $debug);
            }   
//          if ($result == -1)
//            $a['item'][$i]['link'] = ''; // drop this item
        }
    }

//  $a['item'] = misc_array_unique_merge ($a['item']);

  return $a;
}


function
rsscache_download_feeds_by_category ($c)
{
  global $rsscache_sql_db;

  $c = trim ($c);
  $category = config_xml_by_category ($c);

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($category);
//exit;

  if ($category == NULL)
    return;

  if (!rsscache_item_has_feed ($category))
    return;

  $table_suffix = (isset ($category['rsscache:table_suffix']) ? $category['rsscache:table_suffix'] : NULL);
  for ($j = 0; rsscache_item_has_feed ($category, $j); $j++)
    {
      $exec = (isset ($category['rsscache:feed_'.$j.'_exec']) ? $category['rsscache:feed_'.$j.'_exec'] : '');
      $link = (isset ($category['rsscache:feed_'.$j.'_link']) ? '"'.$category['rsscache:feed_'.$j.'_link'].'"' : '');

      $p = '';
      $p .= 'category: '.$c."\n"
           .'exec: '.$exec."\n"
           .'link: '.$link."\n"
           .'table_suffix: '.($table_suffix ? $table_suffix : '(none)')."\n"
; 
      echo $p;

      // get feed
      $p = $exec.' '.$link;
      // DEBUG
//      echo $p;
//      exit;
      exec ($p, $a);
      $p = implode ("\n", $a);
      // DEBUG
//      echo $p;
//      exit;
      $rss = simplexml_load_string ($p, 'SimpleXMLElement', LIBXML_NOCDATA);
      // DEBUG
      print_r ($rss);
      exit;
      $a = rss2array ($rss);
      // DEBUG
      print_r ($a);
      exit;

      // download thumbnails
//      $a = rsscache_download_thumbnails ($a);

      $sql_queries_s = rsstool_write_ansisql ($a, $c, $table_suffix, $rsscache_sql_db->conn);

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
rsscache_default_channel ()
{
  global $rsscache_link;
  global $rsscache_time;
  global $rsscache_logo;
  global $rsscache_description;

  $config = config_xml ();
  $channel = array ('title' => rsscache_title (),
                    'link' => $rsscache_link,
                    'description' => str_replace (array ('  ',           "\n"),
                                                  array ('&nbsp;&nbsp;', ''), $rsscache_description),
                    'image' => $rsscache_logo,
                    'lastBuildDate' => $rsscache_time,
                    'docs' => $rsscache_link,
);
  return $channel;
}


}


?>