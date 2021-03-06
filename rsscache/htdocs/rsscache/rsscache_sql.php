<?php
/*
rsscache.php - rsscache engine SQL functions

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
if (!defined ('RSSCACHE_SQL_PHP'))
{
define ('RSSCACHE_SQL_PHP', 1);
//require_once ('config.php');
//require_once ('misc/misc.php');
//require_once ('misc/sql.php');
//require_once ('misc/widget_media.php');


$rsscache_sql_db = NULL;


function
rsscache_sql_open ()
{
  // move item to different category
  global $rsscache_sql_db;
  global $rsscache_dbhost,
         $rsscache_dbuser,
         $rsscache_dbpass,
         $rsscache_dbname;

  $rsscache_sql_db = new misc_sql;
  $rsscache_sql_db->sql_open ($rsscache_dbhost,
                 $rsscache_dbuser,
                 $rsscache_dbpass,
                 $rsscache_dbname);
}


function
rsscache_sql_close ()
{
  global $rsscache_sql_db;

  $rsscache_sql_db->sql_close ();
}


function
rsscache_sql_query ($sql_query_s)
{
  global $rsscache_sql_db;
  $debug = 0;

  // DEBUG
  if ($debug == 1)
    {
      echo $sql_query_s.'<br>';
      exit;
    }

  $rsscache_sql_db->sql_write ($sql_query_s, 1, $debug);

  $debug = 0;
  $d = $rsscache_sql_db->sql_read (1, $debug);

  // DEBUG
//  echo '<tt><pre>';
//  print_r ($d);

  return $d;
}


function
rsscache_sql_queries ($sql_queries_s)
{
  $sql_array = explode ("\n", $sql_queries_s);

  // DEBUG
//  print_r ($sql_array);

  for ($i = 0; isset ($sql_array[$i]); $i++)
    {
      $sql_query_s = $sql_array[$i];

      if (substr (trim ($sql_query_s), 0, 2) == '--')
        continue;

      rsscache_sql_query ($sql_query_s);
    }
}


function
rsscache_sql_stats_func ($c, $t)
{
  $a = array ();
  $rsstool_table = rsscache_tablename_by_category ('rsstool', $c);  

  if ($c)
    $a[] = 'tv2_moved = \''.$c.'\'';
  if ($t > 0)
    $a[] = 'rsstool_dl_date > '.$t;
  $sql_query_s = 'SELECT SQL_CACHE tv2_moved, MIN(rsstool_dl_date), COUNT(*) AS rsscache_rows'
        .' FROM '
        .$rsstool_table
        .(count ($a) ? ' WHERE ( '.implode (' AND ', $a).' )' : '')
        .' GROUP BY tv2_moved'
//        .' ORDER BY rsscache_rows DESC'
;
  // DEBUG
//echo $sql_query_s."\n";
  return $sql_query_s;
}


function
rsscache_sql_stats ($c = '')
{
  global $rsscache_sql_db;
  global $rsscache_time;
  $db = $rsscache_sql_db;

  $debug = 0;

  $stats = array ();

  // total items and days since creation
  $sql_query_s = rsscache_sql_stats_func ($c, 0);
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    $stats[] = array ('stats_category' => $r[$i][0],
                  'stats_items' => (int) $r[$i][2],
                  'stats_days' => (int) (($rsscache_time - $r[$i][1]) / 86400));

  for ($j = 0; isset ($stats[$j]); $j++)
    {
      $stats[$j]['stats_items_today'] = 0;
      $stats[$j]['stats_items_7_days'] = 0;
      $stats[$j]['stats_items_30_days'] = 0;
    }

  // downloaded items today
  $sql_query_s = rsscache_sql_stats_func ($c, mktime (0, 0, 0));
//  $sql_query_s = rsscache_sql_stats_func ($c, mktime (0, 0, 0, date ('n'), date ('j')));
//  $sql_query_s = rsscache_sql_stats_func ($c, $rsscache_time);
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    for ($j = 0; isset ($stats[$j]); $j++)
      if ($r[$i]['tv2_moved'] == $stats[$j]['stats_category'])
        {
          $stats[$j]['stats_items_today'] = (int) $r[$i][2];
          break;
        }

  // downloaded items last 7 days   
  $sql_query_s = rsscache_sql_stats_func ($c, mktime (0, 0, 0, date ('n'), date ('j') - 7));
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    for ($j = 0; isset ($stats[$j]); $j++)
      if ($r[$i]['tv2_moved'] == $stats[$j]['stats_category'])
        {
          $stats[$j]['stats_items_7_days'] = (int) $r[$i][2];
          break;
        }

  // downloaded items last 30 days
  $sql_query_s = rsscache_sql_stats_func ($c, mktime (0, 0, 0, date ('n'), date ('j') - 30));
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    for ($j = 0; isset ($stats[$j]); $j++)
      if ($r[$i]['tv2_moved'] == $stats[$j]['stats_category'])
        {
          $stats[$j]['stats_items_30_days'] = (int) $r[$i][2];
          break;
        }

  // DEBUG
//echo '<pre><tt>';
//print_r ($stats);
//exit;

  return $stats;
}


function
rsscache_sql2array ($d_array)
{
  global $rsscache_link;
  global $rsscache_time;
  global $rsscache_logo;
  global $rsscache_results;
  global $rsscache_admin;
  global $output;
  global $rsscache_link_static;

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($d_array);
//exit;

  $f = rsscache_get_request_value ('f'); // function
//  $output = rsscache_get_request_value ('output');

  $item = array ();
//  $config = config_xml ();

  for ($i = 0; isset ($d_array[$i]); $i++)
    {
      $category_xml = config_xml_by_category ($d_array[$i]['tv2_moved']);
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($category_xml);
//exit;

      $a = array ('title' => $d_array[$i]['rsstool_title'],
//                       'link' => rsscache_link ($d_array[$i]),
                       'link' => $d_array[$i]['rsstool_url'],
                       'description' => $d_array[$i]['rsstool_desc'],
                       'pubDate' => $d_array[$i]['rsstool_date'],
                       'enclosure' => $category_xml['enclosure'],
                       'category' => $d_array[$i]['tv2_moved'],
                       'author' => $d_array[$i]['rsstool_user'],

                       'media:duration' => ($d_array[$i]['rsstool_media_duration'] * 1),
                       'media:keywords' => str_replace (' ', ', ', $d_array[$i]['rsstool_keywords']),
                       'media:thumbnail' => misc_cleanup_slashes ($rsscache_link_static
                                           .'/thumbnails/rsscache/'
                                           .$d_array[$i]['rsstool_url_crc32']
                                           .'.jpg'),

                       'rsscache:dl_date' => ($d_array[$i]['rsstool_dl_date'] * 1),
                       'rsscache:related_id' => ($d_array[$i]['rsstool_related_id'] * 1),
                       'rsscache:event_start' => ($d_array[$i]['rsstool_event_start'] * 1),
                       'rsscache:event_end' => ($d_array[$i]['rsstool_event_end'] * 1),
                       'rsscache:url_crc32' => ($d_array[$i]['rsstool_url_crc32'] * 1),
                       'rsscache:table_suffix' => isset ($category_xml['rsscache:table_suffix']) ? $category_xml['rsscache:table_suffix'] : NULL,
                       'rsscache:category_title' => $category_xml['title'],

                       'cms:separate' => ($category_xml['cms:separate'] * 1),
                       'cms:button_only' => ($category_xml['cms:button_only'] * 1),
                       'cms:status' => isset ($category_xml['cms:status']) ? ($category_xml['cms:status'] * 1) : 0,
                       'cms:select' => ($category_xml['cms:select'] * 1),
                       'cms:local' => isset ($category_xml['cms:local']) ? $category_xml['cms:local'] : NULL,
                       'cms:iframe' => isset ($category_xml['cms:iframe']) ? $category_xml['cms:iframe'] : NULL,
                       'cms:proxy' => isset ($category_xml['cms:proxy']) ? $category_xml['cms:proxy'] : NULL,
);

if (isset ($category_xml['rsscache:stats_category']))
  {
    $a['rsscache:stats_category'] = $category_xml['rsscache:stats_category'];
    $a['rsscache:stats_items'] = ($category_xml['rsscache:stats_items'] * 1);
    $a['rsscache:stats_days'] = ($category_xml['rsscache:stats_days'] * 1);
    $a['rsscache:stats_items_today'] = ($category_xml['rsscache:stats_items_today'] * 1);
    $a['rsscache:stats_items_7_days'] = ($category_xml['rsscache:stats_items_7_days'] * 1);
    $a['rsscache:stats_items_30_days'] = ($category_xml['rsscache:stats_items_30_days'] * 1);
  }

//widget_media_demux ($media_url)
//widget_media ($media_url, $width = NULL, $height = NULL, $ratio = NULL, $autoplay = 0, $hq = 0, $loop = 0, $blackbg = 0)
      $demux = widget_media_demux ($d_array[$i]['rsstool_url']);
      if ($demux != 0)
        {
          $a['media:embed'] = widget_media ($d_array[$i]['rsstool_url'], -1, -1);
          $a['cms:demux'] = $demux;
        }

      for ($j = 0; isset ($category_xml['rsscache:feed_'.$j.'_link']); $j++)
        {
          $b = array ();
//          if (isset ($category_xml['rsscache:feed_'.$j.'_client']))
//            $b['rsscache:feed_'.$j.'_client'] = $category_xml['rsscache:feed_'.$j.'_client'];
          $b['rsscache:feed_'.$j.'_exec'] = $category_xml['rsscache:feed_'.$j.'_exec'];
          $b['rsscache:feed_'.$j.'_link'] = $category_xml['rsscache:feed_'.$j.'_link'];
          $a = array_merge ($b, $a);
        }

      $item[] = $a;
    }

  return $item;
}


function
rsscache_sql_normalize ($d)
{
  global $rsscache_root,
         $rsscache_link,
         $rsscache_related_search;
  $debug = 0;

  for ($i = 0; isset ($d[$i]); $i++)
    {
      // trim and lower-case categories
//      $d[$i]['tv2_category'] = strtolower (trim ($d[$i]['tv2_category']));
      $d[$i]['tv2_moved'] = strtolower (trim ($d[$i]['tv2_moved']));
//      $d[$i]['rsstool_related_id'] = misc_related_string_id ($d[$i]['rsstool_title']);
      $d[$i]['rsstool_related_id'] = sprintf ("%u", $d[$i]['rsstool_related_id']);
    }

  return array ('channel' => rsscache_default_channel (), 'item' => rsscache_sql2array ($d));
}


function
rsscache_sql_query2boolean ($q, $c = NULL)
{
  $debug = 0;

  // filter
  $filter = '';
  if ($c)
    {
      $category_xml = config_xml_by_category ($c);

      if ($category_xml)
        $q = trim ($q.' '.$category_xml['rsscache:filter']);
    }
  // DEBUG
  if ($debug == 1)
    echo 'search: '.$q.'<br>';

  $a = explode (' ', $q);
  $b = array ('any' => '',
              'require' => '',
              'exclude' => '');

  for ($i = 0; isset ($a[$i]); $i++)
    {
      $s = trim ($a[$i]);

      if ($s == '')
        continue;

      if ($s[0] == '+')
        $b['require'] .= ' '.substr ($s, 1);
      else if ($s[0] == '-')
        $b['exclude'] .= ' '.substr ($s, 1);
      else
        $b['any'] .= ' '.$s;
    }
/*
  $b['any'] = trim ($b['any']);
  if ($b['any'] == '')
    $b['any'] = NULL;

  $b['require'] = trim ($b['require']);
  if ($b['require'] == '')
    $b['require'] = NULL;

  $b['exclude'] = trim ($b['exclude']);
  if ($b['exclude'] == '')
    $b['exclude'] = NULL;
*/
  return $b;
}


