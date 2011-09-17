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


/*
function
simplexml_load_file2 ($xml_file)
{
  // wrapper that normalizes XML
  $p = file_get_contents ($xml_file);

  // HACK: remove unknown special chars
  $a = array ();
  for ($i = 0; $i < 30; $i++)
    $a[] = '&#'.$i.';';
  $p = str_replace ($a, '', $p);

  // re-read as XML
  $xml = simplexml_load_string ($p, 'SimpleXMLElement', LIBXML_NOCDATA);

// DEBUG
//print_r ($xml);
//exit;
  return $xml;
}
*/


function
misc_xml_escape ($s)
{
  if ($s != htmlspecialchars ($s, ENT_QUOTES))
    return '<![CDATA['.$s.']]>';
  return $s;
}


function  
misc_object2array ($o, $prefix = NULL)  
{  
  // DEBUG
//  echo '<pre><tt>';
//  print_r (each ($o));
//  while (list($key, $value) = each($o))
//    echo $key.' => '.$value.'<br>';  
//  exit;
  $a = is_object ($o) ? get_object_vars ($o) : $o;
  foreach ($a as $key => $val)
    {
      $v = NULL;
      if (is_array ($val) || is_object ($val)) 
        $v = misc_object2array ($val, $prefix);
      if ($v == NULL)
//      else
        $v = (string) $val;

      $k = ($prefix ? $prefix : '').$key;

      $b[$k] = $v;
    }
  return isset ($b) ? $b : NULL;
}


function
misc_xml2array ($o, $prefix = NULL)
{
/*
  $tag = 'rss';
  $rss_tags = array ('title', 'link', 'description');
  $url = '';

  $doc = new DOMdocument();
  $doc->load ($url);

  $rss_array = array();
  $items = array();

  foreach($doc->getElementsByTagName($tag) AS $node)
    {
      foreach($rss_tags AS $key => $value)
        $items[$value] = $node->getElementsByTagName($value)->item(0)->nodeValue;
      array_push ($rss_array, $items);
    }

  return $rss_array;
*/
  return misc_object2array ($o, $prefix);
}


function
misc_array2object ($a)
{
  $o = is_array ($a) ? new StdClass () : $a;
  if (is_array ($a))
    foreach ($a as $key => $val)
      {
        $v = is_array($val) ? misc_array2object ($val) : $val;
        $o->$key = $v;
      }
  return $o;
}


function
misc_prefixate_array ($a, $prefix = NULL)
{
//  $b = array ();
  foreach ($a as $key => $val)
    $b[($prefix ? $prefix : '').$key] = $val;
//  return $b;
  return isset ($b) ? $b : NULL;
}


