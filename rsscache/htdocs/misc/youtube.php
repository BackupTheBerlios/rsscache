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
youtube_get_rss_orderby_relevance ($rss)
{
  $title_a = array ();
  $link_a = array ();
  $desc_a = array ();
//  $media_duration_a = array ();
//  $author_a = array ();

  $title_a[] = $rss->channel->item[0]->title;
  $link_a[] = $rss->channel->item[0]->link;
  $desc_a[] = $rss->channel->item[0]->desc;
//  $media_duration_a[] = $rss->channel->item[0]->media_duration;
//  $author_a[] = $rss->channel->item[0]->author;

  for ($i = 1; isset ($rss->channel->item[$i]); $i++)
    {
      similar_text ($rss->channel->item[0]->title, $rss->channel->item[$i]->title, $percent);
//      if ($percent < 66.6)
//        unset ($rss->channel->item[$i]);
      if ($percent > 66.6)
        {
          $title_a[] = $rss->channel->item[$i]->title;
          $link_a[] = $rss->channel->item[$i]->link;
          $desc_a[] = $rss->channel->item[$i]->desc;
//          $media_duration_a[] = $rss->channel->item[$i]->media_duration;
//          $author_a[] = $rss->channel->item[$i]->author;
        }
    }

  // sort by title (useful for evtl. episodes/parts)
  array_multisort ($title_a, SORT_ASC, SORT_STRING, $link_a, $desc_a
//, $media_duration_a, $author_a
);

//function
//generate_rss ($title, $link, $desc, $item_title_array, $item_link_array, $item_desc_array,
//              $item_media_duration_array = NULL,
//              $item_author_array = NULL)
  $p = generate_rss ($rss->title, $rss->link, $rss->desc, $title_a, $link_a, $desc_a
//, $media_duration_a, $author_a
);

  $rss = simplexml_load_string ($p);
  return $rss;
}


function
youtube_get_rss ($search, $channel = NULL, $playlist = NULL, $orderby = 'relevance', $use_tor = 0)
{
  /*
    $orderby
      'relevance'  entries are ordered by their relevance to a search query (default)
      'published'  entries are returned in reverse chronological order
      'viewCount'  entries are ordered from most views to least views
      'rating'     entries are ordered from highest rating to lowest rating
  */
  $maxresults = 50;
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

  $rss = simplexml_load_string ($f);

  // normalize: remove items with low relevance
  if ($orderby == 'relevance' && $search)
    if (trim ($search) != '')
    $rss = youtube_get_rss_orderby_relevance ($rss);

  // DEBUG
//echo '<pre><tt>';
//print_r ($rss);

  return $rss;
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
          $b = simplexml_load_string ($xml);
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