function
rsscache_sql_keyword_func ($any = NULL, $require = NULL, $exclude = NULL, $table_suffix = NULL)
{
  $debug = 0;
  // DEBUG
//  if ($debug == 1)
//    echo 'any: '.$any.'<br>require: '.$require.'<br>exclude: '.$exclude.'<br>';

  $rsstool_table = rsscache_tablename ('rsstool', $table_suffix);  
  $keyword_table = rsscache_tablename ('keyword', $table_suffix); 

  // HACK: merge any and require since result is sorted by number of matches
  $q = $any.' '.$require;

  $p = '';
  $p .= ' ('
       .' SELECT'
//       .' DISTINCT'
       .' SQL_CACHE rsstool_url_crc32, COUNT(*) AS rsscache_rows'
       .' FROM '.$keyword_table
       .' WHERE '.$keyword_table.'.rsstool_keyword_crc16'
//       .' WHERE '.$keyword_table.'.rsstool_keyword_crc24'
//       .' WHERE '.$keyword_table.'.rsstool_keyword_crc32'
       .' IN ( '
;

  $s = misc_get_keywords ($q, 0); // isalnum()
  $a = explode (' ', $s);
  $a = misc_array_unique_merge ($a);   
  $func = 'misc_crc16'; // 0xffff keywords
//  $func = 'misc_crc24'; // 0xffffff keywords
//  $func = 'crc32'; // 0xffffffff keywords
  $a = array_map ($func, $a);
  $p .= implode (', ', $a);

  $p .= ' )'
       .' GROUP BY rsstool_url_crc32'
//       .' HAVING rsscache_rows = '.count ($a)
       .' ORDER BY rsscache_rows DESC'
//       .' LIMIT 1024'
       .' LIMIT 256'
//       .' LIMIT 64'
       .' )'
       .' AS temp'
       .' JOIN '.$rsstool_table.' ON '.$rsstool_table.'.rsstool_url_crc32 = temp.rsstool_url_crc32';

  // DEBUG
//  echo $p;
//exit;
  return $p;
}


