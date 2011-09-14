<?php
/*
json.php - miscellaneous JSON functions

Copyright (c) 2011 NoisyB


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
if (!defined ('MISC_JSON_PHP'))
{
define ('MISC_JSON_PHP', 1);


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

  if ($use_cms == 1)
$comment = '<!--
rsscache uses RSS 2.0 specification with extensions (rsscache and cms) for configuration

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
      enclosure             optional, category logo/image
        url                 image url
        length              
        type                
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
      media:thumbnail
      rsscache:dl_date
      rsscache:pubDate      same as pubDate but as integer
      rsscache:related_id
      rsscache:event_start
      rsscache:event_end
      rsscache:url_crc32
      rsscache:stats_category
      rsscache:stats_items
      rsscache:stats_days
      rsscache:stats_items_today
      rsscache:stats_items_7_days
      rsscache:stats_items_30_days
      cms:demux
-->';

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
$a = array (
           'media:thumbnail',
           'media:duration',
           'media:keywords',
           'media:embed',
);
   $p .= generate_json_func ($item[$i], $a);
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