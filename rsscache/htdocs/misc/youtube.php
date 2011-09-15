<?php
/*
youtube.php - miscellaneous functions

Copyright (c) 2009 - 2010 NoisyB


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
if (!defined ('MISC_YOUTUBE_PHP'))
{
define ('MISC_YOUTUBE_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
require_once ('misc.php');
require_once ('rss.php');


function
youtube_get_videoid ($url)
{
  // DEBUG
//  echo $url."\n";

  $p = urldecode ($url);
//  $p = str_replace ('&feature=youtube_gdata', '', $p);

  $a = array ('watch?v=', '?v=', '/v/');
  $start = 0;
  for ($i = 0; isset ($a[$i]); $i++)
    {
      $start = strpos ($p, $a[$i]);
      if ($start)
        {
          $p = substr ($p, $start + strlen ($a[$i]));
          break;
        }
    }

  $len = strpos ($p, '&');
  if ($len)
    $p = substr ($p, 0, $len);

  // sanity
  if (strlen ($p) != 11)
    $p = '';

  // DEBUG
//  echo $p."\n";

  return $p;
}


function
youtube_get_thumbnail_urls ($url)
{
  $video_id = youtube_get_videoid ($url);
  $a = array ();
  for ($i = 0; $i < 4; $i++)
    {
      $a[] = 'http://i.ytimg.com/vi/'.$video_id.'/'.($i + 1).'.jpg';
    }
  // DEBUG
//  print_r ($a);
  return $a;
}


function
misc_youtube_search ($search = NULL, $channel = NULL, $playlist_id = NULL, $orderby = 'relevance', $start = 0, $num = 50)
{
      // OLD: http://gdata.youtube.com/feeds/api/videos?author=USERNAME&vq=SEARCH&max-results=50
//    http://gdata.youtube.com/feeds/base/users/'.$v_user.'/uploads?max-results=50
      // OLD: http://gdata.youtube.com/feeds/api/videos?vq=SEARCH&max-results=50
      // http://gdata.youtube.com/feeds/base/videos?q=SEARCH&orderby=published&alt=rss&client=ytapi-youtube-search&v=2   
// http://www.youtube.com/rss/global/recently_added.rss
// http://www.youtube.com/rss/tag/%s.rss
// &search_sort=video_date_uploaded
// http://gdata.youtube.com/feeds/api/videos?vq=SEARCH&max-results=50&search_sort=video_date_uploaded
// http://gdata.youtube.com/feeds/api/videos?author=USER&vq=SEARCH&max-results=50&search_sort=video_date_uploaded
// http://gdata.youtube.com/feeds/base/videos?q=quakelive&orderby=published&alt=rss&client=ytapi-youtube-search&v=2

  /*
    $orderby
      'relevance'  entries are ordered by their relevance to a search query (default)
      'published'  entries are returned in reverse chronological order
      'viewCount'  entries are ordered from most views to least views
      'rating'     entries are ordered from highest rating to lowest rating
  */
//  $maxresults = 50;
  $maxresults = $num;

  $a = array (
    'alt=rss',
    'client=ytapi-youtube-search',
    'v=2',
    'max-results='.$maxresults,
);

  $p = '';
  if ($playlist_id)
      $p .= 'http://gdata.youtube.com/feeds/base/playlists/'.$playlist_id;
  else
    {
      $p .= 'http://gdata.youtube.com/feeds/base/videos';
      $a[] = 'q='.($search ? urlencode ($search) : '');
      if ($channel)
        $a[] = 'author='.$channel;
      if ($orderby)
        $a[] = 'orderby='.$orderby;
    }

  $p .= '?'.implode ($a, '&');
  // DEBUG
//  echo $p."\n";

  return $p;
} 


function
youtube_get_rss ($search = NULL, $channel = NULL, $playlist_id = NULL, $use_tor = 0, $start = 0, $num = 50)
{
//  $relevance_threshold = 66.6;
  $relevance_threshold = 10.0;
//  $orderby = 'published';
  $orderby = 'relevance';

  $url = misc_youtube_search ($search, $channel, $playlist_id, $orderby, $start, $num);
  $rss = misc_download2 ($url, $use_tor, 1); // is XML
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($rss); 
//  exit;

  // additional relevance threshold
  if ($orderby == 'relevance' && $search)
    if (trim ($search) != '')
    {
      $p = rss_improve_relevance_s ($rss, $relevance_threshold);
      $xml = simplexml_load_string ($p, 'SimpleXMLElement', LIBXML_NOCDATA);
//    echo 'a'.$rss->asXML ().'b';
      // DEBUG
//      echo '<pre><tt>';
//      print_r ($xml); 
//      exit;
      return $xml;
    }

  return $rss;
}