function
rsscache_sql ($c, $q, $f, $v, $start, $num, $v_segments = NULL)
{
  /*
    $c == category
    $q == query
    $f == function
    $v == video (rsstool_url_crc32)
    LIMIT $start, $num
    $table_suffix is optional and set in config.xml
  */
  global $rsscache_sql_db,
         $rsscache_isnew,
         $rsscache_root,
         $rsscache_enable_search,
         $rsscache_related_search,
         $rsscache_use_dl_date,
         $rsscache_max_results,
         $rsscache_time,
         $rsscache_item_ttl;
  global $rsscache_debug_sql;

  $debug = $rsscache_debug_sql;
//  $debug = 1;

  $category0_xml = config_xml ();
// DEBUG
//echo '<pre><tt>';
//print_r ($category0_xml);
//exit;
$c = 'c64';
  $category_xml = config_xml_by_category ($c);
  $c = $category_xml['category']; // safety
// DEBUG
//echo '<pre><tt>';
//echo $c;
//print_r ($category_xml);
//exit;

  if ($f == 'stats')
    return rsscache_sql_stats ($rsscache_sql_db, $c);

  if ($f == 'extern')
    return rsscache_sql_extern ($c, $q, $f, $v, $start, $num);

  $rsstool_table = rsscache_tablename_by_category ('rsstool', $c);

  $sql_query_s = '';
  $sql_query_s .= ''
//                 .'EXPLAIN ';
                 .'SELECT'
//                 .' SQL_CACHE'
                 .' rsstool_url,'
                 .' '.$rsstool_table.'.rsstool_url_crc32,'
                 .' rsstool_title,'
                 .' rsstool_desc,'
                 .' rsstool_dl_date,'
                 .' rsstool_date,'
//                 .' tv2_category,'
                 .' tv2_moved,'
                 .' rsstool_event_start,'
                 .' rsstool_event_end,'
                 .' rsstool_media_duration,'
                 .' rsstool_keywords,'
                 .' rsstool_user,'
                 .' rsstool_related_id'
//                 .' tv2_votes,'
//                 .' tv2_score'
;

  // direct
  if ($v)
    {
      $sql_query_s .= ' FROM '.$rsstool_table;
      $sql_query_s .= ' WHERE ( rsstool_url_crc32 = '.$v.' )'
                     .' LIMIT 1'
;
      $d = rsscache_sql_query ($sql_query_s);

      $d = rsscache_sql_normalize ($d);

      return $d;
    }

  // related search
  if ($rsscache_related_search && $f == 'related')
    {
      $sql_query_s .= ' FROM '.$rsstool_table;
      $a = array ();

      // category
      if ($c)
        $a[] = 'tv2_moved = \''.$c.'\'';

//      $a[] = 'rsstool_related_id = '.misc_related_string_id ($q); // super fast
      $a[] = 'rsstool_related_id = '.sprintf ("%u", $q); // super fast
//      $a[] = 'rsstool_related_id = '.$q; // super fast

      if (isset ($a[0]))
        $sql_query_s .= ' WHERE ( '.implode (' AND ', $a).' )';

      // we sort related by title for playlist
      $sql_query_s .= ' ORDER BY rsstool_title ASC';

      // limit
      $sql_query_s .= ' LIMIT '.$rsscache_max_results;

      $d = rsscache_sql_query ($sql_query_s);

      $d = rsscache_sql_normalize ($d);

      return $d;
    }

  // keyword search
  if ($rsscache_enable_search && $q) // search
    {
      if ($v_segments)
        if (trim ($v_segments) != '')
          $q .= ' +part';
      $b = rsscache_sql_query2boolean ($q, $c);
      $sql_query_s .= ' FROM '.rsscache_sql_keyword_func ($b['any'], $b['require'], $b['exclude'],
isset ($category_xml['rsscache:table_suffix']) ? $category_xml['rsscache:table_suffix'] : NULL);
    }
  else // default
    {
      $sql_query_s .= ' FROM '.$rsstool_table;   
    }

  // category
  if ($c)
    $a[] = 'tv2_moved = \''.$c.'\'';

  // functions
  if ($f == 'new')
    $a[] = 'rsstool_dl_date > '.($rsscache_time - $rsscache_isnew).'';

  if ($rsscache_item_ttl > 0)
    $a[] = 'rsstool_date > '.($rsscache_time - $rsscache_item_ttl);

  if ($f == '0_5min')
    $a[] = 'rsstool_media_duration BETWEEN 0 AND 301';
  else if ($f == '5_10min')
    $a[] = 'rsstool_media_duration BETWEEN 299 AND 601';
  else if ($f == '10_30min')
    $a[] = 'rsstool_media_duration BETWEEN 599 AND 1801';
  else if ($f == '30_60_min')
    $a[] = 'rsstool_media_duration BETWEEN 1799 AND 3601';
  else if ($f == '60_min' || $f == '1_h')
    $a[] = 'rsstool_media_duration > 3599';

  if (isset ($a[0]))
    $sql_query_s .= ' WHERE ( '.implode (' AND ', $a).' )';

  // sort
//  if ($f == 'score')
//    $sql_query_s .= ' ORDER BY tv2_score ASC';
//  else
  if ($f == 'new' || $rsscache_use_dl_date)
    $sql_query_s .= ' ORDER BY rsstool_dl_date DESC';
  else
    $sql_query_s .= ' ORDER BY rsstool_date DESC';

  // limit
  $sql_query_s .= ' LIMIT '.$start.','.min ($num, $rsscache_max_results);

  $d = rsscache_sql_query ($sql_query_s);

  $d = rsscache_sql_normalize ($d);

  return $d;
}