function
rss2array ($rss, $debug = 0)
{
  global $rsstool_opts;

  $rss = $rss->channel;

  if ($debug == 1)
    {
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($rss->asXML());
//  exit;
    }

  $item = array ();
  for ($i = 0; isset ($rss->item[$i]); $i++)
    {
      $category = $rss->item[$i];

      $a = misc_object2array ($category);
      if (method_exists ($category, 'children'))
        {
          $o = $category->children ('rsscache', TRUE);
          if ($o)
            $a = array_merge ($a, misc_object2array ($o, 'rsscache:'));

          $o = $category->children ('cms', TRUE);
          if ($o)
            $a = array_merge ($a, misc_object2array ($o, 'cms:'));

          $o = $category->children ('media', TRUE);
          if ($o)
            $a = array_merge ($a, misc_object2array ($o, 'media:'));
        }

//  if ($debug == 1)
//    {
      // DEBUG
//      echo '<pre><tt>';
//      print_r ($a);
//      exit;
//    }

      // feeds
      for ($j = 0; isset ($a['rsscache:feed']['rsscache:'.$j]); $j++)
        if (isset ($a['rsscache:feed']['rsscache:'.$j]))
        {
          $feed = $a['rsscache:feed']['rsscache:'.$j];
          
          // DEBUG
//          echo '<pre><tt>';
//          print_r ($feed);
//          exit;

          $p = ''; 
          if (isset ($feed['rsscache:link']))
            {
              $p .= $feed['rsscache:link'];
            }
          else if (isset ($feed['rsscache:link_prefix']))
            for ($k = 0; isset ($feed['rsscache:link_search']['rsscache:'.$k]); $k++)
              {
//                if (isset ($feed['rsscache:link_prefix']))
                  $p .= $feed['rsscache:link_prefix'];
//                if (isset ($feed['rsscache:link_search']['rsscache:'.$k]))
                  $p .= $feed['rsscache:link_search']['rsscache:'.$k];
                if (isset ($feed['rsscache:link_suffix']))
                  $p .= $feed['rsscache:link_suffix'];
              }

          if (isset ($feed['rsscache:client']))
            $a['rsscache:feed_'.$j.'_client'] = $feed['rsscache:client'];
          $a['rsscache:feed_'.$j.'_opts'] = $rsstool_opts.' '.(isset ($feed['rsscache:opts']) ? $feed['rsscache:opts'] : '');
          $a['rsscache:feed_'.$j.'_link'] = $p;
        }

      // DEBUG
//      echo '<pre><tt>';
//      print_r ($a);
//      exit;

//      if (isset ($a['media:keywords']))
//        $a['media:keywords'] = str_replace (' ', ', ', $a['media:keywords']);

      if (!isset ($a['rsscache:category_title']))
        $a['rsscache:category_title'] = $a['title'];

      if (isset ($a['enclosure']))
        if (isset ($a['enclosure']['@attributes']))  
          if (isset ($a['enclosure']['@attributes']['url']))
            $a['enclosure'] = $a['enclosure']['@attributes']['url'];

      if (isset ($a['media:thumbnail']))
        if (isset ($a['media:thumbnail']['media:@attributes']))  
          if (isset ($a['media:thumbnail']['media:@attributes']['media:url']))
            $a['media:thumbnail'] = $a['media:thumbnail']['media:@attributes']['media:url'];

      unset ($a['rsscache:feed']);
      unset ($a['comment']);
      unset ($a['cms:comment']);
      unset ($a['media:comment']);
      unset ($a['rsscache:comment']);

      // DEBUG
//      echo '<pre><tt>';
//      print_r ($a);
//      exit;

      $item[] = $a;
    }

  $channel = misc_object2array ($rss);
  if (method_exists ($rss, 'children'))
    {
      $o = $rss->children ('rsscache', TRUE);
      if ($o)
        $channel = array_merge ($channel, misc_object2array ($o, 'rsscache:'));

//      $o = $rss->children ('cms', TRUE);
//      if ($o)
//        $channel = array_merge ($channel, misc_object2array ($o, 'cms:'));
    }

  if (isset ($channel['image']))
    if (isset ($channel['image']['url']))
      $channel['image'] = $channel['image']['url']; 

  unset ($channel['item']);

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($channel);
//  print_r ($item);
//  echo generate_rss2 ($channel, $item, 1, 1);
//  exit;

  return array ('channel' => $channel, 'item' => $item);
}


function
generate_rss2_func ($item, $a, $whitespace = '      ')
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

//        if (isset ($item[$t]))
          $p .= $whitespace.'<'.$t.'>'
               .(is_string ($item[$t]) ? misc_xml_escape ($item[$t]) : sprintf ("%u", $item[$t]))
               .'</'.$t.'>'."\n";
      }
  return $p;
}


