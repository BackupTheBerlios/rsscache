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
generate_rss ($title, $link, $desc, $item_title_array, $item_link_array, $item_desc_array, $item_media_duration_array = NULL)
{
  $version = 2; // RSS2.0

  $p = '';
  $p .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";

  if ($version == 1)
    $p .= '<rdf:RDF xmlns="http://purl.org/rss/1.0/">'."\n";
  else
    $p .= '<rss version="2.0">'."\n";

  $p .= '  <channel>'."\n"
       .'    <title>'
       .$title
       .'</title>'."\n"
       .'    <link>'
       .$link
       .'</link>'."\n"
       .'    <description>'
       .$desc
       .'</description>'."\n"
//     .'    <dc:date>%ld</dc:date>'
;

  if ($version == 1)
    {
      $p .= '<items>'."\n"
           .'<rdf:Seq>'."\n";

      for ($i = 0; isset ($item_link_array[$i]); $i++)
        $p .= "\n".'        <rdf:li rdf:resource="'
             .htmlspecialchars ($item_link_array[$i], ENT_QUOTES)
             .'"/>';

      $p .= '</rdf:Seq>'."\n"
           .'</items>'."\n"
           .'</channel>'."\n";
    }

  for ($i = 0; isset ($item_link_array[$i]); $i++)
    {
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
           .'      <pubDate>'
           .strftime ("%Y%m%d %H:%M:%S", time ())
//           .time ()
           .'</pubDate>'."\n";
      if ($item_media_duration_array)
        if (isset ($item_media_duration_array[$i]))
          $p .= '      <media:duration>'.$item_media_duration_array[$i].'</media:duration>'."\n";
      $p .= '    </item>'."\n";
    }

  if ($version == 2)
    $p .= '  </channel>'."\n";

  $p .= '</rss>'."\n";

  return $p;
}


function
rss_to_array ($tag, $array, $url)
{
  // TODO: use ->asXML() ?
  $doc = new DOMdocument();
  $doc->load($url);

  $rss_array = array();
  $items = array();

  foreach($doc->getElementsByTagName($tag) AS $node)
    {
      foreach($array AS $key => $value)
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