/*
function
tv2_sql_move ($rsstool_url_crc32, $new_category)
{
  // move item to different category
  global $tv2_sql_db;
  $debug = 0;

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = \''.$tv2_sql_db->sql_stresc ($new_category).'\''
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32);

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
}


function
tv2_sql_vote ($rsstool_url_crc32, $new_score)
{
  global $tv2_sql_db;
  $debug = 0;

  $sql_query_s = 'SELECT tv2_votes,tv2_score FROM rsstool_table'
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32);
  $tv2_sql_db->sql_write ($p, 0, $debug);
  $r = $tv2_sql_db->sql_read (1, $debug);

  if ($new_score > 0)
    $new_score = ($r[0]['tv2_votes'] * $r[0]['tv2_score'] + $new_score) / ($r[0]['tv2_votes'] + 1);
  else
    $new_score = $r[0]['tv2_score'];

  $sql_query_s = 'UPDATE rsstool_table SET tv2_votes = '.($r[0]['tv2_votes'] + 1).',tv2_score = '.$new_score
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32);

  $tv2_sql_db->sql_write ($p, 1, $debug);
}


function
tv2_sql_restore ($rsstool_url_crc32)
{
  // restore original category
  global $tv2_sql_db;
  $debug = 0;

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = tv2_category'
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32);

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
}
*/


