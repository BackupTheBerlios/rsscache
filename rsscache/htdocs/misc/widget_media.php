<?php
/*
widget_media.php - new HTML widgets for media

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
if (!defined ('MISC_WIDGET_MEDIA_PHP'))
{
define ('MISC_WIDGET_MEDIA_PHP', 1);  
//error_reporting(E_ALL | E_STRICT);
include_once ('misc/misc.php');
include_once ('misc/widget.php');
include_once ('misc/youtube.php');


function
widget_media_object_func ($object, $param, $embed)
{
  $p = '';

  if ($object)
    {
      $p .= '<object';
      for ($i = 0; isset ($object[$i]); $i++)
        $p .= ' '.$object[$i][0].'="'.$object[$i][1].'"';
      $p .= '>';
    }

  if ($param)
  for ($i = 0; isset ($param[$i]); $i++)
    $p .= '<param name="'
         .$param[$i][0].'" value="'.$param[$i][1].'"'
         .'></param>';

  if ($embed)
    {
      $p .= '<embed';
      for ($i = 0; isset ($embed[$i]); $i++)
        $p .= ' '.$embed[$i][0].'="'.$embed[$i][1].'"';
      $p .= '></embed>';
    }

  if ($object)
    $p .= '<object>';

  return $p;
}


function
widget_video_html5 ($video_url, $width = 400, $height = 300, $preview_image = NULL)
{
  $p = '';
  $p .= '<video src="'.$video_url.'" controls="controls" width="'.$width.'" height="'.$height.'"></video>';
  return $p;
}


function
widget_audio_html5 ($audio_url, $start = 0, $stream = 0, $next_stream = NULL)
{
  $p = '';
  $p .= '<audio src="'.$audio_url.'" controls="controls"></audio>';
  return $p;
}


function
widget_audio_youtube ()
{
/*
<object height="344" width="425">
<param name="movie" value="http://www.youtube.com/v/6X-RUpNaiL8&amp;hl=en_US&amp;fs=1&amp;">
<param name="allowFullScreen" value="true">
<param name="allowscriptaccess" value="always">
<embed src="http://www.youtube.com/v/6X-RUpNaiL8&amp;hl=en_US&amp;fs=1&amp;" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true"
 height="25" width="425">
</object>
*/
}


