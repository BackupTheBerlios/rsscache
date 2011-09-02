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
misc_xml_escape ($s)
{
  if ($s != htmlspecialchars ($s, ENT_QUOTES))
    return '<![CDATA['.$s.']]>';
  return $s;
}


function
misc_str_replace_once ($needle, $replace, $haystack)
{
    $pos = strpos ($haystack, $needle);
    if ($pos === false)
      return $haystack;
    return substr_replace ($haystack, $replace, $pos, strlen ($needle));
}  


/*
// TODO: recursive
function
misc_array2object ($a)
{
    if (is_array ($a))
      {
        $o = new StdClass ();
 
        foreach ($a as $key => $val)
          {
            $o->$key = $val;
          }
      }
    else
      {
        $o = $a;
      }
 
    return $o;
}
*/


function
misc_object2array ($o, $prefix = NULL)
{
  foreach ((is_object ($o) ? get_object_vars ($o) : $o) as $key => $val)
    $a[($prefix ? $prefix : '').$key] = (is_array ($val) || is_object ($val)) ? misc_object2array ($val) : $val;
  return $a;
}  


function
misc_array2array ($a, $prefix = NULL)
{
  foreach ($a as $key => $val)
    $b[($prefix ? $prefix : '').$key] = $val;
  return $b;
}


/* 
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
*/


function
rss2array ($rss)
{
  global $rsstool_opts;

  $item = array ();
  for ($i = 0; isset ($rss->item[$i]); $i++)
    {
      $category = $rss->item[$i];
      if (method_exists ($category, 'children'))
        {
          $o = $category->children ('rsscache', TRUE);
          if ($o)
            $rsscache = misc_object2array ($o, 'rsscache:');

          $o = $category->children ('cms', TRUE);
          if ($o)
            $cms = misc_object2array ($o, 'cms:');
        }

      $a = misc_object2array ($category);
      $item_a = array_merge ($cms, $rsscache, $a);

      // DEBUG
//      echo '<pre><tt>';
//      print_r ($item_a);
//      exit;

      // feeds
      $t = array ();
      for ($j = 0; isset ($item_a['rsscache:feed'][$j]); $j++)
        {
          $feed = $item_a['rsscache:feed'][$j];
          // DEBUG
//          echo '<pre><tt>';
//          print_r ($feed);

          // old style config.xml: link[]
          if (isset ($feed['link']))
            if (trim ($feed['link']) != '')
              $t[] = array (
                'client' => $feed->client,
                'opts' => $rsstool_opts.' '.$feed['opts'],
                'link' => $feed['link'],
);
          // TODO: use new style config.xml
          //   link_prefix, link_search[], link_suffix
          if (isset ($feed['link_prefix']))
            for ($k = 0; isset ($feed['link_search'][$k]); $k++)
              {
                $p = '';
//                if (isset ($feed['link_prefix']))
                  $p .= $feed['link_prefix'];
//                if (isset ($feed['link_search'][$k]))
                  $p .= $feed['link_search'][$k];
                if (isset ($feed['link_suffix']))
                  $p .= $feed['link_suffix'];
                $t[] = array (
                  'client' => $feed->client,
                  'opts' => $rsstool_opts.' '.$feed['opts'],
                  'link' => $p,
);
              }
        }

      unset ($item_a['rsscache:feed']);
      for ($j = 0; isset ($t[$j]); $j++)
        {
          $item_a['rsscache:feed_'.$j.'_client'] = $t[$j]['client'];
          $item_a['rsscache:feed_'.$j.'_opts'] = $t[$j]['opts'];
          $item_a['rsscache:feed_'.$j.'_link'] = $t[$j]['link'];
        }
      $item_a['image'] = $item_a['image']['url'];

      // DEBUG
//      echo '<pre><tt>';
//      print_r ($item_a);

      $item[] = $item_a;
    }

  $channel = array ();
  $channel = misc_object2array ($rss);
  unset ($channel['item']);
  $channel['image'] = $channel['image']['url']; 

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($channel);
//  print_r ($item);

//echo generate_rss2 ($channel, $item, 1, 1);
//exit;

  return array ('channel' => $channel, 'item' => $item);
}


function
generate_rss2_func ($item, $a)
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

        $p .= '      <'.$t.'>'
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
    $p .= '<?xml-stylesheet href="'.$xsl_stylesheet.'" type="text/xsl" media="screen"?>'."\n";

  if ($use_cms == 1)
$comment = '<!--
rsscache uses a derivate of RSS for configuration
  channels are web sites
  items are categories

format:
rss                       even config files are made of RSS :)
  channel[]               site
    title                 title
    link                  site link
    description
    image                 optional
      url                 image url
      link                optional, image link
      width               optional, image width
      height              optional, image height
