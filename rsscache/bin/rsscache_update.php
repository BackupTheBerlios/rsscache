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


// unlimited execution time
//ini_set('max_execution_time', '3600');
set_time_limit (0);


rsscache_sql_open ();


$config = config_xml ();

echo misc_exec ('/etc/init.d/tor restart');

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
$num = 50;
for ($i = $start; $i < $rows; $i += $num)
  {
    // current num
    echo ($i)."\n";

    $sql_query_s = 'SELECT rsstool_title,rsstool_desc,rsstool_url,rsstool_url_crc32,rsstool_keywords'
                  .' FROM rsstool_table'.$table_suffix
//                  .' WHERE 1'
                  .' LIMIT '.$i.','.$num.';';

//    $db->sql_write ($sql_query_s, $debug);
//    $r = $db->sql_read (0 /* $debug */);
    rsscache_sql_query ($sql_query_s);


    for ($j = 0; isset ($r[$j]); $j++)
      {
        // current row
//        echo ($i + $j)."\n"; // current row

//        rsscache_update_normalize ($r[$j]);  // normalize
//        rsscache_update_keywords ($r[$j]);   // update keywords
//        rsscache_update_related_id ($r[$j]); // update related id
//        rsscache_update_crc32 ($r[$j]);      // update crc32 checksums
//        rsscache_update_dead_links ($r[$j]['rsstool_url'], $r[$j]['rsstool_url_crc32']); // check dead link

        // youtube specific
        if (strstr ($r[$j]['rsstool_url'], '.youtube.')) 
          {
//            youtube_thumbnail ($r[$j]['rsstool_url']); // get (missing) youtube thumbnails
          }
      }

    if ($j < $num)
      break;

//    sleep (60);
  }

rsscache_sql_close ();


exit;

?>