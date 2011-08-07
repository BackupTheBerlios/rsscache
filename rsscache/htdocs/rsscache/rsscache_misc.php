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
require_once ('misc/misc.php');
require_once ('misc/widget.php');
//require_once ('misc/wikipedia.php');
//require_once ('misc/rss.php');
require_once ('misc/sql.php');
require_once ('misc/youtube.php');
require_once ('rsscache_lang.php');
require_once ('rsscache_sql.php');
require_once ('rsscache_write.php'); // write RSS


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
config_xml_normalize ($config)
{
  global $rsstool_path;
  global $rsstool_opts;

//rsscache_sql ($c, $q, $f, $v, $start, $num, $table_suffix = NULL)
      $stats = rsscache_sql (NULL, NULL, 'stats', NULL, 0, count ($config->category));
      // DEBUG
//echo '<pre><tt>';
//print_r ($stats);

      // add new variables
      for ($j = 0; isset ($stats[$j]); $j++)
        {
          $config->items += $stats[$j]['items'];
          $config->items_today += $stats[$j]['items_today'];
          $config->items_7_days += $stats[$j]['items_7_days'];
          $config->items_30_days += $stats[$j]['items_30_days'];
          $config->days += $stats[$j]['days'];
        }

      for ($i = 0; isset ($config->category[$i]); $i++)
//        if ($config->category[$i]->query)
          {
//            $a = array();
//            parse_str ($config->category[$i]->query, $a);

//            if (isset ($a['c']))
              for ($j = 0; isset ($stats[$j]); $j++)
                if ($stats[$j]['category'] == $config->category[$i]->name)
              {
                $config->category[$i]->items = $stats[$j]['items'];
                $config->category[$i]->items_today = $stats[$j]['items_today'];
                $config->category[$i]->items_7_days = $stats[$j]['items_7_days'];
                $config->category[$i]->items_30_days = $stats[$j]['items_30_days'];
                $config->category[$i]->days = $stats[$j]['days'];
                break;
              }
          }

  for ($i = 0; isset ($config->category[$i]); $i++)
    {
      $category = $config->category[$i];
      $category->tooltip = ''
                .($category->tooltip ? $category->tooltip : $category->title)
                .($category->items ? ', '.$category->items.' <!-- lang:items -->' : '')
                .($category->days ? ', '.$category->days.' <!-- lang:days -->' : '');
    }
  
  for ($i = 0; isset ($config->category[$i]); $i++)
    for ($j = 0; isset ($config->category[$i]->feed[$j]); $j++)
      {
        $feed = $config->category[$i]->feed[$j];

//        $config->category[$i]->link = array ();
//        $config->category[$i]->opts = array ();
        // old style config.xml: link[]
        for ($k = 0; isset ($feed->link[$k]); $k++)
          if (trim ($feed->link[$k]) != '')
            {
              $config->category[$i]->link[] = $feed->link[$k];
              $config->category[$i]->opts[] = $rsstool_opts.' '.$feed->opts;
              $config->category[$i]->client[] = $feed->client; // ? $feed->client : $rsstool_path; 
            }

        // TODO: use new style config.xml
        //   link_prefix, link_search[], link_suffix
        if (isset ($feed->link_prefix))
          for ($k = 0; isset ($feed->link_search[$k]); $k++)
            {
              $p = '';
//              if (isset ($feed->link_prefix))
                $p .= $feed->link_prefix;
//              if (isset ($feed->link_search[$k]))
                $p .= $feed->link_search[$k];
              if (isset ($feed->link_suffix))
                $p .= $feed->link_suffix;
              $config->category[$i]->link[] = $p;
              $config->category[$i]->opts[] = $rsstool_opts.' '.$feed->opts;
              $config->category[$i]->client[] = $feed->client; //  ? $feed->client : $rsstool_path;
            }
      }

  // DEBUG
//echo '<pre><tt>';
//print_r ($config);

  return $config;
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
        $p = $memcache->get (md5 ($rsscache_config_xml));

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

  $config = simplexml_load_file ($rsscache_config_xml);
  $config = config_xml_normalize ($config);

  // use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ($rsscache_config_xml), serialize ($config), 0, $memcache_expire);
  }

  return $config;
}


