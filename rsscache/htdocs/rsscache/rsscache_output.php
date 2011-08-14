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
require_once ('misc/misc.php');
//require_once ('misc/rss.php');
require_once ('misc/sql.php');
require_once ('misc/youtube.php');
require_once ('rsscache_sql.php');


function
rsscache_write_log_rss ()
{
  // last N requests
}


function
rsscache_write_stats_rss ()
{
  global $rsscache_link;
  global $rsscache_time;
  global $rsscache_logo;
  global $rsscache_xsl_trans;
  global $rsscache_xsl_stylesheet;

  $items = 0;
  $items_today = 0;
  $items_7_days = 0;
  $items_30_days = 0;

  $config = config_xml ();

  $item = array ();

  for ($i = 0; isset ($config->category[$i]); $i++)
    if (trim ($config->category[$i]->name) != '' &&
        (isset ($config->category[$i]->feed[0]->link[0]) ||
         isset ($config->category[$i]->feed[0]->link_prefix)))
      {
        $category = $config->category[$i];
        $p = '';
        $p .= ''
             .($category->items * 1).' items<br>'
             .($category->items_today * 1).' items today<br>'
             .($category->items_7_days * 1).' items last 7 days<br>'
             .($category->items_30_days * 1).' items last 30 days<br>'
             .($category->days * 1).' days since creation of category'
;
        $item[] = array ('title' => $category->title,
                         'link' => 'http://'.$_SERVER['SERVER_NAME'].'/?c='.$category->name,
                         'desc' => $p,
                         'date' => $rsscache_time,
//                       'image' => $category->logo,
                         'category' => $category->name,
                         'media_duration' => 0,
//                         'user' => NULL
                         'dl_date' => $rsscache_time,
//                         'keywords' => NULL,
//                         'related_id' => NULL,
//                         'event_start' => 0,
//                         'event_end' => 0,
//                         'url_crc32' => sprintf ("%u", crc32 ())
);
        $items += ($category->items * 1);
        $items_today += ($category->items_today * 1);
        $items_7_days += ($category->items_7_days * 1);
        $items_30_days += ($category->items_30_days * 1);
      }

  $p = ''
      .($items * 1).' items<br>'
      .($items_today * 1).' items today<br>'
      .($items_7_days * 1).' items last 7 days<br>'
      .($items_30_days * 1).' items last 30 days<br>'
;
  return generate_rss2 (array ('title' => rsscache_title (),
                               'link' => $rsscache_link,
                               'desc' => $p,
                               'logo' => $rsscache_logo,
                               'lastBuildDate' => $rsscache_time), $item, 1, 1,
                               $rsscache_xsl_trans == 1 ? $rsscache_xsl_stylesheet : NULL);

}


function
rsscache_write_rss ($d_array)
{
  global $rsscache_link;
  global $rsscache_time;
  global $rsscache_logo;
  global $rsscache_results;
  global $rsscache_xsl_trans;
  global $rsscache_xsl_stylesheet;

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($d_array);

  $f = rsscache_get_request_value ('f'); // function

  $item = array ();

  for ($i = 0; isset ($d_array[$i]); $i++)
    {
      $link = (substr (rsscache_link ($d_array[$i]), 0, 7) == 'http://') ? 
        rsscache_link ($d_array[$i]) : 
        ($rsscache_link.'?'.rsscache_link ($d_array[$i]));
      $date = ($f == 'new') ?
        $d_array[$i]['rsstool_dl_date'] :
        $d_array[$i]['rsstool_date'];

      $item[] = array ('title' => $d_array[$i]['rsstool_title'],
//                       'link' => $d_array[$i]['rsstool_url'],
                       'link' => $link,
                       'desc' => $d_array[$i]['rsstool_desc'],
                       'date'  => $date,
                       'image' => rsscache_thumbnail ($d_array[$i]),
                       'enclosure' => rsscache_thumbnail ($d_array[$i]),
                       'category' => $d_array[$i]['tv2_moved'],
                       'media_duration' => $d_array[$i]['rsstool_media_duration'],
                       'user' => $d_array[$i]['rsstool_user'],
                       'dl_date' => $d_array[$i]['rsstool_dl_date'],
                       'keywords' => $d_array[$i]['rsstool_keywords'],
                       'related_id' => $d_array[$i]['rsstool_related_id'],
                       'event_start' => $d_array[$i]['rsstool_event_start'],
                       'event_end' => $d_array[$i]['rsstool_event_end'],
                       'url_crc32' => sprintf ("%u", $d_array[$i]['rsstool_url_crc32']),
);
    }

  $p = ''
      .'&amp;q=SEARCH&nbsp;&nbsp;   SEARCH query<br>'
      .'&amp;start=N&nbsp;&nbsp;    start from result N<br>'
      .'&amp;num=N&nbsp;&nbsp;      show N results (default: '.$rsscache_results.')<br>'
      .'&amp;c=NAME&nbsp;&nbsp;     category (leave empty for all categories)<br>'
      .'&amp;item=URL_CRC32&nbsp;&nbsp; show single item<br>'
      .'&amp;f=FUNC&nbsp;&nbsp;     execute FUNCtion<br>'
      .'&amp;output=FORMAT&nbsp;&nbsp; output in "html" or "rss" (default: rss)<br>'
//      .'&amp;prefix=SUBDOMAIN&nbsp;&nbsp; prefix or SUBDOMAIN (leave empty for current subdomain)<br>'
      .'<br>'           
      .'*** functions ***<br>'
      .'&amp;f=author&nbsp;&nbsp;   find user/author/channel (requires &amp;q=SEARCH)<br>'
      .'&amp;f=0_5min&nbsp;&nbsp;   media with duration 0-5 minutes<br>'
      .'&amp;f=5_10min&nbsp;&nbsp;  media with duration 5-10 minutes<br>'
      .'&amp;f=10_30min&nbsp;&nbsp; media with duration 10-30 minutes<br>'
      .'&amp;f=30_60min&nbsp;&nbsp; media with duration 30-60 minutes<br>'
      .'&amp;f=60_min&nbsp;&nbsp;   media with duration 60+ minutes<br>'
      .'&amp;f=new&nbsp;&nbsp;      show only newly created items (default: download time)<br>'
//      .'&amp;f=related&nbsp;&nbsp;  find related items (requires &amp;q=TITLE)<br>'
      .'&amp;f=related&nbsp;&nbsp;  find related items (requires &amp;q=RELATED_ID)<br>'
      .'&amp;f=stats&nbsp;&nbsp;    statistics<br>'
      .'<br>'
      .'*** admin functions ***<br>'
      .'requires access to <a href="./rsscache/admin.php">admin.php</a><br>'
      .'&amp;f=cache&nbsp;&nbsp;    cache (new) items into database (requires &amp;c=CATEGORY)<br>'
//      .'&amp;f=log&nbsp;&nbsp;      show log<br>'
//      .'&amp;f=sql&nbsp;&nbsp;      dump database<br>'
      .'<br>'
      .'*** install ***<br>'
      .'see apache2/sites-enabled/rsscache<br>'
;
  return generate_rss2 (array ('title' => rsscache_title (),
                               'link' => $rsscache_link,
                               'desc' => $p,
                               'logo' => $rsscache_logo,
                               'lastBuildDate' => $rsscache_time), $item, 1, 1,
                               $rsscache_xsl_trans == 1 ? $rsscache_xsl_stylesheet : NULL);
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


}


?>