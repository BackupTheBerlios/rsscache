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
require_once ('misc/widget.php');
//require_once ('misc/wikipedia.php');
//require_once ('misc/rss.php');
require_once ('misc/sql.php');
require_once ('misc/youtube.php');
require_once ('rsscache_lang.php');
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

  $channel_desc = 'rsscache urls have a similar syntax like google urls<br>'
                 .'<br>'           
                 .'q=SEARCH&nbsp;&nbsp;  SEARCH query<br>'
                 .'start=N&nbsp;&nbsp;   start from result N<br>'
                 .'num=N&nbsp;&nbsp;     show N results<br>'
                 .'c=NAME&nbsp;&nbsp;    category (leave empty for all categories)<br>'
                 .'item=CRC32&nbsp;&nbsp;show single item<br>'
                 .'f=FUNC&nbsp;&nbsp;    execute FUNCtion<br>'
                 .'<br>'           
                 .'*** functions ***<br>'
                 .'f=0_5min&nbsp;&nbsp;  media with duration 0-5 minutes<br>'
                 .'f=5_10min&nbsp;&nbsp; media with duration 5-10 minutes<br>'
                 .'f=10_min&nbsp;&nbsp;  media with duration 10+ minutes<br>'
                 .'f=stats&nbsp;&nbsp;   statistics<br>'
                 .'f=new&nbsp;&nbsp;     show only newly created items (default: download time)<br>'
                 .'f=related&nbsp;&nbsp; find related items (requires &q=SEARCH)<br>'
                 .'f=html&nbsp;&nbsp;    show feed in html (XSL transformation)<br>'
                 .'<br>'
                 .'*** install ***<br>'
                 .'see apache2/sites-enabled/rsscache<br>';

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

  $s = '<img src="images/new.png" border="0">';

  for ($i = 0; isset ($config->category[$i]); $i++)
    if ($config->category[$i]->name != '' &&
        (isset ($config->category[$i]->feed[0]->link[0]) || isset ($config->category[$i]->feed[0]->link_prefix)))
      {
        $category = $config->category[$i];
        $p = ''
            .($category->items * 1).' <!-- lang:items --><br>'
            .($category->items_today * 1).' <!-- lang:items --> <!-- lang:today -->'
                                     .(($category->items_today * 1) > 0 ? ' '.$s : '').'<br>'
            .($category->items_7_days * 1).' <!-- lang:items --> <!-- lang:last --> 7 <!-- lang:days --><br>'
            .($category->items_30_days * 1).' <!-- lang:items --> <!-- lang:last --> 30 <!-- lang:days --><br>'
            .($category->days * 1).' <!-- lang:days --> <!-- lang:since creation of category -->'
;
        $p = misc_template ($p, $rsscache_translate[$rsscache_language ? $rsscache_language : 'default']);

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
//      .'<!-- lang:category -->: '.$config->category[$i]->name.'<br>'
      .($items * 1).' <!-- lang:items --><br>'
      .($items_today * 1).' <!-- lang:items --> <!-- lang:today -->'
                               .((items_today * 1) > 0 ? ' '.$s : '').'<br>'
      .($items_7_days * 1).' <!-- lang:items --> <!-- lang:last --> 7 <!-- lang:days --><br>'
      .($items_30_days * 1).' <!-- lang:items --> <!-- lang:last --> 30 <!-- lang:days --><br>'
;
  $p = misc_template ($p, $rsscache_translate[$rsscache_language ? $rsscache_language : 'default']);

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
                             '',
                             $rss_title_array,
                             $rss_link_array,
                             $rss_desc_array,
                             $rss_date_array,  
                             NULL,
                             NULL,
                             $rss_image_array);
}


}


?>