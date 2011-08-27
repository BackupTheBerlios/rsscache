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
//phpinfo ();
//error_reporting(E_ALL | E_STRICT);
require_once ('../htdocs/rsscache/default.php');
require_once ('../htdocs/config.php');
require_once ('../htdocs/misc/misc.php');
require_once ('../htdocs/misc/sql.php');
require_once ('../htdocs/misc/youtube.php');
require_once ('../htdocs/rsscache/rsscache_sql.php');
require_once ('../htdocs/rsscache/rsscache_misc.php');


$debug = 0;


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