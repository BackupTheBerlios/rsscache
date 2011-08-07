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
require_once ('misc/wikipedia.php');
//require_once ('misc/rss.php');
require_once ('misc/sql.php');
require_once ('misc/youtube.php');
require_once ('rsscache_lang.php');
require_once ('rsscache_sql.php');


function
rsscache_write_rss ($channel_title,
                    $channel_link,
                    $channel_desc,
                    $item_title_array,
                    $item_link_array,
                    $item_desc_array,
                    $item_date_array = NULL,
                    $item_media_duration_array = NULL,
                    $item_author_array = NULL)
{
  global $rsscache_xsl_trans;
  global $rsscache_xsl_stylesheet;

//  $d = strftime ("%Y%m%d %H:%M:%S", time ());
  $d = time ();

  // DEBUG
//  print_r ($rss_title_array);
//  print_r ($rss_link_array);
//  print_r ($rss_desc_array);
//  print_r ($rss_date_array);
//  print_r ($rss_media_duration_array);
//  print_r ($rss_author_array);

$channel_desc = 'rsscache urls have a similar syntax like google urls<br>'
               .'<tt><pre>q=SEARCH    SEARCH query<br>'
               .'start=N     start from result N<br>'
               .'num=N       show N results<br>'
               .'c=NAME      category (leave empty for all categories)<br>'
               .'item=CRC32  show single item<br>'
               .'f=FUNC      execute FUNCtion'
               .'</pre></tt><br>'
               .'*** functions ***<br>'
               .'<tt><pre>f=0_5min    media with duration 0-5 minutes<br>'
               .'f=5_10min   media with duration 5-10 minutes<br>'
               .'f=10_min    media with duration 10+ minutes<br>'
               .'f=stats     statistics<br>'
               .'f=new       show only newly created items (default: download time)<br>'
               .'f=related   find related items (requires &q=SEARCH)<br>'
               .'f=html      show feed in html (XSL transformation)<br>'
               .'</pre></tt><br>'
               .'*** install ***<br>'
               .'see apache2/sites-enabled/rsscache<br>';

  $p = '';

  $p .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";

  if ($rsscache_xsl_trans == 1)
    $p .= '<?xml-stylesheet href="'.$rsscache_xsl_stylesheet.'" type="text/xsl" media="screen"?>'."\n";

  $p .= '<rss version="2.0">'."\n";

  $p .= '  <channel>'."\n"
       .'    <title>'
       .htmlspecialchars ($channel_title, ENT_QUOTES)
       .'</title>'."\n"
       .'    <link>'
       .htmlspecialchars ($channel_link, ENT_QUOTES)
       .'</link>'."\n"
       .'    <description>'
       .htmlspecialchars ($channel_desc, ENT_QUOTES)
       .'</description>'."\n"
//       .sprintf ('    <dc:date>%u</dc:date>', $d)
;

  for ($i = 0; isset ($item_link_array[$i]); $i++)
    {
      if ($item_date_array)
        if (isset ($item_date_array[$i]))
          $d = $item_date_array[$i];

      if ($version == 1)
        $p .= '<item rdf:about="'
             .$item_link_array[$i]
             .'">'."\n";
      else
        $p .= '    <item>'."\n";

      $p .= '      <title>'
           .htmlspecialchars ($item_title_array[$i], ENT_QUOTES)
           .'</title>'."\n"
           .'      <link>'
           .htmlspecialchars ($item_link_array[$i], ENT_QUOTES)
           .'</link>'."\n"
           .'      <description>'
           .htmlspecialchars ($item_desc_array[$i], ENT_QUOTES)
           .'</description>'."\n"
;

      if ($item_date_array)
        if (isset ($item_date_array[$i]))
          $p .= '      <pubDate>'
               .$item_date_array[$i]
               .'</pubDate>'."\n";

      if ($item_media_duration_array)
        if (isset ($item_media_duration_array[$i]))
          $p .= '      <media:duration>'.$item_media_duration_array[$i].'</media:duration>'."\n";

      if ($item_author_array)
        if (isset ($item_author_array[$i]))
          $p .= '      <author>'.$item_author_array[$i].'</author>'."\n";

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
  $items = 0;
  $items_today = 0;
  $items_7_days = 0;
  $items_30_days = 0;

  $config = config_xml ();

  $rss_title_array = array ();
  $rss_link_array = array ();
  $rss_desc_array = array ();
  $rss_date_array = array ();

  $s = '<img src="images/new.png" border="0">';

  for ($i = 0; isset ($config->category[$i]); $i++)
    if ($config->category[$i]->name != '' &&
        (isset ($config->category[$i]->feed[0]->link[0]) || isset ($config->category[$i]->feed[0]->link_prefix)))
      {
        $category = $config->category[$i];
        $p = ''
            .'<img src="'.$config->category[$i]->logo.'" border="0"><br>'
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
// TODO: correct date
        $rss_date_array[] = time ();

        $items += ($category->items * 1);
        $items_today += ($category->items_today * 1);
        $items_7_days += ($category->items_7_days * 1);
        $items_30_days += ($category->items_30_days * 1);
      }

        $p = ''
//            .'<!-- lang:category -->: '.$config->category[$i]->name.'<br>'
            .($items * 1).' <!-- lang:items --><br>'
            .($items_today * 1).' <!-- lang:items --> <!-- lang:today -->'
                                     .((items_today * 1) > 0 ? ' '.$s : '').'<br>'
            .($items_7_days * 1).' <!-- lang:items --> <!-- lang:last --> 7 <!-- lang:days --><br>'
            .($items_30_days * 1).' <!-- lang:items --> <!-- lang:last --> 30 <!-- lang:days --><br>'
;
  $p = misc_template ($p, $rsscache_translate[$rsscache_language ? $rsscache_language : 'default']);

  return rsscache_write_rss (rsscache_title (),
                        $rsscache_link,
                       $p,
                       $rss_title_array, $rss_link_array, $rss_desc_array, $rss_date_array);
}


function
rsscache_rss ($d_array)
{
  global $rsscache_link;

  $rss_title_array = array ();
  $rss_link_array = array ();
  $rss_desc_array = array ();
  $rss_date_array = array ();

  for ($i = 0; isset ($d_array[$i]); $i++)
    {
      $rss_title_array[$i] = $d_array[$i]['rsstool_title'];
//      $rss_link_array[$i] = $d_array[$i]['rsstool_url'];
      if (substr (rsscache_link ($d_array[$i]), 0, 7) == 'http://')
        $rss_link_array[$i] = rsscache_link ($d_array[$i]);
      else
        $rss_link_array[$i] = $rsscache_link.'?'.rsscache_link ($d_array[$i]);

      $rss_desc_array[$i] = ''
                           .rsscache_thumbnail ($d_array[$i], 120, 1)
                           .'<br>'
                           .$d_array[$i]['rsstool_desc']
;
      $rss_date_array[] = time ();
    }

  return rsscache_write_rss (rsscache_title (),
                     $rsscache_link,
                     'test',
                     $rss_title_array, $rss_link_array, $rss_desc_array, $rss_date_array);
}


}


?>