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
require_once ('misc/misc.php');
require_once ('misc/sql.php');


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
//  if ($debug == 1)
//    echo $sql_query_s.'<br>';
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

  for ($i = 0; isset ($sql_array[$i]); $i++)
    {
      $sql_query_s = $sql_array[$i];

      if (substr (trim ($sql_query_s), 0, 2) == '--')
        continue;

      rsscache_sql_query ($sql_query_s);
    }
}


function
rsscache_sql_stats_func ($c = NULL, $table_suffix = NULL, $t = 0)
{
  $a = array ();

  $rsstool_table = 'rsstool_table';
  $keyword_table = 'keyword_table';
  if ($table_suffix)
    if (trim ($table_suffix) != '')
      {      
        $rsstool_table .= '_'.$table_suffix;
        // TODO
//      $keyword_table .= '_'.$table_suffix;
      }

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
rsscache_sql_stats ($db, $c = NULL)
{
  global $rsscache_time;
  $category = config_xml_by_category ($c);

  $debug = 0;

  $stats = array ();

  // total items and days since creation
  $sql_query_s = rsscache_sql_stats_func ($c, $category->table_prefix, 0);
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    {
      $a = array ('category' => $r[$i][0],
                  'items' => (int) $r[$i][2],
                  'days' => (int) (($rsscache_time - $r[$i][1]) / 86400));
      $stats[] = $a;
    }

  // downloaded items today
  $sql_query_s = rsscache_sql_stats_func ($c, $category->table_prefix, mktime (0, 0, 0));
//  $sql_query_s = rsscache_sql_stats_func ($c, $category->table_prefix, mktime (0, 0, 0, date ('n'), date ('j')));
//  $sql_query_s = rsscache_sql_stats_func ($c, $category->table_prefix, $rsscache_time);
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    for ($j = 0; isset ($stats[$j]); $j++)
      if ($r[$i]['tv2_moved'] == $stats[$j]['category'])
        {
          $stats[$j]['items_today'] = (int) $r[$i][2];
          break;
        }

  // downloaded items last 7 days   
  $sql_query_s = rsscache_sql_stats_func ($c, $category->table_prefix, mktime (0, 0, 0, date ('n'), date ('j') - 7));
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    for ($j = 0; isset ($stats[$j]); $j++)
      if ($r[$i]['tv2_moved'] == $stats[$j]['category'])
        {
          $stats[$j]['items_7_days'] = (int) $r[$i][2];
          break;
        }

  // downloaded items last 30 days
  $sql_query_s = rsscache_sql_stats_func ($c, $category->table_prefix, mktime (0, 0, 0, date ('n'), date ('j') - 30));
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    for ($j = 0; isset ($stats[$j]); $j++)
      if ($r[$i]['tv2_moved'] == $stats[$j]['category'])
        {
          $stats[$j]['items_30_days'] = (int) $r[$i][2];
          break;
        }

  // DEBUG
//echo '<pre><tt>';
//print_r ($stats);
//exit;

  return $stats;
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
      $d[$i]['tv2_category'] = strtolower (trim ($d[$i]['tv2_category']));
      $d[$i]['tv2_moved'] = strtolower (trim ($d[$i]['tv2_moved']));
    }

  return $d;
}


function
rsscache_sql_query2boolean ($q, $c = NULL)
{
  $debug = 0;

  // filter
  $filter = '';
  if ($c)
    {
      $category = config_xml_by_category ($c);
      if ($category)
        if ($category->filter)
          $filter = $category->filter;
    }

  $q = trim ($q.' '.$filter);
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

  $rsstool_table = 'rsstool_table';
  $keyword_table = 'keyword_table';
  if ($table_suffix)
    if (trim ($table_suffix) != '')
      {
        $rsstool_table .= '_'.$table_suffix;
        // TODO
//      $keyword_table .= '_'.$table_suffix;
      }

  // HACK: merge any and require since result is sorted by number of matches
  $q = $any.' '.$require;

  $p = '';
  $p .= ' ('
       .' SELECT'
//       .' DISTINCT'
       .' SQL_CACHE rsstool_url_crc32, COUNT(*) AS rsscache_rows'
       .' FROM keyword_table'
       .' WHERE keyword_table.rsstool_keyword_crc16'
//       .' WHERE keyword_table.rsstool_keyword_crc24'
//       .' WHERE keyword_table.rsstool_keyword_crc32'
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
rsscache_sql ($c, $q, $f, $v, $start, $num, $table_suffix = NULL)
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
         $rsscache_wall_results,
         $rsscache_cloud_results,
//         $rsscache_time,
         $rsscache_item_ttl;
  global $rsscache_debug_sql;

  $debug = $rsscache_debug_sql;
//  $debug = 1;

  $v_segments = rsscache_get_request_value ('v_segments');
//  $q = rsscache_get_request_value ('q'); // we ignore the arg and make sure we get an unescaped one
//  $c = $rsscache_sql_db->sql_stresc ($c);
//  $v = $rsscache_sql_db->sql_stresc ($v);
//  $start = $rsscache_sql_db->sql_stresc ($start);
//  $num = $rsscache_sql_db->sql_stresc ($num);

  $rsstool_table = 'rsstool_table';
  $keyword_table = 'keyword_table';
  if ($table_suffix)
    if (trim ($table_suffix) != '')
      {
        $rsstool_table .= '_'.$table_suffix;
        // TODO
//      $keyword_table .= '_'.$table_suffix;
      }

  if ($f == 'stats')
    return rsscache_sql_stats ($rsscache_sql_db, $c);

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
                 .' tv2_category,'
                 .' tv2_moved,'
                 .' rsstool_event_start,'
                 .' rsstool_event_end,'
                 .' rsstool_media_duration,'
                 .' rsstool_keywords'
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

      $a[] = 'rsstool_related_id = '.misc_related_string_id ($q); // super fast

      if (isset ($a[0]))
        $sql_query_s .= ' WHERE ( '.implode (' AND ', $a).' )';

      // we sort related by title for playlist
      $sql_query_s .= ' ORDER BY rsstool_title ASC';

      // limit
      $sql_query_s .= ' LIMIT '.$rsscache_wall_results;

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
      $sql_query_s .= ' FROM '.rsscache_sql_keyword_func ($b['any'], $b['require'], $b['exclude'], $table_suffix);
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
    $a[] = 'rsstool_media_duration BETWEEN 300 AND 601';
  else if ($f == '10_min')
    $a[] = 'rsstool_media_duration > 600';
  else if ($f == '1_h')
    $a[] = 'rsstool_media_duration > 3600';

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
  $sql_query_s .= ' LIMIT '.$start.','.min ($num, $rsscache_wall_results);

  $d = rsscache_sql_query ($sql_query_s);

  $d = rsscache_sql_normalize ($d);

  return $d;
}


}


?>