TODO:    rsscache:filter  optional
    item[]                    feed downloads
      title                 category title
      link                  same as rsscache:query
      description
      category              category name
      image                 optional, category logo
        url                 image url
        link                optional, image link
        width               optional, image width
        height              optional, image height
TODO:      rsscache:filter         optional, boolean full-text search query for SQL query using IN BOOLEAN MODE modifier
      rsscache:feed[]
        rsscache:link                   link of feed (RSS, etc.)
                                http://gdata.youtube.com/feeds/api/videos?author=USERNAME&vq=SEARCH&max-results=50
                                http://gdata.youtube.com/feeds/api/videos?vq=SEARCH&max-results=50
        NOTE: use link_prefix, link_suffix and link_search when getting more than one RSS feed from the same place
        rsscache:link_prefix    same as link
        rsscache:link_search[]
        rsscache:link_suffix
        rsscache:opts    
TODO:      rsscache:filter       optional, boolean full-text search query for SQL query using IN BOOLEAN MODE modifier
      rsscache:table_suffix  
TODO:      rsscache:votable          if items of this category can be voted for
                                       0 = not (default)
                                       1 = by everyone
TODO:      rsscache:reportable       if items can be reported to the admins
                                       0 = not (default)
                                       1 = by everyone
TODO:      rsscache:movable          if items can be moved to another category
                                       0 = not (default)
                                       1 = by the admin only
                                       2 = by everyone
                     CMS options, widget.php/widget_cms():

    cms:separate     optional, adds a line-feed or separator before the next category
                            0 == no separator (default)
                            1 == line-feed
                            2 == horizontal line (hr tag)
    cms:button_only  optional, show only button
    cms:status       optional, adds a small status note
                            0 == nothing (default)
                            1 == "New!"
                            2 == "Soon!"
                            3 == "Preview!"
                            4 == "Update!"
    cms:select       add to select menu
    cms:local        optional, local file to embed
    cms:iframe       optional, url to embed
    cms:proxy        optional, url to embed (proxy-style)
    cms:query        optional, url of button or select
                            &q=SEARCH       search

                            *** functions ***
                            &f=all          show all categories (sorted by time of RSS feed download)
                            &f=new          show all categories (sorted by time of RSS item)
                            &f=0_5min       show videos with <5 minutes duration
                            &f=5_10min
                            &f=10_min
                            &f=score        sort by score/votes/popularity
                            &f=4_3          4:3 ratio for videos 
                            &f=16_9         16:9 ratio for videos

                            *** output ***
                            &output=rss     output page as RSS feed
                            &output=mirror  output page as static HTML
                            &output=wall    show search results as wall
                            &output=cloud   same as wall
                            &output=stats   show RSS feed download stats
                            &output=1col    show videos in 1 column
                            &output=2cols   show videos in 2 columns

optional:
rss
  channel[]
    docs
    item[]
      pubDate
      enclosure
      author
      media:duration
      media:keywords
      rsscache:dl_date
      rsscache:pubDate      same as pubDate but as integer
      rsscache:related_id
      rsscache:event_start
      rsscache:event_end
      rsscache:url_crc32
      rsscache:category_items
      rsscache:category_days
-->';

//  $p .= $comment;

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
//           'rsscache:stats_category',
           'rsscache:stats_items',
           'rsscache:stats_days',
           'rsscache:stats_items_today',
           'rsscache:stats_items_7_days',
           'rsscache:stats_items_30_days',
);

   $p .= generate_rss2_func ($channel, $a);

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

  for ($i = 0; isset ($item[$i]['link']); $i++)
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
//      $p .= '    <media:group>'."\n";

//      $p .= '      <media:content url="'.$item[$i]['link'].'" />'."\n";

//      $p .= '      <media:embed>'.''.'</media:embed>'."\n";

//      $p .= '      <media:category scheme="">'..'</media:category>';

      if (isset ($item[$i]['image']))
        $p .= '      <media:thumbnail url="'.$item[$i]['image'].'" />'."\n";

      if (isset ($item[$i]['media:duration']))
        $p .= '      <media:duration>'.$item[$i]['media:duration'].'</media:duration>'."\n";

      if (isset ($item[$i]['media:keywords']))
        $p .= '      <media:keywords>'.misc_xml_escape (str_replace (' ', ', ', $item[$i]['media:keywords'])).'</media:keywords>'."\n";