/*
function
rsscache_update_ttl ()
{
  global $rsscache_root;
  global $rsscache_item_ttl;
  global $rsscache_time;
  global $db;

  if ($rsscache_item_ttl <= 0) // no ttl set
    {
      echo '$rsscache_time_ttl <= 0: no items removed from database'."\n";
      return;
    }

  $sql_query = ''
//  .'UPDATE rsstool_table'.$table_suffix
//  .' SET'
//  .' rsscache_active = 0'
  .'DELETE'
  .' FROM rsstool_table'.$table_suffix
  .' WHERE rsstool_date < '.($rsscache_time - $rsscache_item_ttl * 86400)
;
  $db->sql_write ($sql_query_s, 0);

  // remove thumbnails
//  $path = $rsscache_root.'/thumbnails/youtube/'.youtube_get_videoid ($rsstool_url).'_'.$i.'.jpg';
//  if (file_exists ($path))
//    unlink ($path);
}
*/


function
rsscache_update_normalize ($d)
{
  global $db;
  global $table_suffix;

  // HACK: fix garbage in the database
  if (strstr ($d['rsstool_url'], 'www.google.com'))
    {
      // remove eventual google redirect
      $offset = strpos ($d['rsstool_url'], '?q=') + 3;
      $len = strpos ($d['rsstool_url'], '&source=') - $offset;
      $d['rsstool_url'] = substr ($d['rsstool_url'], $offset, $len);

      // desc
      $offset = 0;
      $len = strrpos ($d['rsstool_desc'], '<div ');
      if ($len)
        $d['rsstool_desc'] = substr ($d['rsstool_desc'], $offset, $len);
    }
  else if (strstr ($d['rsstool_url'], 'news.google.com'))
    {
      // remove eventual google redirect
      $offset = strpos ($d['rsstool_url'], '&url=') + 5;
      $len = strpos ($d['rsstool_url'], '&usg=') - $offset;
      $d['rsstool_url'] = substr ($d['rsstool_url'], $offset, $len);
    }
  else if (strstr ($d['rsstool_url'], 'www.youtube.com'))
    {
      $d['rsstool_url'] = str_replace ('&feature=youtube_gdata', '', $d['rsstool_url']);
    }

  // fix category names
//  $d['rsscache_category'] = trim ($d['rsscache_category']);
//  $d['rsscache_moved'] = trim ($d['rsscache_moved']);

  // strip any tags from the desc
  $p = $d['rsstool_desc'];
  $p = str_replace ('>', '> ', $p);
  $p = strip_tags2 ($p);
  $p = str_replace (array ('  ', '  ', '  ', '  ', '  '), ' ', $p);
  $d['rsstool_desc'] = $p;
  $d['rsstool_desc'] = str_replace ('youtube.com', '', $d['rsstool_desc']);

  // update
  $p = sprintf ("%u", crc32 ($d['rsstool_url']));
  $sql_query_s = 'UPDATE rsstool_table'.$table_suffix
      .' SET'
      .' rsstool_url = \''.$d['rsstool_url'].'\', '
      .' rsstool_url_crc32 = '.$p.', '
      .' rsstool_desc = \''.$d['rsstool_desc'].'\''
      .' WHERE rsstool_url_crc32 = '.$d['rsstool_url_crc32'].' ;';
  echo $sql_query_s."\n";
  $db->sql_write ($sql_query_s, 0);

  // rename thumbnail
  if ($d['rsstool_url_crc32'] != $p) 
  if (rename ('../htdocs/thumbnails/rsscache/'.$d['rsstool_url_crc32'].'.jpg',
              '../htdocs/thumbnails/rsscache/'.$p.'.jpg'))
    echo 'rename '.$d['rsstool_url_crc32'].'.jpg '.$p.'.jpg'."\n";
}


