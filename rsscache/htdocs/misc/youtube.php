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


function
youtube_get_rss ($q)
{
  $q = urlencode ($q);
//Example: http://gdata.youtube.com/feeds/api/videos?author=USERNAME&vq=SEARCH&max-results=50
//         http://gdata.youtube.com/feeds/api/videos?vq=SEARCH&max-results=50
  $f = file_get_contents ('http://gdata.youtube.com/feeds/api/videos?vq='.$q.'&max-results=5');
  $xml = simplexml_load_string ($f);
// DEBUG
//echo '<pre><tt>';
//print_r ($xml);  
  return $xml;
} 


function
youtube_get_videoid ($url)
{
  // DEBUG
//  echo $url."\n";

//  if (strstr ($video_url, '?v='))
//    $video_url = substr ($video_url, strpos ($video_url, '?v=') + 3);
//  else
//    $video_url = substr ($video_url, strpos ($video_url, 'watch') + 12);
//  $video_url = str_replace ('&feature=youtube_gdata', '', $video_url);

//      $start = strpos ($t, 'watch?v=') + 8;
//      $t = substr ($t, $start);  
//      $len = strpos ($t, '&');
//      if ($len)  
//        $len = substr ($t, 0, $len);

  $p = urldecode ($url);   
  $start = strpos ($p, 'watch?v=');
  if ($start)   
    $start += 8;
  $p = substr ($p, $start);
  if (strpos ($p, '&'))
    {
      $len = strpos ($p, '&');  
      $s = substr ($p, 0, $len);
    }
  else $s = $p;

  // DEBUG
//  echo $s."\n";

  return $s;
}


function
youtube_download_single ($video_id, $use_tor = 0, $debug = 0)
{
  // normalize
  if (strpos ($video_id, '?v='))
    $video_id = substr ($video_id, strpos ($video_id, '?v=') + 3);
  if (strpos ($video_id, '&'))
    $video_id = substr ($video_id, 0, strpos ($video_id, '&'));

  // DEBUG
//  echo $video_id;

  $url = 'http://www.youtube.com/get_video_info?&video_id='.$video_id;

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
  $a = array ();
  parse_str ($page, $a);

  if ($debug == 1)
    {
      echo '<pre><tt>';
      print_r ($a);
      echo '</tt></pre>';
    }

  if (!isset ($a['fmt_url_map']))
    return NULL;

  $b = explode (',', $a['fmt_url_map']);

  if ($debug == 1)
    {
      echo '<pre><tt>';
      print_r ($b);
      echo '</tt></pre>';
    }

  $a = array_merge ($a, $b);

  $url = urldecode ($b[0]);
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
youtube_thumbnail ($url)
{
  global $tv2_root;
  global $thumbnails_path;

  $s = youtube_get_videoid ($url);

  if (!strlen ($s))
    return;

  for ($i = 1; $i < 4; $i++)
    {
      // download thumbnail
      $url = 'http://i.ytimg.com/vi/'.$s.'/'.$i.'.jpg';

      $filename = $s.'_'.$i.'.jpg';
      $path = $tv2_root.'/thumbnails/youtube/'.$filename;

      // DEBUG
//      echo $url."\n";

      if (file_exists ($path)) // do not overwrite existing files
        {
          echo 'WARNING: file '.$path.' exists, skipping'."\n";
          return -1;
        }
      else echo $path."\n";

      misc_download ($url, $path);
//      echo 'wget -nc "'.$url.'" -O "'.$filename.'"'."\n"; // -N?
//      echo 'rm "'.$filename.'"'."\n"; // remove old thumbs
    }
  return 0;
}


function
youtube_check_dead_link ($url, $use_tor)
{
  global $tv2_root;
  global $thumbnails_path;
  global $db;

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