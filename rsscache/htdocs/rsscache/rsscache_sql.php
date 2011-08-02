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
misc_sql_stresc ($s, $db_conn = NULL)
{
  if ($db_conn)
    return mysql_real_escape_string ($s, $db_conn);
  if (function_exists ('mysql_escape_string'))
    return mysql_escape_string ($s); // deprecated
  echo 'WARNING: neither mysql_real_escape_string() or mysql_escape_string() could be found/used'."\n"
      .'         making this script vulnerable to SQL injection attacks'."\n";
  return $s;
}


function
rsstool_write_ansisql ($xml, $rsscache_category, $table_suffix = NULL, $db_conn = NULL)
{
  $sql_update = 0;
  $rsscache_engine = 1;
  $p = '';

  $p .= '-- -----------------------------------------------------------'."\n"
       .'-- RSStool - read, parse, merge and write RSS and Atom feeds'."\n"
       .'-- -----------------------------------------------------------'."\n"
       ."\n"
       .'-- DROP TABLE IF EXISTS rsstool_table;'."\n"
       .'-- CREATE TABLE rsstool_table ('."\n"
//       .'--   rsstool_url_md5 varchar(32) NOT NULL default \'\','."\n"
       .'--   rsstool_url_crc32 int(10) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_site text NOT NULL,'."\n"
       .'--   rsstool_dl_url text NOT NULL,'."\n"
//       .'--   rsstool_dl_url_md5 varchar(32) NOT NULL default \'\','."\n"
       .'--   rsstool_dl_url_crc32 int(10) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_title text NOT NULL,'."\n"
//       .'--   rsstool_title_md5 varchar(32) NOT NULL default \'\','."\n"
       .'--   rsstool_title_crc32 int(10) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_desc text NOT NULL,'."\n"
       .'--   rsstool_date bigint(20) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_dl_date bigint(20) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_keywords text NOT NULL,'."\n"
       .'--   rsstool_media_duration bigint(20) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_image text NOT NULL,'."\n"
       .'--   rsstool_event_start bigint(20) unsigned NOT NULL default \'0\','."\n"
       .'--   rsstool_event_end bigint(20) unsigned NOT NULL default \'0\','."\n"
       .'--   UNIQUE KEY rsstool_url_crc32 (rsstool_url_crc32),'."\n"
//       .'--   UNIQUE KEY rsstool_url_md5 (rsstool_url_md5),'."\n"
//       .'--   UNIQUE KEY rsstool_title_crc32 (rsstool_title_crc32),'."\n"
//       .'--   UNIQUE KEY rsstool_title_md5 (rsstool_title_md5),'."\n"
//       .'--   FULLTEXT KEY rsstool_title (rsstool_title),'."\n"
//       .'--   FULLTEXT KEY rsstool_desc (rsstool_desc)'."\n"
       .'-- ) TYPE=MyISAM;'."\n"
       ."\n";

  $p .= ''
       .'-- DROP TABLE IF EXISTS rsstool_table;'."\n"
       .'-- CREATE TABLE IF NOT EXISTS keyword_table ('."\n"
//       .'--   rsstool_url_md5 varchar(32) NOT NULL,'."\n"
       .'--   rsstool_url_crc32 int(10) unsigned NOT NULL,'."\n"
//       .'--   rsstool_keyword_crc32 int(10) unsigned NOT NULL,'."\n"
//       .'--   rsstool_keyword_crc24 int(10) unsigned NOT NULL,'."\n"
       .'--   rsstool_keyword_crc16 smallint(5) unsigned NOT NULL,'."\n"
       .'--   PRIMARY KEY (rsstool_url_crc32,rsstool_keyword_crc16),'."\n"
//       .'--   KEY rsstool_keyword_24bit (rsstool_keyword_crc24),'."\n"
       .'--   KEY rsstool_keyword_16bit (rsstool_keyword_crc16)'."\n"
       .'-- ) ENGINE=MyISAM DEFAULT CHARSET=utf8;'."\n"
       ."\n";

  $items = count ($xml->item);
  for ($i = 0; $i < $items; $i++)
    if ($xml->item[$i]->url != '')
    {
      $p .= 'INSERT IGNORE INTO ';
      // rsstool_table
      $s = 'rsstool_table';
      if ($table_suffix)
        if (trim ($table_suffix) != '')
          $s = 'rsstool_table_'.$table_suffix;
      $p .= $s;
      $p .= ' ('
           .' rsstool_dl_url,'
//           .' rsstool_dl_url_md5,'
           .' rsstool_dl_url_crc32,'
           .' rsstool_dl_date,'
           .' rsstool_site,'
           .' rsstool_url,'
//           .' rsstool_url_md5,'
           .' rsstool_url_crc32,'
           .' rsstool_date,'
           .' rsstool_title,'
//           .' rsstool_title_md5,'
           .' rsstool_title_crc32,'
           .' rsstool_desc,'
           .' rsstool_keywords,'
           .' rsstool_related_id,'
           .' rsstool_media_duration,'
           .' rsstool_image,'
           .' rsstool_user,'
           .' rsstool_event_start,'
           .' rsstool_event_end';

      // HACK: rsscache category
      if ($rsscache_engine == 1)
        $p .= ', tv2_category, tv2_moved';

      $p .= ' ) VALUES ('
           .' \''.misc_sql_stresc ($xml->item[$i]->dl_url, $db_conn).'\','
//           .' \''.$xml->item[$i]->dl_url_md5.'\','
           .' \''.$xml->item[$i]->dl_url_crc32.'\','
           .' \''.$xml->item[$i]->dl_date.'\','
           .' \''.misc_sql_stresc ($xml->item[$i]->site, $db_conn).'\','
           .' \''.misc_sql_stresc ($xml->item[$i]->url, $db_conn).'\','
//           .' \''.$xml->item[$i]->url_md5.'\','
           .' \''.$xml->item[$i]->url_crc32.'\','
           .' \''.$xml->item[$i]->date.'\','
           .' \''.misc_sql_stresc ($xml->item[$i]->title, $db_conn).'\','
//           .' \''.$xml->item[$i]->title_md5.'\','
           .' \''.$xml->item[$i]->title_crc32.'\','
           .' \''.misc_sql_stresc ($xml->item[$i]->desc, $db_conn).'\','
           .' \''.misc_sql_stresc ($xml->item[$i]->media_keywords, $db_conn).'\','
           .' '.sprintf ("%u", misc_related_string_id ($xml->item[$i]->title)).','
           .' \''.($xml->item[$i]->media_duration * 1).'\','
           .' \''.$xml->item[$i]->image.'\','  
           .' \''.$xml->item[$i]->user.'\','  
           .' \''.($xml->item[$i]->event_start * 1).'\','
           .' \''.($xml->item[$i]->event_end * 1).'\'';

      // HACK: rsscache category
      if ($rsscache_engine == 1)
        $p .= ', \''.$rsscache_category.'\', \''.$rsscache_category.'\'';

      $p .= ' );'."\n";

      // UPDATE rsstool_table
      $p .= '-- just update if row exists'."\n";
      if ($sql_update == 0)
        $p .= '-- ';
      $p .= 'UPDATE rsstool_table SET '
           .' rsstool_title = \''.misc_sql_stresc ($xml->item[$i]->title, $db_conn).'\','
//           .' rsstool_title_md5 = \''.$xml->item[$i]->title_md5.'\','
           .' rsstool_title_crc32 = \''.$xml->item[$i]->title_crc32.'\','
           .' rsstool_desc = \''.misc_sql_stresc ($xml->item[$i]->desc, $db_conn).'\''
           .' WHERE rsstool_url_crc32 = '.$xml->item[$i]->url_crc32
           .';'
           ."\n";

      // keyword_table
      $a = explode (' ', $xml->item[$i]->media_keywords);
      for ($j = 0; isset ($a[$j]); $j++)
        if (trim ($a[$j]) != '')
          $p .= 'INSERT IGNORE INTO keyword_table ('
//               .' rsstool_url_md5,'
               .' rsstool_url_crc32,'
//               .' rsstool_keyword_crc32,'
//               .' rsstool_keyword_crc24,'
               .' rsstool_keyword_crc16'
               .' ) VALUES ('
//               .' \''.$xml->item[$i]->url_md5.'\','
               .' '.$xml->item[$i]->url_crc32.','
//               .' '.sprintf ("%u", crc32 ($a[$j])).','
//               .' '.sprintf ("%u", misc_crc24 ($a[$j])).','
               .' '.misc_crc16 ($a[$j])
               .' );'
               ."\n";
    }

  return $p;
}


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
rsscache_sql_stats_func ($category = NULL, $t = 0)
{
  $a = array ();
  if ($category)
    $a[] = 'tv2_moved = \''.$category.'\'';
  if ($t > 0)
    $a[] = 'rsstool_dl_date > '.$t;
  $sql_query_s = 'SELECT SQL_CACHE tv2_moved, MIN(rsstool_dl_date), COUNT(*) AS rsscache_rows'
        .' FROM rsstool_table'
        .(count ($a) ? ' WHERE ( '.implode (' AND ', $a).' )' : '')
        .' GROUP BY tv2_moved'
//        .' ORDER BY rsscache_rows DESC'
;
  // DEBUG
//echo $sql_query_s.'<br>';
  return $sql_query_s;
}