function
youtube_get_download_urls ($video_id, $use_tor = 0, $debug = 0)
{
  // normalize
  $video_id = youtube_get_videoid ($video_id);

  // DEBUG
//  echo $video_id;

  if ($video_id == '')
    return NULL;

  $url = 'http://www.youtube.com/get_video_info?video_id='.$video_id;
  // DEBUG
//  echo $url."\n";

/*
  if (misc_url_exists ($url) === true)
    {
      $h = get_headers ($url);
      // DEBUG
//      print_r ($h);
      return $h[19];
    }
*/

   $page = misc_download2 ($url, $use_tor); // text/plain
  // DEBUG
//  echo $page;

  $a = array ();
  parse_str ($page, $a);

  if ($debug == 1)
    {
      echo '<pre><tt>';
      print_r ($a);
    }

  // DEBUG
//  echo $a['status'];

//  if (!isset ($a['fmt_url_map']))   // changed by yt in august 2011 
//    return NULL;
  if (!isset ($a['url_encoded_fmt_stream_map']))
    return NULL;

//  if (isset ($a['fmt_list']))
//    {
//  [fmt_list] => 35/854x480/9/0/115,34/640x360/9/0/115,18/640x360/9/0/115,5/320x240/7/0/0
//      $t = explode (',', $a['fmt_list']);
//      for ($i = 0; isset ($t[$i]); $i++)
//        $b['fmt_list_'.$i] = $t[$i];
/*
Standard (fmt=0 ?) > MP3, ~64 kbps, 22.05 KHz, mono (1 channel)
fmt=5 > MP3, ~64 kbps, 22.05 KHz, mono (1 channel) (little difference in video bitrate)
fmt=6 > MP3, ~66 kbps, 44.1 KHz, mono (1 channel)
fmt=18 > AAC, ~126 kbps, 44.1 KHz, stereo (2 channels)
fmt=22 > AAC, ~248 kbps, 44.1 KHz, stereo (2 channels) (it's rare, only if uploaded video have 720p)
fmt=34 > AAC, ~68 kbps, 22.05 KHz, stereo (2 channels)
fmt=35 > AAC, ~112 kbps, 44.1 KHz, stereo (2 channels) (it's rare)
fmt=13 and fmt=17 > only on mobile devices (3GP with AMR or AAC audio)
*/
//      $a = array_merge ($a, $b);
//    }

  $b = explode (',', $a['url_encoded_fmt_stream_map']);
  for ($i = 0; isset ($b[$i]); $i++)
    $b[$i] = substr ($b[$i], 4);
  $a = array_merge ($a, $b);

  if ($debug == 1)
    {
      echo '<pre><tt>';
      print_r ($a);
    }

  // normalize
  for ($j = 0; isset ($a[$j]); $j++)
    {
      $p = urldecode ($a[$j]);
      $p = misc_substr2 ($p, 'url=', '; ', NULL);
      // DEBUG
      echo $p."\n";
//      $a[$j] = $p;
    }

//  if ($debug == 1)
    {
//      echo '<pre><tt>';
//      print_r ($a);
exit;
    }

  return $a;
}


/*
function
youtube_download ($video_id, $use_tor = 0, $debug = 0)
{
  $a = array ();

  if (!strstr ($video_id, 'http://'))
    return youtube_get_download_urls ($video_id, $use_tor, $debug);
    
  // RSS feed
   $b = misc_download2 ($url, $use_tor, 1); // is XML

      if ($debug == 1)
        {
          echo '<pre><tt>';
          print_r ($b);
        }

      for ($i = 0; isset ($b->channel->item[$i]); $i++)
        {
          $c = youtube_get_download_urls ($b->channel->item[$i]->link, $use_tor, $debug);
          if ($c)
            $a[] = $c;
        }

  return $a;
}
*/


function
youtube_check_dead_link ($url, $use_tor)
{
  $s = youtube_get_videoid ($url);

  if (!strlen($s))
    return;

  $a = youtube_download ($s, $use_tor, 0);

  if (!($a[0]))
    return;

  // DEBUG
//  echo $a[0]['status']."\n";

  if ($a[0]['status'] == 'ok') // is alive
    return 0;
  return 1; // is dead
}


}


?>