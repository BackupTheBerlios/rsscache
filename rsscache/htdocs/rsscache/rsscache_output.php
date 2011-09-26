<?php
/*
rsscache_output.php - rsscache engine output functions

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
if (!defined ('RSSCACHE_OUTPUT_PHP'))
{
define ('RSSCACHE_OUTPUT_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
//require_once ('misc/misc.php');
//require_once ('misc/rss.php');
//require_once ('misc/sql.php');
//require_once ('misc/youtube.php');
require_once ('rsscache_sql.php');


function
rsscache_write_robots ()
{
  $p .= '';
  $p .= 'Sitemap: http://'.$_SERVER['SERVER_NAME'].'/sitemap.xml'."\n"
       .'User-agent: *'."\n"
       .'Allow: /'."\n";

  return $p;
}


function
rsstool_write_ansisql ($a, $rsscache_category, $table_suffix = NULL, $db_conn = NULL)
{
  $sql_update = 0;
  $rsscache_engine = 1;
  $p = '';

  $rsstool_table = rsscache_tablename ('rsstool', $table_suffix);
  $keyword_table = rsscache_tablename ('keyword', $table_suffix);

  $p .= '-- -----------------------------------------------------------'."\n"
       .'-- RSStool - read, parse, merge and write RSS and Atom feeds'."\n"
       .'-- -----------------------------------------------------------'."\n"
       ."\n"
       .'-- DROP TABLE IF EXISTS '.$rsstool_table.';'."\n"
       .'-- CREATE TABLE '.$rsstool_table.' ('."\n"
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
       .'-- DROP TABLE IF EXISTS '.$rsstool_table.';'."\n"
       .'-- CREATE TABLE IF NOT EXISTS '.$keyword_table.' ('."\n"
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

  $items = count ($a['item']);
  for ($i = 0; $i < $items; $i++)
    if ($a['item'][$i]['link'] != '')
    {
      // rsstool_table
      $p .= 'INSERT IGNORE INTO '.$rsstool_table.' ('
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
           .' \''.misc_sql_stresc ($a['item'][$i]['rsscache:dl_url'], $db_conn).'\','
//           .' \''.$a['item'][$i]['rsscache:dl_url_md5'].'\','
           .' \''.$a['item'][$i]['rsscache:dl_url_crc32'].'\','
           .' \''.$a['item'][$i]['rsscache:dl_date'].'\','
           .' \''.misc_sql_stresc ($a['item'][$i]['rsscache:site'], $db_conn).'\','
           .' \''.misc_sql_stresc ($a['item'][$i]['link'], $db_conn).'\','
//           .' \''.$a['item'][$i]['rsscache:url_md5'].'\','
           .' \''.$a['item'][$i]['rsscache:url_crc32'].'\','
           .' \''.$a['item'][$i]['pubDate'].'\','
           .' \''.misc_sql_stresc ($a['item'][$i]['title'], $db_conn).'\','
//           .' \''.$a['item'][$i]['rsscache:title_md5'].'\','
           .' \''.$a['item'][$i]['rsscache:title_crc32'].'\','
           .' \''.misc_sql_stresc ($a['item'][$i]['description'], $db_conn).'\','
           .' \''.misc_sql_stresc ($a['item'][$i]['media_keywords'], $db_conn).'\','
           .' '.sprintf ("%u", misc_related_string_id ($a['item'][$i]['title'])).','
           .' \''.($a['item'][$i]['media_duration'] * 1).'\','
           .' \''.$a['item'][$i]['image'].'\','  
           .' \''.$a['item'][$i]['user'].'\','  
           .' \''.($a['item'][$i]['event_start'] * 1).'\','
           .' \''.($a['item'][$i]['event_end'] * 1).'\'';

      // HACK: rsscache category
      if ($rsscache_engine == 1)
        $p .= ', \''.$rsscache_category.'\', \''.$rsscache_category.'\'';

      $p .= ' );'."\n";

      // UPDATE rsstool_table
      $p .= '-- just update if row exists'."\n";
      if ($sql_update == 0)
        $p .= '-- ';
      $p .= 'UPDATE '.$rsstool_table.' SET '
           .' rsstool_title = \''.misc_sql_stresc ($a['item'][$i]['title'], $db_conn).'\','
//           .' rsstool_title_md5 = \''.$a['item'][$i]['title_md5'].'\','
           .' rsstool_title_crc32 = \''.$a['item'][$i]['title_crc32'].'\','
           .' rsstool_desc = \''.misc_sql_stresc ($a['item'][$i]['description'], $db_conn).'\''
           .' WHERE rsstool_url_crc32 = '.$a['item'][$i]['rsscache:url_crc32']
           .';'
           ."\n";

      // keyword_table
      $a = explode (' ', $a['item'][$i]['media_keywords']);
      for ($j = 0; isset ($a[$j]); $j++)
        if (trim ($a[$j]) != '')
          $p .= 'INSERT IGNORE INTO '.$keyword_table.' ('
//               .' rsstool_url_md5,'
               .' rsstool_url_crc32,'
//               .' rsstool_keyword_crc32,'
//               .' rsstool_keyword_crc24,'
               .' rsstool_keyword_crc16'
               .' ) VALUES ('
//               .' \''.$a['item'][$i]['url_md5'].'\','
               .' '.$a['item'][$i]['url_crc32'].','
//               .' '.sprintf ("%u", crc32 ($a[$j])).','
//               .' '.sprintf ("%u", misc_crc24 ($a[$j])).','
               .' '.misc_crc16 ($a[$j])
               .' );'
               ."\n";
    }

  return $p;
}

// TODO: turn into XSL


/*
{
  "Herausgeber": "Xema",
  "Nummer": "1234-5678-9012-3456",
  "Deckung": 2e+6,
  "Währung": "EUR",
  "Inhaber": {
    "Name": "Mustermann",
    "Vorname": "Max",
    "männlich": true,
    "Depot": {},
    "Hobbys": [ "Reiten", "Golfen", "Lesen" ],
    "Alter": 42,
    "Kinder": [],
    "Partner": null
  }
}
*/


