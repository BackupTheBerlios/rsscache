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
require_once ('default.php');
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('misc/widget.php');
require_once ('misc/wikipedia.php');
//require_once ('misc/rss.php');
require_once ('misc/sql.php');
require_once ('misc/youtube.php');
require_once ('rsscache_sql.php');
require_once ('rsscache_lang.php');


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
    $p = $rsstool_path.' '.$rsstool_opts.' -u "'.$rsscache_user_agent.';" '.$opts.' --xml "'.$url.'" -o '.$tmp;

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
      $p .= widget_button (rsscache_link_normalize ($rsscache_link.'/thumbnails/'.$rsscache_thumbnails_prefix.'rsscache/'.$d['rsstool_url_crc32'].'.jpg'),
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
rsscache_f_wiki ()
{
  $c = rsscache_get_request_value ('c');        
  $config = config_xml_by_category ($c);      
//  return widget_wikipedia ($config->wiki);
  return wikipedia_get_html ($config->wiki);
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
            [rsscache_demux] => 12
        )
*/
function
//rsscache_stripdir ($url, $start, $num)
rsscache_stripdir ($url)
{
  global $rsscache_tor_enabled;

  $v = array ();

  if (widget_media_demux ($url) != 0)
    {
      $v[] = $url;
      return $v;
    }

  if ($rsscache_tor_enabled)
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


function
config_xml_normalize ($config)
{
  global $rsscache_use_database;

  if ($rsscache_use_database == 1)
    {
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
    }

  for ($i = 0; isset ($config->category[$i]); $i++)
    {
      $category = $config->category[$i];
      $category->tooltip = ''
                .($category->tooltip ? $category->tooltip : $category->title)
                .($category->items ? ', '.$category->items.' <!-- lang:items -->' : '')
                .($category->days ? ', '.$category->days.' <!-- lang:days -->' : '');
    }
  // DEBUG
//echo '<pre><tt>';
//print_r ($config);

  return $config;
}


function
config_xml ($memcache_expire = 0)
{
  global $rsscache_use_database;
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

            if ($rsscache_use_database)
              rsscache_sql_close ();

            exit;
          }
      }
    else
      {
        echo 'ERROR: could not connect to memcached';

        if ($rsscache_use_database)
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
    if ($config->category[$i]->name == $category)
      return $config->category[$i];

  return NULL;
}


// HACK
function
rsscache_normalize ($category)
{
  $p = strtolower ($category);

  if ($p == 'baseq3')
    $category = 'quake3';
  else if ($p == 'baseqz')
    $category = 'quakelive';

  return $category;
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


function
generate_rss2 ($title, $link, $desc, $item_title_array, $item_link_array, $item_desc_array,
              $item_media_duration_array = NULL,
              $item_author_array = NULL)
{
  $version = 2; // RSS2.0

  $p = '';
  $p .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";

  if ($version == 1)
    $p .= '<rdf:RDF xmlns="http://purl.org/rss/1.0/">'."\n";
  else
    $p .= '<rss version="2.0">'."\n";

  $p .= '  <channel>'."\n"
       .'    <title>'
       .htmlspecialchars ($title, ENT_QUOTES)
       .'</title>'."\n"
       .'    <link>'
       .htmlspecialchars ($link, ENT_QUOTES)
       .'</link>'."\n"
       .'    <description>'
       .htmlspecialchars ($desc, ENT_QUOTES)
       .'</description>'."\n"
//     .'    <dc:date>%ld</dc:date>'
;

  if ($version == 1)
    {
      $p .= '<items>'."\n"
           .'<rdf:Seq>'."\n";

      for ($i = 0; isset ($item_link_array[$i]); $i++)
        $p .= "\n".'        <rdf:li rdf:resource="'
             .htmlspecialchars ($item_link_array[$i], ENT_QUOTES)
             .'"/>';

      $p .= '</rdf:Seq>'."\n"
           .'</items>'."\n"
           .'</channel>'."\n";
    }

  for ($i = 0; isset ($item_link_array[$i]); $i++)
    {
      if ($version == 1)
        $p .= '<item rdf:about="'
             .$item_link_array[$i]
             .'">'."\n";
      else
        $p .= '    <item>'."\n";

      $p .= '      <title>'
           .htmlspecialchars ($item_title_array[$i], ENT_QUOTES)
           .'</title>'."\n"
           .'      <link>'
           .htmlspecialchars ($item_link_array[$i], ENT_QUOTES)
           .'</link>'."\n"
           .'      <description>'
           .htmlspecialchars ($item_desc_array[$i], ENT_QUOTES)
           .'</description>'."\n"
           .'      <pubDate>'
           .strftime ("%Y%m%d %H:%M:%S", time ())
//           .time ()
           .'</pubDate>'."\n";

      if ($item_media_duration_array)
        if (isset ($item_media_duration_array[$i]))
          $p .= '      <media:duration>'.$item_media_duration_array[$i].'</media:duration>'."\n";

      if ($item_author_array)
        if (isset ($item_author_array[$i]))
          $p .= '      <author>'.$item_author_array[$i].'</author>'."\n";

      $p .= '    </item>'."\n";
    }

  if ($version == 2)
    $p .= '  </channel>'."\n";

  $p .= '</rss>'."\n";

  return $p;
}


function
rsscache_stats_rss ()
{
  global $rsscache_link;
  global $rsscache_translate;
  global $rsscache_language;
  $items = 0;
  $items_today = 0;
  $items_7_days = 0;
  $category->items_30_days = 0;

//    header ('Content-type: text/xml');
    header ('Content-type: application/xml');
//    header ('Content-type: text/xml-external-parsed-entity');
//    header ('Content-type: application/xml-external-parsed-entity');
//    header ('Content-type: application/xml-dtd');

  $config = config_xml ();

  $rss_title_array = array ();
  $rss_link_array = array ();
  $rss_desc_array = array ();

  $s = '<img src="images/new.png" border="0">';

  for ($i = 0; isset ($config->category[$i]); $i++)
    if ($config->category[$i]->name != '' &&
        (isset ($config->category[$i]->feed[0]->link[0]) || isset ($config->category[$i]->feed[0]->link_prefix)))
      {
        $category = $config->category[$i];
        $p = ''
            .'<img src="'.$config->category[$i]->logo.'" border="0"><br>'
            .($category->items * 1).' <!-- lang:items --><br>'
            .($category->items_today * 1).' <!-- lang:items --> <!-- lang:today -->'
                                     .(($category->items_today * 1) > 0 ? ' '.$s : '').'<br>'
            .($category->items_7_days * 1).' <!-- lang:items --> <!-- lang:last --> 7 <!-- lang:days --><br>'
            .($category->items_30_days * 1).' <!-- lang:items --> <!-- lang:last --> 30 <!-- lang:days --><br>'
            .($category->days * 1).' <!-- lang:days --> <!-- lang:since creation of category -->'
;
        $p = misc_template ($p, $rsscache_translate[$rsscache_language ? $rsscache_language : 'default']);

        $rss_title_array[] = $category->title;
        $rss_link_array[] = 'http://'.$_SERVER['SERVER_NAME'].'/?'.$category->query;
        $rss_desc_array[] = $p;

        $items += ($category->items * 1);
        $items_today += ($category->items_today * 1);
        $items_7_days += ($category->items_7_days * 1);
        $items_30_days += ($category->items_30_days * 1);
      }

  $rss_title_array[] = 'ALL';
  $rss_link_array[] = 'http://'.$_SERVER['SERVER_NAME'];

        $p = ''
//            .'<!-- lang:category -->: '.$config->category[$i]->name.'<br>'
            .($items * 1).' <!-- lang:items --><br>'
            .($items_today * 1).' <!-- lang:items --> <!-- lang:today -->'
                                     .((items_today * 1) > 0 ? ' '.$s : '').'<br>'
            .($items_7_days * 1).' <!-- lang:items --> <!-- lang:last --> 7 <!-- lang:days --><br>'
            .($items_30_days * 1).' <!-- lang:items --> <!-- lang:last --> 30 <!-- lang:days --><br>'
;
  $p = misc_template ($p, $rsscache_translate[$rsscache_language ? $rsscache_language : 'default']);
  $rss_desc_array[] = $p;

  // DEBUG
//  print_r ($rss_title_array);
//  print_r ($rss_link_array);
//  print_r ($rss_desc_array);

  return generate_rss2 (rsscache_title (),
                       $rsscache_link,
//                       'Statistics',
                     'rsscache urls have a similar syntax like google urls<br>'
.'<br>'
.'<br>'
.'q=SEARCH  SEARCH query<br>'
.'start=N   start from result N<br>'
.'num=N     show N results<br>'
.'c=NAME    category (leave empty for all categories)<br>'
.'<br>'
.'<br>'
.'*** functions ***<br>'
.'f=0_5min      videos with duration 0-5 minutes<br>'
.'f=5_10min     videos with duration 5-10 minutes<br>'
.'f=10_min      videos with duration 10+ minutes<br>'
.'f=stats       statistics<br>'
.'f=new         show only new items<br>'
.'f=related     find related items (requires &q=SEARCH)<br>'
.'<br>'   
.'<br>'
.'*** install ***<br>'
.'see apache2/sites-enabled/rsscache<br>'
.'',
                       $rss_title_array, $rss_link_array, $rss_desc_array);
}


function
rsscache_rss ($d_array)
{
  global $rsscache_link;

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
      if (substr (rsscache_link ($d_array[$i]), 0, 7) == 'http://')
        $rss_link_array[$i] = rsscache_link ($d_array[$i]);
      else
        $rss_link_array[$i] = $rsscache_link.'?'.rsscache_link ($d_array[$i]);

      $rss_desc_array[$i] = ''
                           .rsscache_thumbnail ($d_array[$i], 120, 1)
                           .'<br>'
                           .$d_array[$i]['rsstool_desc']
;
    }

  // DEBUG
//  print_r ($rss_title_array);
//  print_r ($rss_link_array);
//  print_r ($rss_desc_array);

  return generate_rss2 (rsscache_title (),
                     $rsscache_link,
                     'rsscache urls have a similar syntax like google urls<br>'
.'<br>'
.'<br>'
.'q=SEARCH  SEARCH query<br>'
.'start=N   start from result N<br>'
.'num=N     show N results<br>'
.'c=NAME    category (leave empty for all categories)<br>'
.'<br>'
.'<br>'
.'*** functions ***<br>'
.'f=0_5min      videos with duration 0-5 minutes<br>'
.'f=5_10min     videos with duration 5-10 minutes<br>'
.'f=10_min      videos with duration 10+ minutes<br>'
.'f=stats       statistics<br>'
.'f=new         show only new items<br>'
.'f=related     find related items (requires &q=SEARCH)<br>'
.'<br>'
.'<br>'
.'*** install ***<br>'
.'see apache2/sites-enabled/rsscache<br>'
,
                     $rss_title_array, $rss_link_array, $rss_desc_array);
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


}


?>