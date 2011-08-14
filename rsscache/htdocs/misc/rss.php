<?php
/*
rss.php - miscellaneous RSS functions

Copyright (c) 2006 - 2011 NoisyB


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
if (!defined ('MISC_RSS_PHP'))
{
define ('MISC_RSS_PHP', 1);


function
generate_rss2_escape ($s)
{
//  return htmlspecialchars ($s, ENT_QUOTES);
  return '<![CDATA['.$s.']]>';
}


function
generate_rss2 ($channel, $item, $use_mrss = 0, $use_rsscache = 0, $xsl_stylesheet = NULL)
/*
format:
channel
  title
  link
  desc
item[]
  title
  link
  desc
  date
  image
  enclosure
  category
  media_duration
  user
  dl_date
  keywords
  related_id
  event_start
  event_end
  url_crc32
*/
{
  // DEBUG
//  print_r ($channel);
//  print_r ($item);

  $p = '';

  $p .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";

  if ($xsl_stylesheet)
    $p .= '<?xml-stylesheet href="'.$xsl_stylesheet.'" type="text/xsl" media="screen"?>'."\n";

  $p .= '<rss version="2.0"';

  if ($use_mrss == 1)
    $p .= ' xmlns:media="http://search.yahoo.com/mrss/"';

  if ($use_rsscache == 1)
    $p .= ' xmlns:rsscache="http://www.example.com/rsscache/"';

  $p .= '>'."\n";

  $p .= '  <channel>'."\n"
       .'    <title>'.generate_rss2_escape ($channel['title']).'</title>'."\n"
       .'    <link>'.generate_rss2_escape ($channel['link']).'</link>'."\n"
       .'    <description>'.generate_rss2_escape ($channel['desc']).'</description>'."\n";

  if (isset ($channel['lastBuildDate']))
    $p .= '    <lastBuildDate>'.strftime ("%a, %d %h %Y %H:%M:%S %z", $channel['lastBuildDate']).'</lastBuildDate>'."\n";

  if (isset ($channel['logo']))
    $p .= ''
//       .'    <language>en</language>'."\n"
       .'    <image>'."\n"
//       .'      <title><![CDATA[]]></title>'."\n"

       .'      <url>'.$channel['logo'].'</url>'."\n"

//       .'      <link>'.generate_rss2_escape ($channel['link']).'</link>'."\n"
//       .'      <width></width>'."\n"
//       .'      <height></height>'."\n"
       .'    </image>'."\n"
;

  for ($i = 0; isset ($item[$i]['link']); $i++)
    {
      $p .= '    <item>'."\n";

      $p .= '      <title>'.generate_rss2_escape ($item[$i]['title']).'</title>'."\n"
           .'      <link>'.generate_rss2_escape ($item[$i]['link']).'</link>'."\n"
           .'      <description>'.generate_rss2_escape ($item[$i]['desc']).'</description>'."\n"
           .'      <pubDate>'
//                <pubDate>Fri, 05 Aug 2011 15:03:02 +0200</pubDate>
           .strftime ("%a, %d %h %Y %H:%M:%S %z", $item[$i]['date'])
//           .strftime ("%a, %d %h %Y %H:%M:%S %Z", $item[$i]['date'])
           .'</pubDate>'."\n"
//           .'<comments>http://domain/bla.txt</comments>'."\n"
;

        if (isset ($item[$i]['category']))
          $p .= '      <category><![CDATA['.$item[$i]['category'].']]></category>'."\n";

        if (isset ($item[$i]['user']))
          $p .= '      <author>'.generate_rss2_escape ($item[$i]['user']).'</author>'."\n";

        if (isset ($item[$i]['enclosure']))
          {
            $suffix = strtolower (get_suffix ($item[$i]['enclosure']));

            // HACK
            if ($suffix == '.jpg') $suffix = '.jpeg';

            // TODO: get filesize from db
            $p .= '      <enclosure url="'.$item[$i]['image'].'" length="" type="image/'.substr ($suffix, 1).'" />'."\n";
          }

      // mrss
      if ($use_mrss == 1)
        {
//      $p .= '      <media:group>'."\n";

//      $p .= '        <media:content url="'.$item[$i]['link'].'" />'."\n";

//      $p .= '        <media:category scheme="">'..'</media:category>';

      if (isset ($item[$i]['image']))
        $p .= '        <media:thumbnail url="'.$item[$i]['image'].'" />'."\n";

      if (isset ($item[$i]['media_duration']))
        $p .= '        <media:duration>'.$item[$i]['media_duration'].'</media:duration>'."\n";

      if (isset ($item[$i]['keywords']))
        $p .= '        <media:keywords><![CDATA['.str_replace (' ', ', ', $item[$i]['keywords']).']]></media:keywords>'."\n";

//      $p .= '      </media:group>'."\n";
        }

      // rsscache
      if ($use_rsscache == 1)
        {
//      $p .= '      <rsscache:group>'."\n";

      if (isset ($item[$i]['dl_date']))
        $p .= '        <rsscache:dl_date>'.sprintf ("%u", $item[$i]['dl_date']).'</rsscache:dl_date>'."\n";

      if (isset ($item[$i]['date']))
        $p .= '        <rsscache:date>'.sprintf ("%u", $item[$i]['date']).'</rsscache:date>'."\n";

      if (isset ($item[$i]['related_id']))
        $p .= '        <rsscache:related_id>'.sprintf ("%u", $item[$i]['related_id']).'</rsscache:related_id>'."\n";

      if (isset ($item[$i]['event_start']))
        $p .= '        <rsscache:event_start>'.sprintf ("%u", $item[$i]['event_start']).'</rsscache:event_start>'."\n";

      if (isset ($item[$i]['event_end']))
        $p .= '        <rsscache:event_end>'.sprintf ("%u", $item[$i]['event_end']).'</rsscache:event_end>'."\n";

      if (isset ($item[$i]['url_crc32']))
        $p .= '        <rsscache:url_crc32>'.sprintf ("%u", $item[$i]['url_crc32']).'</rsscache:url_crc32>'."\n";

//      $p .= '      </rsscache:group>'."\n";
        }

      $p .= '    </item>'."\n";
    }

  $p .= '  </channel>'."\n";

  $p .= '</rss>'."\n";

  return $p;
}