function
generate_json_func ($item, $a)
{
  $p = '';
  for ($i = 0; isset ($a[$i]); $i++)
    if (isset ($item[$a[$i]]))
      {
        $t = $a[$i];

        // DEBUG
//        echo '<pre><tt>';
//        print_r ($item);
//        echo $t;

        if (isset ($item[$t]))
          $p .= '      "'.$t.'": '
               .(is_string ($item[$t]) ? '"'.$item[$t].'"' : sprintf ("%u", $item[$t]))
               .','."\n";
      }
  return $p;
}


function
generate_json ($channel, $item, $use_mrss = 0, $use_rsscache = 0)
{
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($channel);
//  print_r ($item);

  $use_cms = 0; 
  if ($use_rsscache == 1)
    $use_cms = 1;

  $p = '';

//  $p .= $comment;

  $p .= '{';

  $p .= '  {'."\n"
;

$a = array (
           'title',
           'link',
           'description',
           'docs',
//           'rsscache:stats_category',
           'rsscache:stats_items',
           'rsscache:stats_days',
           'rsscache:stats_items_today',
           'rsscache:stats_items_7_days',
           'rsscache:stats_items_30_days',
);

   $p .= generate_json_func ($channel, $a);

  if (isset ($channel['lastBuildDate']))
    $p .= '    "lastBuildDate": "'.strftime ("%a, %d %h %Y %H:%M:%S %z", $channel['lastBuildDate']).'",'."\n";

  if (isset ($channel['image']))
    $p .= ''
       .'    "image": "'.$channel['image'].'",'."\n"
;

  // items
//  for ($i = 0; isset ($item[$i]['link']); $i++)
  for ($i = 0; isset ($item[$i]); $i++)
    {
      $p .= '    {'."\n";

$a = array (
           'title',
           'link',
           'description',
           'category',
           'author',
           'comments',
);
   $p .= generate_json_func ($item[$i], $a);

//                <pubDate>Fri, 05 Aug 2011 15:03:02 +0200</pubDate>
      if (isset ($item[$i]['pubDate']))
        $p .= ''
             .'      "pubDate": "'
             .strftime (
                "%a, %d %h %Y %H:%M:%S %z",
//                "%a, %d %h %Y %H:%M:%S %Z",
                $item[$i]['pubDate'])
             .'",'."\n"
;
      if (isset ($item[$i]['enclosure']))
        $p .= '      "enclosure": "'.$item[$i]['enclosure'].'"'."\n";

      // mrss
      if ($use_mrss == 1)
        {
//      $p .= '      "media:content": "'.$item[$i]['link'].'",'."\n";

//      $p .= '      "media:embed": "'.'",'."\n";

//      $p .= '      "media:category": "'.'",'."\n";

$a = array (
           'media:thumbnail',
           'media:duration',
);
   $p .= generate_json_func ($item[$i], $a);

      if (isset ($item[$i]['media:keywords']))
        $p .= '      "media:keywords": "'.str_replace (' ', ', ', $item[$i]['media:keywords']).'",'."\n";
        }

      // rsscache
      if ($use_rsscache == 1)
        {
      if (isset ($item[$i]['pubDate']))
        $p .= '      "rsscache:pubDate": '.sprintf ("%u", $item[$i]['pubDate']).','."\n";

$a = array (
           'rsscache:dl_date',
           'rsscache:related_id',
           'rsscache:event_start',
           'rsscache:event_end',
           'rsscache:url_crc32',
           'rsscache:movable',
           'rsscache:reportable',
           'rsscache:votable',
           'rsscache:table_suffix',
           'rsscache:stats_category',
           'rsscache:stats_items',
           'rsscache:stats_days',
           'rsscache:stats_items_today',
           'rsscache:stats_items_7_days',
           'rsscache:stats_items_30_days',
);

   $p .= generate_json_func ($item[$i], $a);
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($channel);
//  print_r ($item);


for ($j = 0; isset ($item[$i]['rsscache:feed_'.$j.'_link']); $j++)
  {
    $p .= '    "rsscache:feed": [ '."\n";
    if (isset ($item[$i]['rsscache:feed_'.$j.'_client']))
      $p .= '      "rsscache:client": "'.$item[$i]['rsscache:feed_'.$j.'_client'].'",'."\n";
    if (isset ($item[$i]['rsscache:feed_'.$j.'_opts']))
      $p .= '      "rsscache:opts": "'.$item[$i]['rsscache:feed_'.$j.'_opts'].'",'."\n";
    if (isset ($item[$i]['rsscache:feed_'.$j.'_link']))
      $p .= '      "rsscache:link": "'.$item[$i]['rsscache:feed_'.$j.'_link'].'",'."\n";
    $p .= '    ],'."\n";
  }
        }

      // CMS
      if ($use_cms == 1)
        {
            $a = array (
            'cms:separate',
            'cms:button_only',
            'cms:status',
            'cms:select',
            'cms:local',
            'cms:iframe',
            'cms:proxy',
            'cms:query',
            'cms:demux',
);

   $p .= generate_json_func ($item[$i], $a);
        }

      $p .= '    }'."\n";
    }

  $p .= '  }'."\n";

  $p .= '}'."\n";

  return $p;
}


}


?>