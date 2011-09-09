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
//require_once ('rss.php');


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
youtube_get_rss ($search, $channel = NULL, $playlist = NULL, $orderby = 'relevance', $use_tor = 0, $start = 0, $num = 50)
{
  /*
    $orderby
      'relevance'  entries are ordered by their relevance to a search query (default)
      'published'  entries are returned in reverse chronological order
      'viewCount'  entries are ordered from most views to least views
      'rating'     entries are ordered from highest rating to lowest rating
  */
//  $maxresults = 50;
  $maxresults = $num;
  $q = urlencode ($search);

  if ($playlist)
    {
      $url = 'http://gdata.youtube.com/feeds/base/playlists/'.$playlist;
      $url .= '?alt=rss&client=ytapi-youtube-search&v=2&max-results='.$maxresults;
    }
  else if ($channel)
    {
      // OLD: http://gdata.youtube.com/feeds/api/videos?author=USERNAME&vq=SEARCH&max-results=50
//    http://gdata.youtube.com/feeds/base/users/'.$v_user.'/uploads?max-results=50
      $url = 'http://gdata.youtube.com/feeds/base/videos?author='.$channel.'&q='.$q;
      $url .= '&orderby='.$orderby;
      $url .= '&alt=rss&client=ytapi-youtube-search&v=2&max-results='.$maxresults;
    }
  else
    {
      // OLD: http://gdata.youtube.com/feeds/api/videos?vq=SEARCH&max-results=50
      // http://gdata.youtube.com/feeds/base/videos?q=SEARCH&orderby=published&alt=rss&client=ytapi-youtube-search&v=2   
      $url = 'http://gdata.youtube.com/feeds/base/videos?q='.$q;
      $url .= '&orderby='.$orderby;
      $url .= '&alt=rss&client=ytapi-youtube-search&v=2&max-results='.$maxresults;
    }

  // DEBUG
//  echo $url."\n";

  if ($use_tor)
    $f = tor_get_contents ($url);
  else
    $f = file_get_contents ($url);

  $rss = simplexml_load_string ($f, 'SimpleXMLElement', LIBXML_NOCDATA);

  // DEBUG
//echo '<pre><tt>';
//print_r ($rss);

  return $rss;
} 


function
youtube_get_rss2 ($q, $user = NULL, $playlist_id = NULL, $use_tor = 0, $start = 0, $num = 50)
{
//$relevance_threshold = 66.6;
$relevance_threshold = 10.0;

  //  $orderby
  //    'relevance'  entries are ordered by their relevance to a search query (default)
  //    'published'  entries are returned in reverse chronological order
  //    'viewCount'  entries are ordered from most views to least views
  //    'rating'     entries are ordered from highest rating to lowest rating
//  $orderby = 'published';
  $orderby = 'relevance';

  if ($user)
    $rss = youtube_get_rss (NULL, trim ($user), NULL, $orderby, $use_tor, $start, $num);
  else if ($playlist_id)
    $rss = youtube_get_rss ('', NULL, trim ($playlist_id), $orderby, $use_tor, $start, $num);
  else // if ($q)
    $rss = youtube_get_rss ($q, $user ? trim ($user) : NULL, NULL, $orderby, $use_tor, $start, $num);

  // DEBUG
//  echo '<pre><tt>';
//print_r ($rss); 
//exit;
// normalize: remove items with low relevance
$xml = $rss;
if ($orderby == 'relevance' && $q)
  if (trim ($q) != '')
    {
      $p = rss_improve_relevance_s ($rss, $relevance_threshold);
      $xml = simplexml_load_string ($p, 'SimpleXMLElement', LIBXML_NOCDATA);
//    echo 'a'.$rss->asXML ().'b';
    }
  // DEBUG
//  echo '<pre><tt>';
//print_r ($xml); 
//exit;
  return $xml;
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
youtube_download_single ($video_id, $use_tor = 0, $debug = 0)
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

  if ($use_tor)
    $page = tor_get_contents ($url);
  else
    $page = file_get_contents ($url);
  // DEBUG
//  echo $page;

  $a = array ();
  parse_str ($page, $a);

  if ($debug == 1)
    {
      echo '<pre><tt>';
      print_r ($a);
      echo '</tt></pre>';
    }

//  if (!isset ($a['fmt_url_map']))
//    return NULL;
//  $b = explode (',', $a['fmt_url_map']);
  // changed by yt in august 2011
  if (!isset ($a['url_encoded_fmt_stream_map']))
    return NULL;
  $b = explode (',', $a['url_encoded_fmt_stream_map']);
  for ($i = 0; isset ($b[$i]); $i++)
    $b[$i] = substr ($b[$i], 4);

  if ($debug == 1)
    {
      echo '<pre><tt>';
      print_r ($b);
      echo '</tt></pre>';
    }

  $a = array_merge ($a, $b);

  $url = urldecode ($b[0]); // high quality
//  $url = urldecode ($b[max (0, count ($b) - 1)]); // low quality
  $url = substr ($url, strrpos ($url, 'http://'));
  $a['video_url'] = $url;

    // TODO: youtube_download_single_normalize ()
    for ($j = 0; isset ($a[$j]); $j++)
      {
    $p = urldecode ($a[$j]);
    $o = strpos ($p, 'url=');
    if ($o)
      $p = substr ($p, $o + 4);
    $o = strpos ($p, '; ');
    if ($o)
      $p = substr ($p, 0, $o);
    $p = trim ($p);
    $a[$j] = $p;
      }

  return $a;
}


function
youtube_download ($video_id, $use_tor = 0, $debug = 0)
{
  $a = array ();

  if (strstr ($video_id, '?v='))
    $a[0] = youtube_download_single ($video_id, $debug);
  else if (strstr ($video_id, 'http://')) // RSS feed
    {
      if ($use_tor)
        {
          $xml = tor_get_contents ($video_id);
          $b = simplexml_load_string ($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
      else
        $b = simplexml_load_file ($video_id);

      if ($debug == 1)
        {
          echo '<pre><tt>';
          print_r ($b);
          echo '</tt></pre>';
        }

      for ($i = 0; isset ($b->channel->item[$i]); $i++)
        {
          $c = youtube_download_single ($b->channel->item[$i]->link, $use_tor, $debug);
          if ($c)
            $a[] = $c;
        }
    }
  else
    $a[0] = youtube_download_single ($video_id, $use_tor, $debug);

  return $a;
}


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