function
config_xml_by_category ($category)
{
  $config = config_xml ();

  for ($i = 0; isset ($config->category[$i]); $i++)
    if (trim ($config->category[$i]->name) == $category)
      return $config->category[$i];

  return NULL;
}


function
rsscache_download_thumbnails ($xml)
{
  global $wget_path;
  global $wget_opts;
  global $rsscache_domain;
  global $debug;
  
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
      $result = misc_exec_wget ($s, $p, $noclobber, $wget_path, $wget_opts);
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

  $xml = simplexml_load_file ($tmp);

  // DEBUG
//  print_r ($xml);
//  exit;
   
  unlink ($tmp);

  return $xml;
}


function
rsscache_download_feeds_by_category ($category_name)
{
  global $rsscache_sql_db;
  global $rsscache_user_agent;

  $category_name = trim ($category_name);
  $category = config_xml_by_category ($category_name);

  // TODO: single category using category_name   
  if ($category == NULL)
    return;

  for ($j = 0; isset ($category->link[$j]); $j++)
    {
      // rsstool options
      $opts = '';
      if (isset ($category->opts[$j]))
        $opts = $category->opts[$j];

//      $p = '';
//      $p .= 'category: '.$category_name."\n"
//           .'client: '.$category->client[$j]."\n"
//           .'opts: '.$opts."\n"
//           .'url: '.$category->link[$j]."\n"; 
//      echo $p;

      // get feed
      $xml = rsscache_feed_get ($category->client[$j], $opts, $category->link[$j]);
      // download thumbnails
      $xml = rsscache_download_thumbnails ($xml);
      // xml to sql
      $sql_queries_s = rsstool_write_ansisql ($xml, $category_name, $category->table_suffix, $rsscache_sql_db->conn);

      rsscache_sql_queries ($sql_queries_s);
    }
}


function
rsscache_title ($d_array = NULL)
{
  global $rsscache_title;
  $v = rsscache_get_request_value ('v');
  $c = rsscache_get_request_value ('c');
  $category = config_xml_by_category ($c);

  $a = array ();
  if (trim ($rsscache_title) != '')
    $a[] = $rsscache_title;

  if ($category)
    if (trim ($category->title) != '')
      $a[] = $category->title;

  if ($v && $d_array != NULL)
    $a[] = $d_array[0]['rsstool_title'];

  return implode (' - ', $a);
}


function
rsscache_duration ($d)
{
  if ($d['rsstool_media_duration'] > 0)
    return gmstrftime ($d['rsstool_media_duration'] > 3599 ? '%H:%M:%S' : '%M:%S', (int) $d['rsstool_media_duration']);
  return '';
}


function
rsscache_link_normalize ($link)
{
  // checks is file is on local server or on static server and returns correct link
  global $rsscache_root,
         $rsscache_link,
         $rsscache_link_static;
  $p = $link; // $d['rsstool_url']

  if (strncmp ($p, $rsscache_link, strlen ($rsscache_link)) || // extern link
      !$rsscache_link_static) // no static server
    return $link;

  $p = str_replace ($rsscache_link, $rsscache_root, $link); // file on local server?
  if (file_exists ($p))
    return $link;

  return str_replace ($rsscache_link, $rsscache_link_static, $link); // has to be on static server then
}


function
rsscache_link ($d)
{
  $p = '';
/*
  if ($d['rsscache_demux'] > 0)
    {
      $s = ''
          .'&seo='.str_replace (' ', '_', rsscache_keywords ($d))
;
      $p .= http_build_query2 (array ('v' => $d['rsstool_url_crc32'], 'f' => ''), true).$s;
    }
  else
*/
    {
      $s = rsscache_link_normalize (urldecode ($d['rsstool_url'])); // local, static or other server?
      $p .= $s; // .http_build_query2 (array (), false);
    }

  return $p;
}