function
generate_rss ($title, $link, $desc, $item_title_array, $item_link_array, $item_desc_array,
              $item_media_duration_array = NULL,
              $item_author_array = NULL)
{
  $item = array ();
  for ($i = 0; isset ($item_link_array[$i]); $i++)
    $item[] = array ('title' => $item_link_array[$i],
                     'link' => $item_link_array[$i],
                     'desc' => $item_desc_array[$i],
                     'media:duration' => $item_media_duration_array[$i],
                     'author' => $item_author_array[$i]);


  return generate_rss2 (array ('title' => $title,
                               'link' => link,
                               'desc' => desc), $item, 1);
}


function
rss_to_array ($tag, $rss_tags, $url)
{
  // TODO: use ->asXML() ?
  $doc = new DOMdocument();
  $doc->load($url);

  $rss_array = array();
  $items = array();

  foreach($doc->getElementsByTagName($tag) AS $node)
    {
      foreach($rss_tags AS $key => $value)
        {
          $items[$value] = $node->getElementsByTagName($value)->item(0)->nodeValue;
        }
      array_push ($rss_array, $items);
    }

  return $rss_array;
}


function
parse_rss_from_url ($rss_url)
{
  $rss_tags = array(
    'title',
    'link',
    'guid',
    'comments',
    'description',
    'pubDate',
    'category',
  );
  $rss_item_tag = 'item';
    
  $rssfeed = rss_to_array ($rss_item_tag, $rss_tags, $rss_url);
    
  return $rssfeed;
}


}

?>