function
generate_rss2 ($channel, $item, $use_mrss = 0, $use_rsscache = 0, $xsl_stylesheet = NULL)
{
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($channel);
//  print_r ($item);

  $use_cms = 0; 
  if ($use_rsscache == 1)
    $use_cms = 1;

  $p = '';

  $p .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";

  if ($xsl_stylesheet)
    $p .= '<?xml-stylesheet href="'.$xsl_stylesheet.'" type="text/xsl"?>'."\n";

  $p .= '<rss version="2.0"';

  // base
//  $p .= ' xml:base="http://rsscache.a1.25u.com/"';

  if ($use_mrss == 1)
    $p .= ' xmlns:media="http://search.yahoo.com/mrss/"';

  if ($use_rsscache == 1)
    $p .= ' xmlns:rsscache="data:,rsscache"';

  if ($use_cms == 1)
    $p .= ' xmlns:cms="data:,cms"';

  $p .= '>'."\n";

  $p .= '  <channel>'."\n"
;

$a = array (
           'title',
           'link',
           'description',
           'docs',
);

   $p .= generate_rss2_func ($channel, $a, '    ');

  if (isset ($channel['lastBuildDate']))
    $p .= '    <lastBuildDate>'.strftime ("%a, %d %h %Y %H:%M:%S %z", $channel['lastBuildDate']).'</lastBuildDate>'."\n";

  if (isset ($channel['image']))
    $p .= ''
//       .'    <language>en</language>'."\n"
       .'    <image>'."\n"
       .'      <title>'.misc_xml_escape ($channel['title']).'</title>'."\n"
       .'      <url>'.$channel['image'].'</url>'."\n"
       .'      <link>'.misc_xml_escape ($channel['link']).'</link>'."\n"
//       .'      <width></width>'."\n"
//       .'      <height></height>'."\n"
       .'    </image>'."\n"
;
  // textinput
//  $p .= '    <textinput>'."\n"
//       .'      <description>Search Google</description>'."\n"
//       .'      <title>Search</title>'."\n"
//       .'      <link>http://www.google.no/search?</link>'."\n"
//       .'      <name>q</name>'."\n"
//       .'    </textinput>'."\n"
//;

$a = array (
//           'rsscache:stats_category',
           'rsscache:stats_items',
           'rsscache:stats_days',
           'rsscache:stats_items_today',
           'rsscache:stats_items_7_days',
           'rsscache:stats_items_30_days',
);

   $p .= generate_rss2_func ($channel, $a, '    ');

  // items
//  for ($i = 0; isset ($item[$i]['link']); $i++)
  for ($i = 0; isset ($item[$i]); $i++)
    {
      $p .= '    <item>'."\n";

$a = array (
           'title',
           'link',
           'description',
           'category',
           'author',
           'comments',
);
   $p .= generate_rss2_func ($item[$i], $a);

//                <pubDate>Fri, 05 Aug 2011 15:03:02 +0200</pubDate>
      if (isset ($item[$i]['pubDate']))
        $p .= ''
             .'      <pubDate>'
             .strftime (
                "%a, %d %h %Y %H:%M:%S %z",
//                "%a, %d %h %Y %H:%M:%S %Z",
                $item[$i]['pubDate'])
             .'</pubDate>'."\n"
;
        if (isset ($item[$i]['enclosure']))
          {
            $suffix = strtolower (get_suffix ($item[$i]['enclosure']));

            // HACK
            if ($suffix == '.jpg') $suffix = '.jpeg';

            // TODO: get filesize from db
            $p .= '      <enclosure url="'.$item[$i]['enclosure'].'"'
//                 .' length=""'
                 .' type="image/'.substr ($suffix, 1).'" />'."\n"
;
          }

      // mrss
      if ($use_mrss == 1)
        {
/*

<media:title>

The title of the particular media object. It has 1 optional attribute.

        <media:title type="plain">The Judy's - The Moo Song</media:title>

type specifies the type of text embedded. Possible values are either 'plain' or 'html'. Default value is 'plain'. All html must be entity-encoded. It is an optional attribute.

<media:description>

Short description describing the media object typically a sentence in length. It has 1 optional attribute.

        <media:description type="plain">This was some really bizarre band I listened to as a young lad.</media:description>

type specifies the type of text embedded. Possible values are either 'plain' or 'html'. Default value is 'plain'. All html must be entity-encoded. It is an optional attribute.

<media:player>

Allows the media object to be accessed through a web browser media player console. This element is required only if a direct media url attribute is not specified in the <media:content> element. It has 1 required attribute, and 2 optional attributes.

        <media:player url="http://www.foo.com/player?id=1111" height="200" width="400" />

url is the url of the player console that plays the media. It is a required attribute.

height is the height of the browser window that the url should be opened in. It is an optional attribute.

width is the width of the browser window that the url should be opened in. It is an optional attribute.

<media:text>

Allows the inclusion of a text transcript, closed captioning, or lyrics of the media content. Many of these elements are permitted to provide a time series of text. In such cases, it is encouraged, but not required, that the elements be grouped by language and appear in time sequence order based on the start time. Elements can have overlapping start and end times. It has 4 optional attributes.

        <media:text type="plain" lang="en" start="00:00:03.000" 
        end="00:00:10.000"> Oh, say, can you see</media:text>
        <media:text type="plain" lang="en" start="00:00:10.000" 
        end="00:00:17.000">By the dawn's early light</media:text>

type specifies the type of text embedded. Possible values are either 'plain' or 'html'. Default value is 'plain'. All html must be entity-encoded. It is an optional attribute.

lang is the primary language encapsulated in the media object. Language codes possible are detailed in RFC 3066. This attribute is used similar to the xml:lang attribute detailed in the XML 1.0 Specification (Third Edition). It is an optional attribute.

start specifies the start time offset that the text starts being relevant to the media object. An example of this would be for closed captioning. It uses the NTP time code format (see: the time attribute used in <media:thumbnail>).   It is an optional attribute.

end specifies the end time that the text is relevant. If this attribute is not provided, and a start time is used, it is expected that the end time is either the end of the clip or the start of the next <media:text> element.

<media:embed>

Sometimes player specific embed code is needed for a player to play any video. <media:embed> allows inclusion of such information in the form of key value pairs.

     <media:embed url="http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.2" width="512" height="323" >
                <media:param name="type">application/x-shockwave-flash</media:param>
                <media:param name="width">512</media:param>
                <media:param name="height">323</media:param>
                <media:param name="allowFullScreen">true</media:param>
                <media:param name="flashVars">id=7809705&vid=2666306&lang=en-us&intl=us&thumbUrl=http%3A//us.i1.yimg.com/us.yimg.com/i/us/sch/cn/video06/2666306_rndf1e4205b_19.jpg</media:param>
     </media:embed>

<media:status>

Optional tag to specify the status of a media object - whether it's still active or it has been blocked/deleted.

     <media:status state="blocked" reason="http://www.reasonforblocking.com"/>

state can have values "active", "blocked" or "deleted". "active" means a media object is active in the system, "blocked" means a media object is blocked by the publisher, "deleted" means a media object has been deleted by the publisher.

reason is a reason explaining why a media object has been blocked/deleted. It can be plain text or a url.

<media:subTitle>

Optional element for subtitle/CC link. It contains type and language attributes. Language is based on RFC 3066. There can be more than one such tag per media element e.g. one per language. Please refer to Timed Text spec - W3C for more information on Timed Text and Real Time Subtitling.

 
        <media:subTitle type="application/smil" lang="en-us"  href="http://www.example.org/subtitle.smil"  />

<media:peerLink>

Optional element for P2P link.

        <media:peerLink type="application/x-bittorrent " href="http://www.example.org/sampleFile.torrent"  />

For a valid mRSS item, at least one of the following links is required:

    media:content

    media:player
    media:peerLink

<media:location>

Optional element to specify geographical information about various locations captured in the content of a media object. The format conforms to geoRSS.

  <media:location description="My house" start="00:01" end="01:00">
       <georss:where>
       <gml:Point>
         <gml:pos>35.669998 139.770004</gml:pos>
       </gml:Point>
       </georss:where>
      </media:location>

description description of the place whose location is being specified.

start time at which the reference to a particular location starts in the media object.

end time at which the reference to a particular location ends in the media object.

<media:scenes>

Optional element to specify various scenes within a media object. It can have multiple child <media:scene> elements, where each <media:scene> element contains information about a particular scene. <media:scene> has optional sub-elements as "sceneTitle","sceneDescription", "sceneStartTime" and "sceneEndTime", which contains title, description, start and end time of a particular scene in the media respectively.

    <media:scenes>
        <media:scene>
            <sceneTitle>sceneTitle1</sceneTitle>
            <sceneDescription>sceneDesc1</sceneDescription>
            <sceneStartTime>00:15</sceneStartTime>
            <sceneEndTime>00:45</sceneEndTime>
        </media:scene>
        <media:scene>
            <sceneTitle>sceneTitle2</sceneTitle>
            <sceneDescription>sceneDesc2</sceneDescription>
            <sceneStartTime>00:57</sceneStartTime>
            <sceneEndTime>01:45</sceneEndTime>
        </media:scene>
    </media:scenes>
*/
//      $p .= '    <media:group>'."\n";

//      $p .= '      <media:content url="'.$item[$i]['link'].'" />'."\n";

//      $p .= '      <media:category scheme="">'.'</media:category>';

      if (isset ($item[$i]['media:thumbnail']))
        $p .= '      <media:thumbnail url="'.$item[$i]['media:thumbnail'].'" media:url="'.$item[$i]['media:thumbnail'].'" />'."\n";

$a = array (
           'media:duration',
           'media:keywords',
           'media:embed',
);
  
   $p .= generate_rss2_func ($item[$i], $a);

              $a['item'][$i]['media:content_'.$j] = $b[$j];


for ($j = 0; isset ($item[$i]['media:content_'.$j]); $j++)
  {
    if (isset ($item[$i]['media:content_'.$j]))
      $p .= '        <media:content url="'.misc_xml_escape ($item[$i]['media:content_'.$j]).'" />'."\n";
  }

//      $p .= '      </media:group>'."\n";
        }

      // rsscache
      if ($use_rsscache == 1)
        {
//      $p .= '    <rsscache:group>'."\n";

      if (isset ($item[$i]['pubDate']))
        $p .= '      <rsscache:pubDate>'.sprintf ("%u", $item[$i]['pubDate']).'</rsscache:pubDate>'."\n";

$a = array (
           'rsscache:category_title',
           'rsscache:dl_date',
           'rsscache:related_id',
           'rsscache:event_start',
           'rsscache:event_end',
           'rsscache:url_crc32',
           'rsscache:movable',
           'rsscache:reportable',
           'rsscache:votable',
           'rsscache:table_suffix',
           'rsscache:download',
);

   $p .= generate_rss2_func ($item[$i], $a);
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($channel);
//  print_r ($item);

for ($j = 0; isset ($item[$i]['rsscache:feed_'.$j.'_link']); $j++)
  {
    $p .= '      <rsscache:feed>'."\n";
    if (isset ($item[$i]['rsscache:feed_'.$j.'_client']))
      $p .= '        <rsscache:client>'.misc_xml_escape ($item[$i]['rsscache:feed_'.$j.'_client']).'</rsscache:client>'."\n";
    if (isset ($item[$i]['rsscache:feed_'.$j.'_opts']))
      $p .= '        <rsscache:opts>'.misc_xml_escape ($item[$i]['rsscache:feed_'.$j.'_opts']).'</rsscache:opts>'."\n";
    if (isset ($item[$i]['rsscache:feed_'.$j.'_link']))
      $p .= '        <rsscache:link>'.misc_xml_escape ($item[$i]['rsscache:feed_'.$j.'_link']).'</rsscache:link>'."\n";
    $p .= '      </rsscache:feed>'."\n";
  }

//      $p .= '      </rsscache:group>'."\n";
        }
$a = array (
           'rsscache:stats_category',
           'rsscache:stats_items',
           'rsscache:stats_days',
           'rsscache:stats_items_today',
           'rsscache:stats_items_7_days',
           'rsscache:stats_items_30_days',
);

   $p .= generate_rss2_func ($item[$i], $a);

      // CMS
      if ($use_cms == 1)
        {
//          $p .= '    <cms:group>'."\n";

            $a = array (
            'cms:separate',
            'cms:button_only',
            'cms:status',
            'cms:select',
            'cms:local',
            'cms:iframe',
            'cms:proxy',
//            'cms:query',
            'cms:demux',
);

   $p .= generate_rss2_func ($item[$i], $a);

//          $p .= '    </cms:group>'."\n";
        }

      $p .= '    </item>'."\n";
    }

  $p .= '  </channel>'."\n";

  $p .= '</rss>'."\n";

  return $p;
}


