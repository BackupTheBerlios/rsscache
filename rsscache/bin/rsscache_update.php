#!/usr/bin/php -q
<?php
/*
rsscache_update.php - script to customize/update/clean/fix database

Copyright (c) 2009 - 2011 NoisyB


Usage: rsscache_update.php [START_ROW]


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
error_reporting(E_ALL | E_STRICT);
require_once ('../htdocs/rsscache/default.php');
require_once ('../htdocs/config.php');
require_once ('../htdocs/misc/sql.php');
require_once ('../htdocs/misc/misc.php');
require_once ('../htdocs/misc/youtube.php');


$debug = 0;


/*
// done in rsscache_sql.php
function
rsscache_update_ttl ()
{
  global $rsscache_root;
  global $rsscache_item_ttl;
  global $rsscache_time;
  global $db;
  global $table_suffix;

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


// main ()


//ini_set('max_execution_time', '3600');
set_time_limit (0);

//$debug = 1;

$db = new misc_sql;
$db->sql_open ($rsscache_dbhost, $rsscache_dbuser, $rsscache_dbpass, $rsscache_dbname);
// DEBUG
echo 'database: '.$rsscache_dbname.' ('.$rsscache_dbuser.')'."\n";
$table_suffix = '';
//$table_suffix = '_cod4'; 
//$table_suffix = '_css';
//$table_suffix = '_halo3';
//$table_suffix = '_minecraft';
//$table_suffix = '_starcraft2';
//$table_suffix = '_wow';
$rows = $db->sql_get_table_rows ('rsstool_table');
echo 'rows: '.$rows."\n";


// remove items according to $rsscache_item_ttl
//rsscache_update_ttl ();
//$db->sql_close ();
//exit;


// (manual) maintenance
$start = 0;
//$start = 177470; // emulive
//$start = 1125066; // video
//613327
//677000
//742400
$start = $argv[1];
$chunk = 50;
for ($i = $start; $i < $rows; $i += $chunk)
  {
    // current chunk
    echo ($i)."\n";

    $sql_query_s = 'SELECT rsstool_title,rsstool_desc,rsstool_url,rsstool_url_crc32,rsstool_keywords'
                  .' FROM rsstool_table'.$table_suffix
//                  .' WHERE 1'
                  .' LIMIT '.$i.','.$chunk.';';
    $db->sql_write ($sql_query_s, $debug);
    $r = $db->sql_read (0 /* $debug */);


    for ($j = 0; isset ($r[$j]); $j++)
      {
        // current row
//        echo ($i + $j)."\n";

        // normalize
//        rsscache_update_normalize ($r[$j]);

        // rsscache_update keywords
//        rsscache_update_keywords ($r[$j]);

        // rsscache_update related id
//        rsscache_update_related_id ($r[$j]);

        // rsscache_update crc32 checksums
//        rsscache_update_crc32 ($r[$j]);

        // check dead link
//        rsscache_update_dead_links ($r[$j]['rsstool_url'], $r[$j]['rsstool_url_crc32']);

        // youtube specific
        if (strstr ($r[$j]['rsstool_url'], '.youtube.'))
          {
            // get (missing) youtube thumbnails
//            youtube_thumbnail ($r[$j]['rsstool_url']);
          }
      }

    if ($j < $chunk)
      break;

//    sleep (60);
  }

$db->sql_close ();


exit;

?>