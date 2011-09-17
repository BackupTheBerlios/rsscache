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
  for ($i = 0; isset ($a['item'][$i]); $i++)
    if (isset ($a['item'][$i]['category']))
      for ($j = 0; isset ($stats[$j]); $j++)
        if ($stats[$j]['stats_category'] == $a['item'][$i]['category'])
          {
            $a['item'][$i] = array_merge ($a['item'][$i], misc_prefixate_array ($stats[$j], 'rsscache:'));
            break;
          }

  for ($j = 0; isset ($stats[$j]); $j++)
    {
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
//rsscache_download_videos ($channel, $item)
rsscache_download_videos ($item)
{
  $debug = 0;
      // DEBUG
//      echo '<pre><tt>';
//      print_r ($item);
//      exit;
//  for ($i = 0; isset ($item[$i]); $i++)
    {
//      $id = youtube_get_videoid ($item[$i]['link']);
      $id = youtube_get_videoid ($item['link']);
      $b = youtube_get_download_urls ($id, 0, $debug);
   
      // DEBUG
//      print_r ($b);
//      exit;
      for ($j = 0; isset ($b[$j]); $j++);

//      $item[$i]['rsscache:download'] = $b[max (0, $j - 2)]; // lowest quality
      return $b[max (0, $j - 2)]; // lowest quality
    }

//  return array ('channel' => $channel, 'item' => $item);
  return NULL;
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
rsscache_default_channel_description ($use_mrss = 0, $use_rsscache = 0)
{
  $p = 'RSScache uses RSS 2.0 specification with new namespaces (rsscache and cms) for configuration'."\n"
      .''."\n"
      .'format:'."\n"
      .'rss                       even config files are made of RSS :)'."\n"
      .'  channel[]               site'."\n"
      .'    title                 title'."\n"
      .'    link                  site link'."\n"
      .'    description'."\n"
      .'    image                 optional'."\n"
      .'      url                 image url'."\n"
      .'      link                optional, image link'."\n"
      .'TODO:      width               optional, image width'."\n"
      .'TODO:      height              optional, image height'."\n"
      .'TODO:    rsscache:filter  optional'."\n"
      .'    item[]                    feed downloads'."\n"
      .'      title                 category title'."\n"
      .'      link                  optional, url of button or select'."\n"
      .'                              &q=SEARCH       search'."\n"
      .'                              *** functions ***'."\n"
      .'                              &f=all          show all categories (sorted by time of RSS feed download)'."\n"
      .'                              &f=new          show all categories (sorted by time of RSS item)'."\n"
      .'                              &f=0_5min       show media with <5 minutes duration'."\n"
      .'                              &f=5_10min'."\n"
      .'                              &f=10_30min'."\n"
      .'                              &f=30_60min'."\n"
      .'                              &f=60min'."\n"
      .'                              &f=related'."\n"
      .'                              &f=stats'."\n"
      .'                              &f=author'."\n"
      .'                              &f=sitemap'."\n"
      .'                              &f=robots'."\n"
      .'                              &f=cache'."\n"
      .'                              &f=config'."\n"
      .'                              &f=4_3          4:3 ratio for videos (CMS only)'."\n"
      .'                              &f=16_9         16:9 ratio for videos (CMS only)'."\n"
      .'TODO:                              &f=score        sort by score/votes/popularity'."\n"
      .'                              *** output ***'."\n"
      .'                              &output=rss     output page as RSS feed'."\n"
      .'                              &output=mirror  output page as static HTML'."\n"
      .'                              &output=wall    show search results as wall'."\n"
      .'                              &output=cloud   same as wall'."\n"
      .'                              &output=stats   show RSS feed download stats'."\n"
      .'                              &output=1col    show videos in 1 column'."\n"
      .'                              &output=2cols   show videos in 2 columns'."\n"
      .'      description'."\n"
      .'      category              category name'."\n"
      .'      enclosure             optional, category logo/image'."\n"
      .'        url                 image url'."\n"
      .'        length              '."\n"
      .'        type                '."\n"
      .'TODO:      rsscache:filter         optional, boolean full-text search query for SQL query using IN BOOLEAN MODE modifier'."\n"
      .'      rsscache:feed[]'."\n"
      .'        rsscache:link                   link of feed (RSS, etc.)'."\n"
      .'                                http://gdata.youtube.com/feeds/api/videos?author=USERNAME&vq=SEARCH&max-results=50'."\n"
      .'                                http://gdata.youtube.com/feeds/api/videos?vq=SEARCH&max-results=50'."\n"
      .'        NOTE: use link_prefix, link_suffix and link_search when getting more than one RSS feed from the same place'."\n"
      .'        rsscache:link_prefix    same as link'."\n"
      .'        rsscache:link_search[]'."\n"
      .'        rsscache:link_suffix'."\n"
      .'        rsscache:opts    '."\n"
      .'TODO:      rsscache:filter       optional, boolean full-text search query for SQL query using IN BOOLEAN MODE modifier'."\n"
      .'      rsscache:table_suffix  '."\n"
      .'TODO:      rsscache:votable          if items of this category can be voted for'."\n"
      .'                                       0 = not (default)'."\n"
      .'                                       1 = by everyone'."\n"
      .'TODO:      rsscache:reportable       if items can be reported to the admins'."\n"
      .'                                       0 = not (default)'."\n"
      .'                                       1 = by everyone'."\n"
      .'TODO:      rsscache:movable          if items can be moved to another category'."\n"
      .'                                       0 = not (default)'."\n"
      .'                                       1 = by the admin only'."\n"
      .'                                       2 = by everyone'."\n"
      ."\n"
      .'CMS options, widget.php/widget_cms():'."\n"
      .'    cms:separate     optional, adds a line-feed or separator before the next category'."\n"
      .'                            0 == no separator (default)'."\n"
      .'                            1 == line-feed'."\n"
      .'                            2 == horizontal line (hr tag)'."\n"
      .'    cms:button_only  optional, show only button'."\n"
      .'    cms:status       optional, adds a small status note'."\n"
      .'                            0 == nothing (default)'."\n"
      .'                            1 == "New!"'."\n"
      .'                            2 == "Soon!"'."\n"
      .'                            3 == "Preview!"'."\n"
      .'                            4 == "Update!"'."\n"
      .'    cms:select       add to select menu'."\n"
      .'    cms:local        optional, local file to embed'."\n"
      .'    cms:iframe       optional, url to embed'."\n"
      .'    cms:proxy        optional, url to embed (proxy-style)'."\n"
      ."\n"
      .'optional:'."\n"
      .'rss'."\n"
      .'  channel[]'."\n"
      .'    docs'."\n"
      .'    item[]'."\n"
      .'      pubDate'."\n"
      .'      author'."\n"
      .'      media:duration'."\n"
      .'      media:keywords'."\n"
      .'      media:thumbnail'."\n"
      .'      rsscache:dl_date'."\n"
      .'      rsscache:pubDate      same as pubDate but as integer'."\n"
      .'      rsscache:related_id'."\n"
      .'      rsscache:event_start'."\n"
      .'      rsscache:event_end'."\n"
      .'      rsscache:url_crc32'."\n"
      .'      rsscache:stats_category'."\n"
      .'      rsscache:stats_items'."\n"
      .'      rsscache:stats_days'."\n"
      .'      rsscache:stats_items_today'."\n"
      .'      rsscache:stats_items_7_days'."\n"
      .'      rsscache:stats_items_30_days'."\n"
      .'      rsscache:download     admin, only'."\n"
      .'      cms:demux'."\n"
      ."\n"
      .'*** queries ***'."\n"
      .'&q=SEARCH     SEARCH query'."\n"
      .'&start=N      start from result N'."\n"
      .'&num=N        show N results'."\n"
      .'&c=NAME       category (leave empty for all categories)'."\n"
      .'&item=URL_CRC32   show single item'."\n"
      .'&f=FUNC       execute FUNCtion'."\n"
      .'&output=FORMAT   output in "rss", "mediawiki", "json", "sitemap", "pls" (admin) or "html" (default: rss)'."\n"
//      .'&prefix=SUBDOMAIN   prefix or SUBDOMAIN (leave empty for current subdomain)'."\n"
      ."\n"           
      .'*** functions ***'."\n"
      .'&f=author     find user/author/channel (requires &q=SEARCH)'."\n"
      .'&<a href="?f=0_5min&output=html">f=0_5min</a>     media with duration 0-5 minutes'."\n"
      .'&<a href="?f=5_10min&output=html">f=5_10min</a>    media with duration 5-10 minutes'."\n"
      .'&<a href="?f=10_30min&output=html">f=10_30min</a>   media with duration 10-30 minutes'."\n"
      .'&<a href="?f=30_60min&output=html">f=30_60min</a>   media with duration 30-60 minutes'."\n"
      .'&<a href="?f=60min&output=html">f=60_min</a>     media with duration 60+ minutes'."\n"
      .'&<a href="?f=new&output=html">f=new</a>        show only newly created items (default: download time)'."\n"
      .'&f=related    find related items (requires &q=RELATED_ID)'."\n"
      .'&<a href="?f=stats&output=html">f=stats</a>      statistics'."\n"
//      .'&f=error404      '."\n"
//      .'&f=error304      '."\n"
//      .'&f=error300      '."\n"
      ."\n"
      .'*** admin functions ***'."\n"
      .'&<a href="?f=robots">f=robots</a>    robots.txt'."\n"
      ."\n"
      .'requires access to <a href="admin.php?output=html">admin.php</a>:'."\n"
      .'&<a href="?f=cache&output=html">f=cache</a>      cache (new) items into database (requires &c=CATEGORY)'."\n"
      .'&<a href="?f=config&output=html">f=config</a>    indent and dump config.xml'."\n"
      .'&<a href="?output=pls">output=pls</a>    generate playlist'."\n"
      ."\n"
      .'*** install ***'."\n"
      .'see apache2/sites-enabled/rsscache'."\n"
;
  return str_replace (array ('&', "\n", ' '), array ('&amp;', '<br>', '&nbsp;'), $p);
}


function
rsscache_default_channel ()
{
  global $rsscache_link;
  global $rsscache_time;
  global $rsscache_logo;
  global $rsscache_results;

  $config = config_xml ();

  $channel = array ('title' => rsscache_title (),
                    'link' => $rsscache_link,
                    'description' => rsscache_default_channel_description (1, 1),
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