function
rss_improve_relevance_s ($rss, $threshold = 66.6) 
{
  // DEBUG
//  print_r ($rss->channel);
//  exit;

//  $threshold = 66.6; // 2/3 similarity is okay for (e.g.) parted movies, etc.
//  $threshold = 0.0;

  $a = array ();
  $a[0][] = (string) $rss->channel->item[0]->title;
  $a[1][] = (string) $rss->channel->item[0]->link;
  $a[2][] = (string) $rss->channel->item[0]->description;

  for ($i = 1; isset ($rss->channel->item[$i]); $i++)
    {
      similar_text ($rss->channel->item[0]->title, $rss->channel->item[$i]->title, $percent);

      if ($percent < $threshold)
//        unset ($rss->channel->item[$i]);
        continue;

          $a[0][] = (string) $rss->channel->item[$i]->title;
          $a[1][] = (string) $rss->channel->item[$i]->link;
          $a[2][] = (string) $rss->channel->item[$i]->description;
    }
//  print_r ($a[0][0]);

  // sort by title (useful for evtl. episodes/parts)
  array_multisort ($a[0], SORT_ASC, SORT_STRING, $a[1], $a[2]);

  $item = array ();
  for ($i = 0; isset ($a[0][$i]); $i++)
    $item[] = array ('title' => $a[0][$i],
                     'link' => $a[1][$i],
                     'description' => '' // $a[2][$i]
,
);

  $channel = array ('title' => (string) $rss->channel->title,
                    'link' => (string) $rss->channel->link,
                    'description' => (string) $rss->channel->description);

  // DEBUG
//  print_r ($channel);
//  print_r ($item);
//  exit;

  return generate_rss2 ($channel, $item, 0, 0);
}


}

?>