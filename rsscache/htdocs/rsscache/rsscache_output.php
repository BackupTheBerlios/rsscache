<?php
/*
rsscache_write.php - rsscache engine miscellaneous functions

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
if (!defined ('RSSCACHE_WRITE_PHP'))
{
define ('RSSCACHE_WRITE_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
require_once ('misc/misc.php');
//require_once ('misc/wikipedia.php');
//require_once ('misc/rss.php');
require_once ('misc/sql.php');
require_once ('misc/youtube.php');
require_once ('rsscache_sql.php');


function
rsscache_write_mrss_escape ($s)
{
//  return htmlspecialchars ($s, ENT_QUOTES);
  return '<![CDATA['.$s.']]>';
}


function
rsscache_write_mrss ($channel_title,
                    $channel_link,
                    $channel_desc,
                    $item_title_array,
                    $item_link_array,
                    $item_desc_array,
                    $item_date_array,
                    $item_media_duration_array = NULL,
                    $item_author_array = NULL,
                    $item_image_array = NULL,
                    $item_category_array = NULL
)
{
  global $rsscache_xsl_trans;
  global $rsscache_xsl_stylesheet;
  global $rsscache_time;

  // DEBUG
//  print_r ($rss_title_array);
//  print_r ($rss_link_array);
//  print_r ($rss_desc_array);
//  print_r ($rss_date_array);
//  print_r ($rss_media_duration_array);
//  print_r ($rss_author_array);

  $p = '';

  $p .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";

  if ($rsscache_xsl_trans == 1)
    $p .= '<?xml-stylesheet href="'.$rsscache_xsl_stylesheet.'" type="text/xsl" media="screen"?>'."\n";

  $p .= '<rss version="2.0"'
        .' xmlns:media="http://search.yahoo.com/mrss/"'
        .'>'."\n";

  $p .= '  <channel>'."\n"
       .'    <title>'.rsscache_write_mrss_escape ($channel_title).'</title>'."\n"
       .'    <link>'.rsscache_write_mrss_escape ($channel_link).'</link>'."\n"
       .'    <description>'.rsscache_write_mrss_escape ($channel_desc).'</description>'."\n"
       .'    <lastBuildDate>'.strftime ("%a, %d %h %Y %H:%M:%S %z", $rsscache_time).'</lastBuildDate>'."\n"
//       .'    <language>en</language>'."\n"
//       .'    <image>'."\n"
//       .'      <title><![CDATA[ZDNet - Business et Solutions IT]]></title>'."\n"
//       .'      <url>http://images.zdnet.fr/i/ser/rss/zdnet-rss-logo.gif</url>'."\n"
//       .'      <link>http://www.zdnet.fr/feeds/rss/</link>'."\n"
//       .'      <width>68</width>'."\n"
//       .'      <height>35</height>'."\n"
//       .'    </image>'."\n"
;

  for ($i = 0; isset ($item_link_array[$i]); $i++)
    {
      $p .= '    <item>'."\n";

      $p .= '      <title>'.rsscache_write_mrss_escape ($item_title_array[$i]).'</title>'."\n"
           .'      <link>'.rsscache_write_mrss_escape ($item_link_array[$i]).'</link>'."\n"
           .'      <description>'.rsscache_write_mrss_escape ($item_desc_array[$i]).'</description>'."\n"
           .'      <pubDate>'
//                <pubDate>Fri, 05 Aug 2011 15:03:02 +0200</pubDate>
           .strftime ("%a, %d %h %Y %H:%M:%S %z", $item_date_array[$i])
//           .strftime ("%a, %d %h %Y %H:%M:%S %Z", $item_date_array[$i])
           .'</pubDate>'."\n"
//           .'<category><![CDATA[bMobile : Actualités]]></category>'."\n"
//           .'<comments>http://www.zdnet.fr/produits/test/dell-streak-7-39762776.htm#xtor=123456</comments>'."\n"
;

      if ($item_author_array)
        if (isset ($item_author_array[$i]))
          $p .= '      <author>'.rsscache_write_mrss_escape ($item_author_array[$i]).'</author>'."\n";

      if ($item_image_array)
        if (isset ($item_image_array[$i]))
          $p .= '<enclosure url="'.$item_image_array[$i].'"'
               .' length=""'
               .' type="image/jpeg"'
               .' />'."\n"
;

//      $p .= '<media:group>'."\n";

      $p .= '<media:content url="'.$item_link_array[$i].'" />'."\n";

//<media:category scheme="http://search.yahoo.com/mrss/category_ schema">music/artist/album/song</media:category>

      if ($item_image_array)
        if (isset ($item_image_array[$i]))
          $p .= '      <media:thumbnail url="'.$item_image_array[$i].'" />'."\n";

      if ($item_media_duration_array)
        if (isset ($item_media_duration_array[$i]))
          $p .= '      <media:duration>'.$item_media_duration_array[$i].'</media:duration>'."\n";

//      $p .= '</media:group>'."\n";


      $p .= '    </item>'."\n";
    }

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
  global $rsscache_time;

  $items = 0;
  $items_today = 0;
  $items_7_days = 0;
  $items_30_days = 0;

  $config = config_xml ();

  $rss_title_array = array ();
  $rss_link_array = array ();
  $rss_desc_array = array ();
  $rss_date_array = array ();
  $rss_image_array = array ();

//  $s = '<img src="images/new.png" border="0">';
  $s = 'NEW!';

  for ($i = 0; isset ($config->category[$i]); $i++)
    if ($config->category[$i]->name != '' &&
        (isset ($config->category[$i]->feed[0]->link[0]) || isset ($config->category[$i]->feed[0]->link_prefix)))
      {
        $category = $config->category[$i];
        $p = '';
        $p .= ''
            .($category->items * 1).' items<br>'
            .($category->items_today * 1).' items today';
        if (($category->items_today * 1) > 0)
          $p .= ' '.$s;
        $p .= '<br>'
            .($category->items_7_days * 1).' items last 7 days<br>'
            .($category->items_30_days * 1).' items last 30 days<br>'
            .($category->days * 1).' days since creation of category'
;

        $rss_title_array[] = $category->title;
//        $rss_link_array[] = 'http://'.$_SERVER['SERVER_NAME'].'/?'.$category->query;
        $rss_link_array[] = 'http://'.$_SERVER['SERVER_NAME'].'/?c='.$category->name;
        $rss_desc_array[] = $p;
        $rss_date_array[] = $rsscache_time;
        $rss_image_array[] = $config->category[$i]->logo;

        $items += ($category->items * 1);
        $items_today += ($category->items_today * 1);
        $items_7_days += ($category->items_7_days * 1);
        $items_30_days += ($category->items_30_days * 1);
      }

  $p = ''
//      .'category: '.$config->category[$i]->name.'<br>'
      .($items * 1).' items<br>'
      .($items_today * 1).' items today';
  if (($items_today * 1) > 0) 
    $p .= ' '.$s;
  $p .= '<br>'                                                                               
       .($items_7_days * 1).' items last 7 days<br>'
       .($items_30_days * 1).' items last 30 days<br>'
;
  return rsscache_write_mrss (rsscache_title (),
                             $rsscache_link,
                             $p,
                             $rss_title_array,
                             $rss_link_array,
                             $rss_desc_array,
                             $rss_date_array,
                             NULL,
                             NULL,
                             $rss_image_array);
}


function
rsscache_rss ($d_array)
{
  global $rsscache_link;
  global $rsscache_time;

  $f = rsscache_get_request_value ('f'); // function

  $rss_title_array = array ();
  $rss_link_array = array ();
  $rss_desc_array = array ();
  $rss_date_array = array ();
  $rss_image_array = array ();

  for ($i = 0; isset ($d_array[$i]); $i++)
    {
      $rss_title_array[$i] = $d_array[$i]['rsstool_title'];
//      $rss_link_array[$i] = $d_array[$i]['rsstool_url'];
      if (substr (rsscache_link ($d_array[$i]), 0, 7) == 'http://')
        $rss_link_array[$i] = rsscache_link ($d_array[$i]);
      else
        $rss_link_array[$i] = $rsscache_link.'?'.rsscache_link ($d_array[$i]);

      $rss_desc_array[$i] = $d_array[$i]['rsstool_desc'];
      if ($f == 'new')
        $rss_date_array[$i] = $d_array[$i]['rsstool_dl_date'];
      else
        $rss_date_array[$i] = $d_array[$i]['rsstool_date'];
      $rss_image_array[$i] = rsscache_thumbnail ($d_array[$i], 120, 1);
    }

  return rsscache_write_mrss (rsscache_title (),
                             $rsscache_link,
                  'rsscache urls have a similar syntax like google urls<br>'
                 .'<br>'           
                 .'q=SEARCH&nbsp;&nbsp;   SEARCH query<br>'
                 .'start=N&nbsp;&nbsp;    start from result N<br>'
                 .'num=N&nbsp;&nbsp;      show N results<br>'
                 .'c=NAME&nbsp;&nbsp;     category (leave empty for all categories)<br>'
                 .'item=CRC32&nbsp;&nbsp; show single item<br>'
                 .'f=FUNC&nbsp;&nbsp;     execute FUNCtion<br>'
//                 .'prefix=SUBDOMAIN&nbsp;&nbsp; prefix or SUBDOMAIN (leave empty for current subdomain)<br>'
                 .'<br>'           
                 .'*** functions ***<br>'
                 .'f=0_5min&nbsp;&nbsp;   media with duration 0-5 minutes<br>'
                 .'f=5_10min&nbsp;&nbsp;  media with duration 5-10 minutes<br>'
                 .'f=10_min&nbsp;&nbsp;   media with duration 10+ minutes<br>'
                 .'f=stats&nbsp;&nbsp;    statistics<br>'
                 .'f=new&nbsp;&nbsp;      show only newly created items (default: download time)<br>'
                 .'f=related&nbsp;&nbsp;  find related items (requires &q=SEARCH)<br>'
                 .'f=html&nbsp;&nbsp;     show feed in html (XSL transformation)<br>'
                 .'<br>'
                 .'*** install ***<br>'
                 .'see apache2/sites-enabled/rsscache<br>',
                             $rss_title_array,
                             $rss_link_array,
                             $rss_desc_array,
                             $rss_date_array,  
                             NULL,
                             NULL,
                             $rss_image_array);
}


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


}


?>