//      $p .= '      </media:group>'."\n";
        }

      // rsscache
      if ($use_rsscache == 1)
        {
//      $p .= '    <rsscache:group>'."\n";

      if (isset ($item[$i]['pubDate']))
        $p .= '      <rsscache:pubDate>'.sprintf ("%u", $item[$i]['pubDate']).'</rsscache:pubDate>'."\n";

$a = array (
           'rsscache:dl_date',
           'rsscache:related_id',
           'rsscache:event_start',
           'rsscache:event_end',
           'rsscache:url_crc32',
           'rsscache:movable',
           'rsscache:reportable',
           'rsscache:votable',
           'rsscache:stats_category',
//           'rsscache:category_items',
//           'rsscache:category_days',
           'rsscache:stats_items',
           'rsscache:stats_days',
           'rsscache:stats_items_today',
           'rsscache:stats_items_7_days',
           'rsscache:stats_items_30_days',
);

   $p .= generate_rss2_func ($item[$i], $a);
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($channel);
//  print_r ($item);


for ($j = 0; isset ($item[$i]['rsscache:feed_'.$j.'_link']); $j++)
  {
    $p .= '    <rsscache:feed>'."\n";
    if (isset ($item[$i]['rsscache:feed_'.$j.'_client']))
      $p .= '      <rsscache:client>'.misc_xml_escape ($item[$i]['rsscache:feed_'.$j.'_client']).'</rsscache:client>'."\n";
    if (isset ($item[$i]['rsscache:feed_'.$j.'_opts']))
      $p .= '      <rsscache:opts>'.misc_xml_escape ($item[$i]['rsscache:feed_'.$j.'_opts']).'</rsscache:opts>'."\n";
    if (isset ($item[$i]['rsscache:feed_'.$j.'_link']))
      $p .= '      <rsscache:link>'.misc_xml_escape ($item[$i]['rsscache:feed_'.$j.'_link']).'</rsscache:link>'."\n";
    $p .= '    </rsscache:feed>'."\n";
  }

//      $p .= '      </rsscache:group>'."\n";
        }

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
            'cms:query',
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
generate_rss ($title, $link, $desc, $item_title_array, $item_link_array, $item_desc_array,
              $item_media_duration_array = NULL,
              $item_author_array = NULL)
{
  $item = array ();
echo 'generate_rss() is deprecated, use generate_rss2() instead';
  for ($i = 0; isset ($item_link_array[$i]); $i++)
    $item[] = array ('title' => $item_link_array[$i],
                     'link' => $item_link_array[$i],
                     'description' => $item_desc_array[$i],
                     'media:duration' => $item_media_duration_array[$i],
                     'author' => $item_author_array[$i]);


  return generate_rss2 (array ('title' => $title,
                               'link' => $link,
                               'description' => $desc), $item, 1);
}


function
rss_improve_relevance_s ($rss)
{
  $threshold = 66.6; // 2/3 similarity is okay for (e.g.) parted movies, etc.

  $title_a = array ();
  $link_a = array ();
  $desc_a = array ();
//  $media_duration_a = array ();
//  $author_a = array ();

  $title_a[] = $rss->channel->item[0]->title;
  $link_a[] = $rss->channel->item[0]->link;
  $desc_a[] = $rss->channel->item[0]->desc;
//  $media_duration_a[] = $rss->channel->item[0]->media->duration;
//  $author_a[] = $rss->channel->item[0]->author;

  for ($i = 1; isset ($rss->channel->item[$i]); $i++)
    {
      similar_text ($rss->channel->item[0]->title, $rss->channel->item[$i]->title, $percent);
//      if ($percent < $threshold)
//        unset ($rss->channel->item[$i]);
      if ($percent > $threshold)
        {
          $title_a[] = $rss->channel->item[$i]->title;
          $link_a[] = $rss->channel->item[$i]->link;
          $desc_a[] = $rss->channel->item[$i]->description;
//          $media_duration_a[] = $rss->channel->item[$i]->media->duration;
//          $author_a[] = $rss->channel->item[$i]->author;
        }
    }

  // sort by title (useful for evtl. episodes/parts)
  array_multisort ($title_a, SORT_ASC, SORT_STRING, $link_a, $desc_a
//, $media_duration_a, $author_a
);

  $channel = array ('title' => $rss->channel->title,
                    'link' => $rss->channel->link,
                    'description' => $rss->channel->desc);

  $item = array ();
  for ($i = 0; isset ($title_a[$i]); $i++)
    $item[] = array ('title' => $title_a[$i],
                     'link' => $link_a[$i],
                     'description' => $desc_a[$i],
//                     'media:duration' => $media_duration_a[$i],
//                     'author' => $author_a[$i],
);

  // DEBUG
//  print_r ($channel);
//  print_r ($title_a);
//  exit;

  return generate_rss2 ($channel, $item, 0, 0);
}


}

?>