function
rsscache_update_keywords ($r)
{
  global $db;

  // update keywords using the (updated) misc_get_keywords()
  $t = $r['rsstool_keywords'];
  if (trim ($t) == '')
    {
      $t = $r['rsstool_title'].' '.$r['rsstool_desc'];
//      $t = $r['rsstool_title'];
    }
  $t = misc_get_keywords ($t, 0); // isalnum
  $a = explode (' ', $t);
  for ($j = 0; isset ($a[$j]); $j++)
    if (trim ($a[$j]) != '')
      {
        $sql_query_s = 'INSERT IGNORE INTO keyword_table ('
//               .' rsstool_url_md5,'
               .' rsstool_url_crc32,'
//               .' rsstool_keyword_crc32,'
//               .' rsstool_keyword_crc24,'
               .' rsstool_keyword_crc16'
               .' ) VALUES ('
//               .' \''.$r['rsstool_url_md5'].'\','
               .' '.$r['rsstool_url_crc32'].','
//               .' '.sprintf ("%u", crc32 ($a[$j])).','
//               .' '.sprintf ("%u", misc_crc24 ($a[$j])).','
               .' '.misc_crc16 ($a[$j])
               .' );'
               ."\n";

        // DEBUG
//        echo $sql_query_s."\n";
        $db->sql_write ($sql_query_s, 0);
      }
}


