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
youtube_get_format ($f)
{
// 2011/09/14 source: http://en.wikipedia.org/wiki/Youtube#Quality_and_codecs
// container, video codec, width, height, min. mbit/s, max mbit/s, audio codec, channels, Hz, kbit/s
$format = array (
  array (45, '.webm',                'VP8', 1280,  720,  2.0,  2.0, 'vorbis',      2, 44100, 192),
  array (44, '.webm',                'VP8',  854,  480,  1.0,  1.0, 'vorbis',      2, 44100, 128),
  array (43, '.webm',                'VP8',  640,  360,  0.5,  0.5, 'vorbis',      2, 44100, 128),

  array (38,  '.mp4', 'MPEG-4 AVC (H.264)', 4096, 3072,  0.0,  0.0,    'aac',      2, 44100, 152),
  array (37,  '.mp4', 'MPEG-4 AVC (H.264)', 1920, 1080,  3.5,  5.0,    'aac',      2, 44100, 152),
  array (22,  '.mp4', 'MPEG-4 AVC (H.264)', 1280,  720,  2.0,  2.9,    'aac',      2, 44100, 152),
  array (18,  '.mp4', 'MPEG-4 AVC (H.264)',  640,  360,  0.5,  0.5,    'aac',      2, 44100,  96),

  array (35,  '.flv', 'MPEG-4 AVC (H.264)',  854,  480,  0.8,  1.0,    'aac',      2, 44100, 128),
  array (34,  '.flv', 'MPEG-4 AVC (H.264)',  640,  360,  0.5,  0.5,    'aac',      2, 44100, 128),
//array ( 6,      '',                   '',    0,    0,  0.0,  0.0,       '',      0,     0,   0),
  array ( 5,  '.flv',     'Sorenson H.263',  400,  240, 0.25, 0.25,    'mp3', 1/*2*/, 22050,  64),
//array ( 0,  '.flv',                   '',    0,    0,  0.0,  0.0,       '',      0,     0,   0),

//array (13,  '.3gp',                   '',    0,    0,  0.0,  0.0,       '',      0,     0,   0),
  array (17,  '.3gp',      'MPEG-4 Visual',  176,  144,  0.0,  0.0,    'aac',      2, 44100,   0),
);
  for ($i = 0; isset ($format[$i]); $i++)
    if ($format[$i][0] == $f)
      return $format[$i];
    else
      {
        if (is_string ($f))
          if (strstr ($format[$i][1], $f))
            return $format[$i];
      }
  return youtube_get_format (5); // default
}

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
youtube_get_status ($url, $use_tor)
{
  $id = youtube_get_videoid ($url);
  if ($id != '')
    {
      $a = youtube_get_download_urls ($id, $use_tor, 0);
      if (isset ($a))
        {
          // DEBUG
//          echo $a['status']."\n";
          return $a['status'];
        }
    }

  return NULL;
}


function
youtube_check_dead_link ($url, $use_tor)
{
  $s = youtube_get_status ($url, $use_tor);
  // DEBUG
//  echo $s."\n";
  if ($s)
    if ($s == 'ok') // is alive
      return 0;
  return 1; // is dead
}


function
youtube_get_thumbnail_urls ($url)
{
  $video_id = youtube_get_videoid ($url);
  $a = array ();
  for ($i = 0; $i < 4; $i++)
    $a[] = 'http://i.ytimg.com/vi/'.$video_id.'/'.($i + 1).'.jpg';
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

  $b = explode (',', $a['url_encoded_fmt_stream_map']);
//  for ($i = 0; isset ($b[$i]); $i++)
//    $b[$i] = substr ($b[$i], 4);
  $a = array_merge ($a, $b);

  if ($debug == 1)
    {
      echo '<pre><tt>';
      print_r ($a);
exit;
    }

  // normalize
  for ($j = 0; isset ($a[$j]); $j++)
    {
      $p = urldecode ($a[$j]);
      // DEBUG
//      echo $p."\n";
      $p = misc_substr2 ($p, 'url=', NULL, '; ');
      // DEBUG
//      echo $p."\n";
      $a[$j] = $p;
    }

//  if ($debug == 1)
    {
//      echo '<pre><tt>';
//      print_r ($a);
//exit;
    }

  $b = array ();
  for ($j = 0; isset ($a[$j]); $j++)
    $b[] = $a[$j];

  return $b;
}


}


?>