function
rsscache_thumbnail ($d, $width = 120)
{
  // NOTE: right now only youtube thumbnails are supported
  global $rsscache_link_static,
         $rsscache_link,
         $rsscache_thumbnails_prefix;

//          $p .= '<a href="?'.http_build_query2 (array ('v' => $d['rsstool_url_crc32'],  
//                                                       'start' => ($start + 5),  
//                                                       'len' => $len), false).'">';  
//          $p .= rsscache_thumbnail ($d, $width, 1);
//          $p .= '</a>';  
  $link = rsscache_link ($d);

  $p = '';

//  if ($d['rsscache_demux'] == 1) // youtube
    {
//widget_button ($icon, $query, $label, $tooltip, $link_suffix = NULL, $flags = 0)
      $t = rsscache_duration ($d);
//      $p .= widget_button (rsscache_link_normalize ($rsscache_link.'/thumbnails/'.$rsscache_thumbnails_prefix.'rsscache/'.$d['rsstool_url_crc32'].'.jpg'),
//                           $link,
//                           NULL,
//                           $d['rsstool_title'].($t != '' ? ' ('.$t.')' : ''));
      $p .= widget_button ('/thumbnails/'.$rsscache_thumbnails_prefix.'rsscache/'.$d['rsstool_url_crc32'].'.jpg',
                           $link,
                           NULL,
                           $d['rsstool_title'].($t != '' ? ' ('.$t.')' : ''));
/*
      $p .= '<nobr>';
      $p .= '<a href="?'.$link.'" title="'.$d['rsstool_title'];
      $t = rsscache_duration ($d);
      if ($t != '')
        $p .= ' ('.$t.')';
      $p .= '">';

       $p .= '<img src="'
            .rsscache_link_normalize ($rsscache_link.'/thumbnails/'.$rsscache_thumbnails_prefix.'rsscache/'.$d['rsstool_url_crc32'].'.jpg')
            .'" width="'.$width.'" border="0" alt="'.$d['rsstool_title'].'"'
            .' onerror="this.parentNode.removeChild(this);"'
            .'>';

      $p .= '</a>';
      $p .= '</nobr>';
*/
    }

  return $p;
}


function
rsscache_event ($d)
{
  global $rsscache_time;

  $t[0] = $d['rsstool_event_start'];
  $t[1] = $d['rsstool_event_end'];

  $t[2] = $t[0] - $rsscache_time;
  date_default_timezone_set ($tz);
  $t[3] = (100 * $t[2]) / (7 * 86400); // percent (week)

  // DEBUG
echo '<pre><tt>';
print_r ($d);
print_r ($t);

  $p = '';
  $p .= '<br>Length: '.floor (($t[1] - $t[0]) / 60).' min';
  $p .= '<br>';
  if ($t[2] > 0)
    {
      $p .= ''
           .'<div style="float:left;font-size:16px;">'
           .'<b>LIVE</b> in '
//           .'Event in '
           .floor ($t[2] / 3600).'h '.floor ($t[2] % 60).'m&nbsp;&nbsp;'
           .'</div>'
;
      // progress
      $p .= '<div style="width:'.floor ($t[3]).'px;background-color:#f00;float:left;">&nbsp;</div>';
      $p .= '<div style="width:'.floor (100 - $t[3]).'px;background-color:#999;float:left;">&nbsp;</div>';
//      $p .= '<div style="float:left;font-size:16px;">'
//           .'&nbsp;(7 days)'
//           .'</div>'
//;
      $p .= '<div style="clear:both;"></div>';
    }
  else
    $p .= ''
         .'<div style="float:left;font-size:16px;">'
         .'Event was '
         .(floor ($t[2] / 3600) * -1).'h '.(floor ($t[2] % 60) * -1).'m&nbsp;ago&nbsp;&nbsp;'
         .'</div>'
;

  return $p;
}


}


?>