function
rsscache_sql_stats ($db, $category = NULL)
{
  global $rsscache_time;

  $debug = 0;

  $stats = array ();

  // total items and days since creation
  $sql_query_s = rsscache_sql_stats_func ($category, 0);
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
  $sql_query_s = rsscache_sql_stats_func ($category, mktime (0, 0, 0));
//  $sql_query_s = rsscache_sql_stats_func ($category, mktime (0, 0, 0, date ('n'), date ('j')));
//  $sql_query_s = rsscache_sql_stats_func ($category, $rsscache_time);
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
  $sql_query_s = rsscache_sql_stats_func ($category, mktime (0, 0, 0, date ('n'), date ('j') - 7));
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
  $sql_query_s = rsscache_sql_stats_func ($category, mktime (0, 0, 0, date ('n'), date ('j') - 30));
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
      // demux
//      $d[$i]['rsscache_demux'] = widget_media_demux ($d[$i]['rsstool_url']);

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
         $rsscache_cloud_results;
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
//                 .' tv2_category,'
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


function
rsscache_sql_move ($rsstool_url_crc32, $new_category)
{
/*
  // move item to different category
  global $rsscache_sql_db;
  $debug = 0;

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = \''.$rsscache_sql_db->sql_stresc ($new_category).'\''
                .' WHERE rsstool_url_crc32 = '.$rsscache_sql_db->sql_stresc ($rsstool_url_crc32);

  $rsscache_sql_db->sql_write ($sql_query_s, 0, $debug);
*/
}


function
rsscache_sql_vote ($rsstool_url_crc32, $new_score)
{
/*
  global $rsscache_sql_db;
  $debug = 0;

  $sql_query_s = 'SELECT tv2_votes,tv2_score FROM rsstool_table'
                .' WHERE rsstool_url_crc32 = '.$rsscache_sql_db->sql_stresc ($rsstool_url_crc32);
  $rsscache_sql_db->sql_write ($p, 0, $debug);
  $r = $rsscache_sql_db->sql_read (1, $debug);

  if ($new_score > 0)
    $new_score = ($r[0]['tv2_votes'] * $r[0]['tv2_score'] + $new_score) / ($r[0]['tv2_votes'] + 1);
  else
    $new_score = $r[0]['tv2_score'];

  $sql_query_s = 'UPDATE rsstool_table SET tv2_votes = '.($r[0]['tv2_votes'] + 1).',tv2_score = '.$new_score
                .' WHERE rsstool_url_crc32 = '.$rsscache_sql_db->sql_stresc ($rsstool_url_crc32);

  $rsscache_sql_db->sql_write ($p, 1, $debug);
*/
}


function
rsscache_sql_restore ($rsstool_url_crc32)
{
/*
  // restore original category
  global $rsscache_sql_db;
  $debug = 0;

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = tv2_category'
                .' WHERE rsstool_url_crc32 = '.$rsscache_sql_db->sql_stresc ($rsstool_url_crc32);

  $rsscache_sql_db->sql_write ($sql_query_s, 0, $debug);
*/
}


}


?>