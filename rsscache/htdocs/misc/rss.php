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

      if (isset ($a['image']['url']))
        $a['image'] = $a['image']['url'];

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

//  if ($use_cms == 1)
//    $p .= '<!--'."\n".rss_default_channel_description ()."\n".'-->'."\n";

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
//      $p .= '    <media:group>'."\n";

//      $p .= '      <media:content url="'.$item[$i]['link'].'" />'."\n";

//      $p .= '      <media:embed>'.''.'</media:embed>'."\n";

//      $p .= '      <media:category scheme="">'..'</media:category>';

      if (isset ($item[$i]['media:thumbnail']))
        $p .= '      <media:thumbnail url="'.$item[$i]['media:thumbnail'].'" media:url="'.$item[$i]['media:thumbnail'].'" />'."\n";

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
           'rsscache:table_suffix',
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
            'cms:query',
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


function
rss_default_channel_description ()
{
  $p = 'RSScache uses RSS 2.0 specification with new namespaces (rsscache and cms) for configuration'."\n"
      .''."\n"
      .'format:'."\n"
      .'rss                       even config files are made of RSS :)'."\n"
      .'  channel[]               site'."\n"
      .'    title                 title'."\n"
      .'    link                  site link'."\n"
      .'    description'."\n"
      .'    image                 optional'."\n"
      .'      url                 image url'."\n"
      .'      link                optional, image link'."\n"
      .'TODO:      width               optional, image width'."\n"
      .'TODO:      height              optional, image height'."\n"
      .'TODO:    rsscache:filter  optional'."\n"
      .'    item[]                    feed downloads'."\n"
      .'      title                 category title'."\n"
      .'      link                  optional, url of button or select'."\n"
      .'                              &q=SEARCH       search'."\n"
      .'                              *** functions ***'."\n"
      .'                              &f=all          show all categories (sorted by time of RSS feed download)'."\n"
      .'                              &f=new          show all categories (sorted by time of RSS item)'."\n"
      .'                              &f=0_5min       show media with <5 minutes duration'."\n"
      .'                              &f=5_10min'."\n"
      .'                              &f=10_30min'."\n"
      .'                              &f=30_60min'."\n"
      .'                              &f=60min'."\n"
      .'                              &f=related'."\n"
      .'                              &f=stats'."\n"
      .'                              &f=author'."\n"
      .'                              &f=sitemap'."\n"
      .'                              &f=robots'."\n"
      .'                              &f=cache'."\n"
      .'                              &f=config'."\n"
      .'                              &f=4_3          4:3 ratio for videos (CMS only)'."\n"
      .'                              &f=16_9         16:9 ratio for videos (CMS only)'."\n"
      .'TODO:                              &f=score        sort by score/votes/popularity'."\n"
      .'                              *** output ***'."\n"
      .'                              &output=rss     output page as RSS feed'."\n"
      .'                              &output=mirror  output page as static HTML'."\n"
      .'                              &output=wall    show search results as wall'."\n"
      .'                              &output=cloud   same as wall'."\n"
      .'                              &output=stats   show RSS feed download stats'."\n"
      .'                              &output=1col    show videos in 1 column'."\n"
      .'                              &output=2cols   show videos in 2 columns'."\n"
      .'      description'."\n"
      .'      category              category name'."\n"
      .'      enclosure             optional, category logo/image'."\n"
      .'        url                 image url'."\n"
      .'        length              '."\n"
      .'        type                '."\n"
      .'TODO:      rsscache:filter         optional, boolean full-text search query for SQL query using IN BOOLEAN MODE modifier'."\n"
      .'      rsscache:feed[]'."\n"
      .'        rsscache:link                   link of feed (RSS, etc.)'."\n"
      .'                                http://gdata.youtube.com/feeds/api/videos?author=USERNAME&vq=SEARCH&max-results=50'."\n"
      .'                                http://gdata.youtube.com/feeds/api/videos?vq=SEARCH&max-results=50'."\n"
      .'        NOTE: use link_prefix, link_suffix and link_search when getting more than one RSS feed from the same place'."\n"
      .'        rsscache:link_prefix    same as link'."\n"
      .'        rsscache:link_search[]'."\n"
      .'        rsscache:link_suffix'."\n"
      .'        rsscache:opts    '."\n"
      .'TODO:      rsscache:filter       optional, boolean full-text search query for SQL query using IN BOOLEAN MODE modifier'."\n"
      .'      rsscache:table_suffix  '."\n"
      .'TODO:      rsscache:votable          if items of this category can be voted for'."\n"
      .'                                       0 = not (default)'."\n"
      .'                                       1 = by everyone'."\n"
      .'TODO:      rsscache:reportable       if items can be reported to the admins'."\n"
      .'                                       0 = not (default)'."\n"
      .'                                       1 = by everyone'."\n"
      .'TODO:      rsscache:movable          if items can be moved to another category'."\n"
      .'                                       0 = not (default)'."\n"
      .'                                       1 = by the admin only'."\n"
      .'                                       2 = by everyone'."\n"
      ."\n"
      .'CMS options, widget.php/widget_cms():'."\n"
      .'    cms:separate     optional, adds a line-feed or separator before the next category'."\n"
      .'                            0 == no separator (default)'."\n"
      .'                            1 == line-feed'."\n"
      .'                            2 == horizontal line (hr tag)'."\n"
      .'    cms:button_only  optional, show only button'."\n"
      .'    cms:status       optional, adds a small status note'."\n"
      .'                            0 == nothing (default)'."\n"
      .'                            1 == "New!"'."\n"
      .'                            2 == "Soon!"'."\n"
      .'                            3 == "Preview!"'."\n"
      .'                            4 == "Update!"'."\n"
      .'    cms:select       add to select menu'."\n"
      .'    cms:local        optional, local file to embed'."\n"
      .'    cms:iframe       optional, url to embed'."\n"
      .'    cms:proxy        optional, url to embed (proxy-style)'."\n"
      ."\n"
      .'optional:'."\n"
      .'rss'."\n"
      .'  channel[]'."\n"
      .'    docs'."\n"
      .'    item[]'."\n"
      .'      pubDate'."\n"
      .'      author'."\n"
      .'      media:duration'."\n"
      .'      media:keywords'."\n"
      .'      media:thumbnail'."\n"
      .'      rsscache:dl_date'."\n"
      .'      rsscache:pubDate      same as pubDate but as integer'."\n"
      .'      rsscache:related_id'."\n"
      .'      rsscache:event_start'."\n"
      .'      rsscache:event_end'."\n"
      .'      rsscache:url_crc32'."\n"
      .'      rsscache:stats_category'."\n"
      .'      rsscache:stats_items'."\n"
      .'      rsscache:stats_days'."\n"
      .'      rsscache:stats_items_today'."\n"
      .'      rsscache:stats_items_7_days'."\n"
      .'      rsscache:stats_items_30_days'."\n"
      .'      cms:demux'."\n"
      ."\n"
      .'*** queries ***'."\n"
      .'&q=SEARCH     SEARCH query'."\n"
      .'&start=N      start from result N'."\n"
      .'&num=N        show N results (default: '.$rsscache_results.')'."\n"
      .'&c=NAME       category (leave empty for all categories)'."\n"
      .'&item=URL_CRC32   show single item'."\n"
      .'&f=FUNC       execute FUNCtion'."\n"
      .'&output=FORMAT   output in "rss", "mediawiki", "json", "playlist" (admin) or "html" (default: rss)'."\n"
//      .'&prefix=SUBDOMAIN   prefix or SUBDOMAIN (leave empty for current subdomain)'."\n"
      ."\n"           
      .'*** functions ***'."\n"
      .'&f=author     find user/author/channel (requires &q=SEARCH)'."\n"
      .'&<a href="?f=0_5min&output=html">f=0_5min</a>     media with duration 0-5 minutes'."\n"
      .'&<a href="?f=5_10min&output=html">f=5_10min</a>    media with duration 5-10 minutes'."\n"
      .'&<a href="?f=10_30min&output=html">f=10_30min</a>   media with duration 10-30 minutes'."\n"
      .'&<a href="?f=30_60min&output=html">f=30_60min</a>   media with duration 30-60 minutes'."\n"
      .'&<a href="?f=60min&output=html">f=60_min</a>     media with duration 60+ minutes'."\n"
      .'&<a href="?f=new&output=html">f=new</a>        show only newly created items (default: download time)'."\n"
      .'&f=related    find related items (requires &q=RELATED_ID)'."\n"
      .'&<a href="?f=stats&output=html">f=stats</a>      statistics'."\n"
//      .'&f=error404      '."\n"
//      .'&f=error304      '."\n"
//      .'&f=error300      '."\n"
      ."\n"
      .'*** admin functions ***'."\n"
      .'&<a href="?f=sitemap">f=sitemap</a>    sitemap.xml'."\n"  
      .'&<a href="?f=robots">f=robots</a>    robots.txt'."\n"
      ."\n"
      .'requires access to <a href="admin.php?output=html">admin.php</a>:'."\n"
      .'&<a href="?f=cache&output=html">f=cache</a>      cache (new) items into database (requires &c=CATEGORY)'."\n"
      .'&<a href="?f=config&output=html">f=config</a>    indent and dump config.xml'."\n"
      .'&<a href="?output=playlist">output=playlist</a>    generate playlist.txt'."\n"
      ."\n"
      .'*** install ***'."\n"
      .'see apache2/sites-enabled/rsscache'."\n"
;
  return str_replace (array ('&', "\n", ' '), array ('&amp;', '<br>', '&nbsp;'), $p);
}


}

?>