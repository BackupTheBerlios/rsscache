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
widget_getwidth_js ()
{
  $p = '<script type="text/javascript">
<!--
function widget_getwidth ()
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
//document.write (widget_getwidth ());
-->
</script>';

  return $p;
}


function
widget_getheight_js ()
{
  $p = '<script type="text/javascript">
<!--
function widget_getheight ()
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

//document.write (widget_getheight ());
-->
</script>';

  return $p;
}


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
widget_media_info_func ($media_url)
{
// wikipedia link, cover from google/images, lyrics
// download link for youtube videos
  return '';
}


function
widget_video_html5 ($video_url, $width = 400, $height = 300, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  $p = '';
  $p .= '<video src="'.$video_url.'" width="'.$width.'" height="'.$height.'"'
       .($control ? ' controls="controls"' : '')
       .($autoplay ? ' autoplay="autoplay"' : '')
       .($loop ? ' loop="loop"' : '')
       .' preload="meta"></video>'
       .'<br>'
       .'Video codec: <a href="http://www.webmproject.org/users/">WebM</a>'
;
<iframe class="youtube-player" type="text/html" width="1280" height="745" src="http://www.youtube.com/embed/A5YDsjDvB8w" frameborder="0"></iframe>


  return $p;
}


function
widget_audio_html5 ($audio_url, $width, $height, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  $p = '';
  $p .= '<audio src="'.$audio_url.'"'
       .($control ? ' controls="controls"' : '')
       .($autoplay ? ' autoplay="autoplay"' : '')
       .($loop ? ' loop="loop"' : '')
       .' preload="meta"></audio>';
  return $p;
}