function
rsscache_update_related_id ($r)
{
  global $db;
  global $table_suffix;

  $id = misc_related_string_id ($r['rsstool_title']);

  $sql_query_s = 'UPDATE rsstool_table'.$table_suffix
                .' SET'
                .' rsstool_related_id = '.sprintf ("%u", $id).''
                .' WHERE rsstool_url_crc32 = '.$r['rsstool_url_crc32'].';';

//  $sql_query_s = 'UPDATE rsstool_table'.$table_suffix
//                .' SET'
//                .' rsstool_related_id = CASE '.sprintf ("%u", $id).''
//                .' WHEN 1 THEN \'value\''
//                .' WHEN 1 THEN \'value\''
//                .' WHEN 1 THEN \'value\''
//                .' END'
//                .' WHERE rsstool_url_crc32 IN ( '.$r['rsstool_url_crc32'].' );';
//UPDATE rsstool_table'.$table_suffix
//    SET myfield = CASE other_field
//        WHEN 1 THEN 'value'
//        WHEN 2 THEN 'value'
//        WHEN 3 THEN 'value'
//    END
//WHERE id IN (1,2,3)

  // DEBUG
//  echo $sql_query_s."\n";
  $db->sql_write ($sql_query_s, 0);
}


function
rsscache_update_moved ()
{
//  global $table_suffix;
//UPDATE rsstool_table'.$table_suffix.' SET rsscache_moved = rsscache_category WHERE rsscache_moved LIKE ''
}


function
rsscache_update_crc32 ($r) // in config and rsstool_table
{
  global $db;
  global $table_suffix;

  $rsstool_dl_url_crc32 = sprintf ("%u", crc32 ($r['rsstool_dl_url']));

  if ($rsstool_dl_url_crc32 != $r['rsstool_dl_url_crc32'])
    {
      $sql_query_s = 'UPDATE rsstool_table'.$table_suffix
                    .' SET'
                    .' rsstool_dl_url_crc32 = '.$rsstool_dl_url_crc32
                    .' WHERE rsstool_dl_url_crc32 = '.$r['rsstool_dl_url_crc32'].';';

//    DEBUG
//      echo $sql_query_s."\n";
      $db->sql_write ($sql_query_s, 0);
    }
}


function
rsscache_update_dead_links ($rsstool_url, $rsstool_url_crc32)
{
  global $rsscache_root;
  global $db;
  global $table_suffix;

  if (!youtube_check_dead_link ($rsstool_url, 1)) // use TOR
    return;

  $sql_query_s = 'DELETE'
                .' FROM rsstool_table'.$table_suffix
                .' WHERE rsstool_url_crc32 = '.$rsstool_url_crc32
                .' LIMIT 1';
  $db->sql_write ($sql_query_s, 0);

  // remove thumbnails
//  $path = $rsscache_root.'/thumbnails/youtube/'.youtube_get_videoid ($rsstool_url).'_'.$i.'.jpg';
//  if (file_exists ($path))
//    unlink ($path);
}



}


?>