function
widget_video_youtube_array ($video_id_array, $width = 425, $height = 344, $autoplay = 1)
{
  $p = '';

  $p .= '<script src="http://www.google.com/jsapi"></script>'

.'<script>
google.load ("swfobject", "2.1");
</script>'

.'<script type="text/javascript">


function play ()
{
  ytplayer = document.getElementById ("widget_video_playall2");

  if (ytplayer.getPlayerState () == 1)
    return;

  if (typeof this.pos == \'undefined\')
    this.pos = 0;

  a = new Array (';

  for ($i = 0; isset ($video_id_array[$i]); $i++)
    $p .= ($i > 0 ? ",\n" : '').'"'.$video_id_array[$i].'"';

  $p .= '  );

  if (this.pos == a.length)
    this.pos = 0;

  ytplayer.loadVideoById (a[this.pos++], 0);
  ytplayer.playVideo ();
}


function playall ()
{
//  ytplayer = document.getElementById ("widget_video_playall2");

//  if (ytplayer) 
    setInterval (play, 2000);
}


var params = { allowScriptAccess: "always" };
var atts = { id: "widget_video_playall2" };
swfobject.embedSWF("http://www.youtube.com/apiplayer?enablejsapi=1&playerapiid=ytplayer&autoplay=1", 
                   "widget_video_playall", '.$width.', '.$height.', "8", null, null, params, atts);
//swfobject.embedSWF("http://www.youtube.com/v/590LV50yUN4?enablejsapi=1&playerapiid=ytplayer&autoplay=1&showsearch=0&rel=0",
//                   "widget_video_playall", '.$width.', '.$height.', "8", null, null, params, atts);


</script>
<div id="widget_video_playall"></div><br>
<a href="javascript:void(0);" onclick="javascript:playall()">Play</a>';

  return $p;
}


function
widget_video_youtube ($video_id, $width = 425, $height = 344, $autoplay = 1, $hq = 1, $loop = 0)
{
  $tor_enabled = 0;

  $url = 'http://www.youtube.com/v/'
        .$video_id
       .'&fs=1'             // allow fullscreen
//       .'&ap=%2526fmt%3D'.($hq ? 18 : 5) // high(er) quality?
       .'&showsearch=0'     // no search
       .'&rel=0'            // no related
       .($autoplay ? '&autoplay=1' : '')
//       .($loop ? '&loop=1' : '')
//       .'&color1=0x000000'
//       .'&color2=0x000000'
//       .'#t=03m22s'         // skip to
//       .'&start=30'         // skip to (2)
;

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', $url),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
    array ('autoplay', $autoplay ? 'true' : 'false'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('autoplay', $autoplay ? 'true' : 'false'),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
  );
 
  $s = '';

  $s .= widget_media_object_func ($o, $p, $e);

  $a = youtube_download ($video_id, $tor_enabled, 0);

  // DEBUG
//  echo '<pre><tt>';
//  echo $video_id."\n";
//  print_r ($a); 

  $j = 0;

  if ($a[$j]['status'] == 'fail') // youtube fail
    {
      $s .= $a[$j]['errorcode'].': '.$a[$j]['reason'];
 
      switch ($a[$j]['errorcode'])
        {
          case 150: // copyright
            $s .= '<br>'
                 .' Probably Naziwalled against access from your country<br>'
                 .'Try a proxy or service that is located in the country of the possible license owner'
;
            break;

          case 100: // removed by user
          default:
            break;
        }
    }
  else
    {
      $s .= '<br>';

      // download
      $s .= '<a href="'.$a[$j]['video_url'].'">Best</a>';

      for ($q = 0; isset ($a[$j][$q]); $q++)
        {
          $fmt = substr ($a[$j][$q], 0, strpos ($a[$j][$q], '|'));
          $t = substr ($a[$j][$q], strpos ($a[$j][$q], '|') + 1);
          $s .= ' <a href="'.$t.'">&fmt='.$fmt.'</a>';
        }

      // direct link
//      $s .= ' <a href="'.$a[$j]['ad_eurl'].'">Direct</a>';

      $s .= '<br>';

      $s .= ''
//           .'<div style="width:'.($width - 10).'px;">'
           .'<input type="text"'
           .' style="width:'.($width - 10).'px;"'
           .' value="'
           .$a[$j]['title']
           .'" readonly="readonly"'
//           .' onclick="javascript:this.execCommand(\'copy\');"'
           .'>'
//           .'</div>'
;

      $s .= '<br>';
      $s .= widget_collapse ('Details', '<pre><tt>'.sprint_r ($a[$j]).'</tt></pre>', 1);
    }

  return $s;
}



function
widget_video_dailymotion ($video_id, $width=420, $height=336)
{
//  $video_id = 'k4H0eU9uhV7waa1XXp';
  $url = 'http://www.dailymotion.com/swf/'.$video_id.'&related=1';

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', $url),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
  );

  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_xvideos ($video_id, $width=510, $height=400)
{
  $o = array (
    array ('width', $width),
    array ('height', $height),
    array ('classid', 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000'),
    array ('codebase', 'http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0'),
  );
  $p = array (
    array ('movie', 'http://static.xvideos.com/swf/flv_player_site_v4.swf'),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
    array ('quality', 'high'),
    array ('bgcolor', '#000000'),
    array ('flashvars', 'id_video='.$video_id),
  );
  $e = array (
    array ('src', 'http://static.xvideos.com/swf/flv_player_site_v4.swf'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('menu', 'false'),
    array ('quality', 'high'),
    array ('bgcolor', '#000000'),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
    array ('pluginspage', 'http://www.macromedia.com/go/getflashplayer'),
    array ('flashvars', 'id_video='.$video_id),
  );

  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_xxxbunker ($video_id, $width=550, $height=400)
{
  $url = 'http://xxxbunker.com/playerConfig.php?videoid='.$video_id.'&autoplay=false';
  $url = urlencode ($url);

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', 'http://xxxbunker.com/flash/player.swf'),
    array ('wmode', 'transparent'),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
    array ('flashvars', 'config='.$url),
  );
  $e = array (
    array ('src', 'http://xxxbunker.com/flash/player.swf'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('flashvars', 'config='.$url),  
    array ('wmode', 'transparent'),
    array ('allowFullScreen', 'true'),  
    array ('allowScriptAccess', 'always'),
  );
 
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_tnaflix ($video_id, $width=650, $height=515)
{
  $url = 'config=embedding_feed.php?viewkey='.$video_id;

  $o = array (
    array ('width', $width),
    array ('height', $height),
    array ('type', 'application/x-shockwave-flash'),
    array ('data', 'http://www.tnaflix.com/embedding_player/player_v0.2.1.swf')
  );
  $p = array (
    array ('movie', 'http://www.tnaflix.com//embedding_player/player_v0.2.1.swf'),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
    array ('FlashVars', 'value='.$url),  
  );
    
  return widget_media_object_func ($o, $p, NULL);
}


function
widget_video_xfire ($video_id, $width=425, $height=279)
{
//  $video_id = '1';
  $url = 'http://media.xfire.com/swf/embedplayer.swf';

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('flashvars', 'videoid='.$url),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
  );

  return widget_media_object_func ($o, NULL, $e);
}


function
widget_video_myspace ($video_id, $width=425, $height=360)
{
//  $video_id = 'k4H0eU9uhV7waa1XXp';
  $video_id = '6773592';
  $url = 'http://mediaservices.myspace.com/services/media/embed.aspx/m='.$video_id.',t=1,mt=video';

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', $url),
    array ('wmode', 'transparent'),   
    array ('allowFullScreen', 'true'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('wmode', 'transparent'),
    array ('allowFullScreen', 'true'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_veoh ($video_id, $width=410, $height=341)
{
  $url = 'http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.4.3.1014&permalinkId='
         .$video_id
         .'&player=videodetailsembedded&videoAutoPlay=0&id=anonymous';

  $o = array (
    array ('width', $width),
    array ('height', $height),
    array ('id', 'veohFlashPlayer'),
    array ('name', 'veohFlashPlayer'),
  );
  $p = array (
    array ('movie', $url),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
    array ('id', 'veohFlashPlayer'),
    array ('name', 'veohFlashPlayer'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_google ($video_id, $width=400, $height=326)
{
  $url = 'http://video.google.com/googleplayer.swf?docid='.$video_id.'&fs=true';

  // original: 400x326
  $e = array (
    array ('id', 'VideoPlayback'),
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('style', 'width:'.$width.'px;height:'.$height.'px;'),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
  );
 
  return widget_media_object_func (NULL, NULL, $e);
}


function
widget_video_yahoo ($video_id, $width=512, $height=322)
{
// vid id
//http://espanol.video.yahoo.com/watch/5410123/14251443
//  $video_id = 'k4H0eU9uhV7waa1XXp';
  $video_id = '6773592';
  $video_vid = '6773592';
  $url = 'http://mediaservices.myspace.com/services/media/embed.aspx/m='.$video_id.',t=1,mt=video';

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', 'http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46'),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
    array ('bgcolor', '#000000'),
    array ('flashvars', 'id='.$id.'&vid='.$vid.'&lang=es-mx&intl=e1&thumbUrl=http%3A//l.yimg.com/a/p/i/bcst/videosearch/9707/88446579.jpeg&embed=1'),
  );
  $e = array (
    array ('src', 'http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('allowFullScreen', 'true'),
    array ('allowScriptAccess', 'always'),
    array ('bgcolor', '#000000'),
    array ('flashvars', 'id='.$id.'&vid='.$vid.'&lang=es-mx&intl=e1&thumbUrl=http%3A//l.yimg.com/a/p/i/bcst/videosearch/9707/88446579.jpeg&embed=1'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_media_demux ($media_url)
{
  if (strstr ($media_url, '.youtube.com'))
    return 1;
  else if (strstr ($media_url, '.dailymotion.'))
    return 2;
  else if (strstr ($media_url, '.xfire.com'))
    return 3;
  else if (strstr ($media_url, 'http://') && in_array (strtolower (get_suffix ($media_url)), array ('.flv', '.mp4')))
    return 4; // flv or mp4
  else if (strstr ($media_url, 'http://') && strtolower (get_suffix ($media_url)) == '.mp3')
    return 5; // mp3
  else if (strstr ($media_url, '.veoh.com'))
    return 6;
  else if (strstr ($media_url, 'xvideos.com'))
    return 7;
//  else if (strstr ($media_url, 'xxxbunker.com'))
//    return 8;
//  else if (strstr ($media_url, 'video.google'))
//    return 9;
//  else if (strstr ($media_url, 'tnaflix.com'))
//    return 10;

  return 0;
}


function
widget_media_fullscreen_js ()
{
  // javascript:getinnerwidth() and javascript:getinnerheight()

  $p = '<script type="text/javascript">
<!--
function getinnerwidth ()
  {
    var w = screen.width;
    if (self.innerWidth != undefined)
      w = self.innerWidth;
    else
      {
        var d = document.documentElement;
        if (d)
          w = d.clientWidth;
      }
    return w;
  }

function getinnerheight ()
  {
    var h = screen.height;
    if (self.innerWidth != undefined)
        h = self.innerHeight;
    else
      {
        var d = document.documentElement;
        if (d)
          h = d.clientHeight;
      }
    return h;
  }

//document.write (getinnerwidth ()+\' \'+getinnerheight ());
-->
</script>';

  return $p;
}


function
widget_media ($media_url, $width = NULL, $height = NULL, $download = 1, $autoplay = 1, $hq = 1, $loop = 0)
{
  $demux = widget_media_demux ($media_url);
  $p = $media_url;
  $s = '';

  if (is_array ($p))
    {
      if ($demux == 1)
        return widget_video_youtube_array ($p, $width, $height, $autoplay);
      else
        return '';
    }

  // javascript wrapper for fullscreen
  $f = get_request_value ('f');

  if ($f == 'fullscreen')
    {
//      $width = -1;
//      $height = -1;
    }

  $fullscreen = 0;
  if ($width == -1 || $height == -1)
    {   
      $fullscreen = 1;

      $width = '\'+(getinnerwidth () - 30)+\'';
      $height = '\'+(getinnerheight () - 35)+\'';

      $s .= widget_media_fullscreen_js ();
      $s .= ''
           .'<script type="text/javascript">'."\n"
           .'document.write (\'';
    } 


  if ($demux == 1) // youtube
    {
      if (strstr ($p, '?v='))   
        $p = substr ($p, strpos ($p, '?v=') + 3);
      else
        $p = substr ($p, strpos ($p, 'watch') + 12);
      $p = str_replace ('&feature=youtube_gdata', '', $p);
      
      $s .= widget_video_youtube ($p, $width, $height, $autoplay, $hq, $loop);
    }
  else if ($demux == 2) // dailymotion
    {
      $p = substr ($p, strpos ($p, '/video/') + 7);
      $p = substr ($p, 0, strpos ($p, 'from') - 3);

      $s .= widget_video_dailymotion ($p, $width, $height);   
    }
  else if ($demux == 3) // xfire
    {
      $p = substr ($p, strpos ($p, '/video/') + 7, -1);

      $s .= widget_video_xfire ($p, $width, $height);
    }
  else if ($demux == 4) // flv or mp4
    $s .= widget_video_jwplayer ($p, $width, $height, NULL);
  else if ($demux == 5) // mp3
    $s .= widget_audio_jwplayer ($p);
  else if ($demux == 6)
    {
      // http://www.veoh.com/videos/v6387308sYb9NxBJ
      $p = substr ($p, strpos ($p, '/videos/') + 8);
      $s .= widget_video_veoh ($p, $width, $height);
    }
  else if ($demux == 7)
    {
//http://www.xvideos.com/video266837/dia_zerva_jordan_and_kenzi_marie
      $p = substr ($p, strpos ($p, '/video') + 6);
      $p = substr ($p, 0, strpos ($p, '/'));        

      $s .= widget_video_xvideos ($p, $width, $height);
    }
  else if ($demux == 8)
    {
//http://xxxbunker.com/1209498
      $p = substr ($p, strpos ($p, 'xxxbunker.com/') + 14);

      $s .= widget_video_xxxbunker ($p, $width, $height);
    }
  else if ($demux == 9)
    {
      $p = substr ($p, 0);

      $s .= widget_video_google ($p, $width, $height);
    }
  else if ($demux == 10)
    {
//http://www.tnaflix.com/view_video.php?viewkey=e6f818fd95b6313e2c28
      $p = substr ($p, strpos ($p, 'viewkey=') + 8);

      $s .= widget_video_tnaflix ($p, $width, $height);
    }


 if ($fullscreen)
    {   
      $s .= '\');'
           ."\n\n"
           .'</script>'
;
    }

  if ($f == 'fullscreen')
    $s .= ''
         .'<a href="javascript:void(0);" onclick="javascript:window.close();">Close</a>';
  else
    $s .= ' '
         .widget_window_open (misc_getlink (array ('f' => 'fullscreen'), true), 1, $media_url)
//         .widget_window_open ($_SERVER['HTTP_REFERER'], 1, $media_url)
         .'<a href="javascript:void(0);" onclick="javascript:widget_window_open();"'
//         .' target="_blank"'
         .'>Fullscreen</a>'
;

  return $s;
}


/*
function
playlist_parse_m3u ($filename)
{
  $fh = fopen ($filename, 'r');

  if (!$fh)
    return NULL;

  $demux = 0;
  $count = 0;
  $a = array (array ());
  while (($p = fgets ($fh)))
    {
      $p = str_replace ("\n", '', $p);

      if ($p[0] != '#')
        continue;

      if (strstr ($p, '#EXTM3U'))
        $demux = 1;
      else if (strstr ($p, '#EXTINF:'))
        {
          $p = substr ($p, 8, -3);
          $a[$count]['title'] = trim (substr ($p, strpos ($p, ',') + 1));
          $a[$count]['link'] = str_replace ("\n", '', fgets ($fh));
          $a[$count++]['duration'] = trim (substr ($p, 0, strpos ($p, ',')));
        }
      else $a[$count++]['title'] = trim ($p, ' #-');
    }

  fclose ($fh);

  if ($demux == 0)
    return NULL;

  return $a;
}


function
playlist_parse ($filename)
{
  $suffix = strtolower (get_suffix ($filename));
  if ($suffix == '.m3u')
    return playlist_parse_m3u ($filename);
//  if ($suffix == '.pls')
//    return playlist_parse_pls ($filename);
//  if ($suffix == '.xspf')
//    return playlist_parse_xspf ($filename); // shareable playlist format
//  if ($suffix == '.wpl')
//    return playlist_parse_wpl ($filename);
  return NULL;
}
*/


function
widget_media_playlist ($media_url, $width = NULL, $height = NULL, $autoplay = 1, $hq = 1, $loop = 0)
{
  return '';
}


function
widget_media_info ($media_url)
{
// wikipedia link, cover from google/images, lyrics
// download link for youtube videos
  return '';
}


}

?>