function
widget_audio_youtube ($video_url, $width, $height, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
// show only the controls
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
widget_video_youtube ($video_url, $width = 425, $height = 344, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  $tor_enabled = 0;
  if (strstr ($video_url, '?v='))   
    $video_url = substr ($video_url, strpos ($video_url, '?v=') + 3);
  else
    $video_url = substr ($video_url, strpos ($video_url, 'watch') + 12);
  $video_url = str_replace ('&feature=youtube_gdata', '', $video_url);
      
  $url = 'http://www.youtube.com/v/'
        .$video_url
       .'&fs=1'             // allow fullscreen
//       .'&ap=%2526fmt%3D'.($hq ? 18 : 5) // high(er) quality?
//       .'&showsearch=0'     // search
//       .'&rel=1'            // related
       .($autoplay ? '&autoplay=1' : '')
       .($loop ? '&loop=1' : '')
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

  // iframe and hd
  $s = '<iframe class="youtube-player" type="text/html" width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$video_url.'" frameborder="0"></iframe>';

  if ($download == 0)
    return $s;

  $yt_array = youtube_download ($video_url, $tor_enabled, 0);

  // DEBUG
//  echo '<pre><tt>';
//  echo $video_url."\n";
//  print_r ($a); 

  $yt = $yt_array[0];

  if ($yt['status'] == 'fail') // youtube fail
    {
      $s .= $yt['errorcode'].': '.$yt['reason'];
 
      switch ($yt['errorcode'])
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
//    [fmt_list] => 35/854x480/9/0/115,34/640x360/9/0/115,18/640x360/9/0/115,5/320x240/7/0/0
      $a = explode (',', $yt['fmt_list']);

      $s .= '<br>';

      // download
      $s .= '<a href="'.$yt['video_url'].'">Best</a>';

      for ($q = 0; isset ($yt[$q]); $q++)
        {
          $b = explode ('/', $a[$q]);
          $fmt = substr ($yt[$q], 0, strpos ($yt[$q], '|'));
          $t = substr ($yt[$q], strpos ($yt[$q], '|') + 1);
          $s .= ' <a href="'.$t.'" title="&fmt='.$fmt.'">'.$b[1].'</a>';
        }

      // direct link
//      $s .= ' <a href="'.$yt['ad_eurl'].'">Direct</a>';

      $s .= '<br>';

      $s .= ''
//           .'<div style="width:'.($width - 10).'px;">'
           .'<input type="text"'
           .' style="width:'.($width - 10).'px;"'
           .' value="'
           .$yt['title']
           .'" readonly="readonly"'
//           .' onclick="javascript:this.execCommand(\'copy\');"'
           .'>'
//           .'</div>'
;

      $s .= '<br>';
      $s .= widget_collapse ('Details', '<pre><tt>'.sprint_r ($yt).'</tt></pre>', 1);
    }

  return $s;
}



function
widget_video_dailymotion ($video_url, $width=420, $height=336, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  $video_url = substr ($video_url, strpos ($video_url, '/video/') + 7);
  $video_url = substr ($video_url, 0, strpos ($video_url, 'from') - 3);
  // $video_url = 'k4H0eU9uhV7waa1XXp';
  $url = 'http://www.dailymotion.com/swf/'.$video_url.'&related=1';

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
widget_video_xvideos ($video_url, $width=510, $height=400, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  // http://www.xvideos.com/video266837/dia_zerva_jordan_and_kenzi_marie
  $video_url = substr ($video_url, strpos ($video_url, '/video') + 6);
  $video_url = substr ($video_url, 0, strpos ($video_url, '/'));        

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
    array ('flashvars', 'id_video='.$video_url),
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
    array ('flashvars', 'id_video='.$video_url),
  );

  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_xxxbunker ($video_url, $width=550, $height=400, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  //http://xxxbunker.com/1209498
  $video_url = substr ($video_url, strpos ($video_url, 'xxxbunker.com/') + 14);
  $url = 'http://xxxbunker.com/playerConfig.php?videoid='.$video_url.'&autoplay=false';
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
widget_video_tnaflix ($video_url, $width=650, $height=515, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  // http://www.tnaflix.com/view_video.php?viewkey=e6f818fd95b6313e2c28
  $video_url = substr ($video_url, strpos ($video_url, 'viewkey=') + 8);
  $url = 'config=embedding_feed.php?viewkey='.$video_url;

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
widget_video_xfire ($video_url, $width=425, $height=279, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  $video_url = substr ($video_url, strpos ($video_url, '/video/') + 7, -1);
//  $video_url = '1';
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
widget_video_myspace ($video_url, $width=425, $height=360, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
//  $video_url = 'k4H0eU9uhV7waa1XXp';
  $video_url = '6773592';
  $url = 'http://mediaservices.myspace.com/services/media/embed.aspx/m='.$video_url.',t=1,mt=video';

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
widget_video_veoh ($video_url, $width=410, $height=341, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  // http://www.veoh.com/videos/v6387308sYb9NxBJ
  $video_url = substr ($video_url, strpos ($video_url, '/videos/') + 8);
  $url = 'http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.4.3.1014&permalinkId='
         .$video_url
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
widget_video_google ($video_url, $width=400, $height=326, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  $url = 'http://video.google.com/googleplayer.swf?docid='.$video_url.'&fs=true';

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
widget_video_yahoo ($video_url, $width=512, $height=322, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
// vid id
//http://espanol.video.yahoo.com/watch/5410123/14251443
//  $video_url = 'k4H0eU9uhV7waa1XXp';
  $video_url = '6773592';
  $video_vid = '6773592';
  $url = 'http://mediaservices.myspace.com/services/media/embed.aspx/m='.$video_url.',t=1,mt=video';

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
  else if (strstr ($media_url, 'http://') && 
           in_array (strtolower (get_suffix ($media_url)), array ('.webm', '.ogg')))
    return 4; // <video>
  else if (strstr ($media_url, 'http://') &&
           in_array (strtolower (get_suffix ($media_url)), array ('.weba', '.wav')))
    return 5; // <audio>
  else if (strstr ($media_url, '.veoh.com'))
    return 6;
  else if (strstr ($media_url, 'xvideos.com'))
    return 7;
  else if (strstr ($media_url, 'xxxbunker.com'))
    return 8;
  else if (strstr ($media_url, 'video.google'))
    return 9;
  else if (strstr ($media_url, 'tnaflix.com'))
    return 10;
  else if (strstr ($media_url, 'http://') && 
           in_array (strtolower (get_suffix ($media_url)), array ('.m3u', '.pls', '.xspf', '.wpl')))
    return 11; // playlist files

  return 0;
}


function
widget_media ($media_url, $width = NULL, $height = NULL, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  $demux = widget_media_demux ($media_url);
  $s = '';

  // javascript wrapper for fullscreen
//  $f = get_request_value ('f');

//  if ($f == 'fullscreen')
//    {
//      $width = -1;
//      $height = -1;
//    }

  $fullscreen = 0;
  if ($width == -1 || $height == -1)
    {   
      $fullscreen = 1;

      $width = '\'+(widget_getwidth () - 30)+\'';
      $height = '\'+(widget_getheight () - 35)+\'';

      $s .= widget_getwidth_js ()
           .widget_getheight_js ();
      $s .= ''
           .'<script type="text/javascript">'."\n"
           .'document.write (\'';
    } 

  $a = array (
         'widget_video_youtube',
         'widget_video_dailymotion',
         'widget_video_xfire',
         'widget_video_html5',
         'widget_audio_html5',
         'widget_video_veoh',
         'widget_video_xvideos',
         'widget_video_xxxbunker',
         'widget_video_google',
         'widget_video_tnaflix'
);

  if ($demux > 0)
    if (isset ($a[$demux - 1]))
      {
        $p = $a[$demux - 1];
        $s .= $p ($media_url, $width, $height, $download, $autoplay, $hq, $loop);
      }

 if ($fullscreen)
    {   
      $s .= '\');'."\n\n"
           .'</script>'
;
    }

/*
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
*/
  return $s;
}


function
widget_audio_file_playlist ($media_url, $width = NULL, $height = NULL, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  $fh = fopen ($filename, 'r');

  if (!$fh)
    return '';

  $demux = 0;
  $suffix = strtolower (get_suffix ($filename));
  if ($suffix == '.m3u')
    $demux = 1;
  if ($suffix == '.pls')
    $demux = 2;
  if ($suffix == '.xspf')
    $demux = 3;
  if ($suffix == '.wpl')
    $demux = 4;

  if ($demux == 1)
    {
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
    }

  return $a;
}


function
widget_video_youtube_playlist ($video_urls, $width = 425, $height = 344, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
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

  for ($i = 0; isset ($video_url_array[$i]); $i++)
    $p .= ($i > 0 ? ",\n" : '').'"'.$video_urls[$i].'"';

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
widget_media_playlist ($media_urls, $width = NULL, $height = NULL, $download = 0, $autoplay = 1, $hq = 0, $loop = 0)
{
  // not a playlist?
  if (!is_array ($media_urls))
    return widget_media ($media_urls, $width, $height, $download, $autoplay, $hq, $loop);

  $a = array (
         'widget_video_youtube_playlist',
         'widget_audio_file_playlist'
);

  if ($demux > 0)
    if (isset ($a[$demux - 1]))
      {
        $p = $a[$demux - 1];
        $s .= $p ($media_urls, $width, $height, $download, $autoplay, $hq, $loop);
      }

  return '';
}


}

?>