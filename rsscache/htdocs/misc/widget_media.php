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
playlist_parser ($playlist)
{
  // parse m3u, pls, xspf, wpl and return url array
  $a = array ();
/*
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
*/
  return $a;
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
    {
      $param[] = array ('allowFullScreen', 'true');
      $param[] = array ('allowScriptAccess', 'always');
      $param[] = array ('wmode', 'transparent');
      for ($i = 0; isset ($param[$i]); $i++)
        $p .= '<param name="'
             .$param[$i][0].'" value="'.$param[$i][1].'"'
             .'></param>';
    }
  if ($embed)
    {
      $embed[] = array ('allowFullScreen', 'true');      
      $embed[] = array ('allowScriptAccess', 'always');      
      $embed[] = array ('wmode', 'transparent');      
      $p .= '<embed';
      for ($i = 0; isset ($embed[$i]); $i++)
        $p .= ' '.$embed[$i][0].'="'.$embed[$i][1].'"';
      $p .= '></embed>';
    }

  if ($object)
    $p .= '</object>';

  return $p;
}


function
widget_image_html4 ($image_url, $width = NULL, $height = NULL, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';

  $p .= '<img name="widget_image_html4_playlist" src="'.$image_url.'"'
       .($width ? ' width="'.$width.'"' : '')
       .($height ? ' height="'.$height.'"' : '')
       .($hq ? ' style="image-rendering:otimizeQuality"' : ' style="image-rendering:optimizeSpeed"')
       .' border="0">';
  return $p;
//  return '';
}


/*
function
widget_audio_html4 ($audio_url, $width, $height, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';
  return $p;
}
*/


function
widget_audio_html5 ($audio_url, $width, $height, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';
  $p .= '<audio src="'.$audio_url.'"'
       .' controls="controls"'
       .($autoplay ? ' autoplay="autoplay"' : '')
       .($loop ? ' loop="loop"' : '')
       .' preload="meta">'
       // fallback to html4 (flash)
//       .widget_audio_html4 ($video_url, $width, $height, $autoplay, $hq, $loop)
       .'</audio>';
  return $p;
}


/*
function
widget_audio_youtube ($video_url, $width, $height, $autoplay = 0, $hq = 0, $loop = 0)
{
// show only the controls
  $p = '';
  return $p;
}
*/


function
widget_video_html4 ($video_url, $width = 400, $height = 300, $autoplay = 0, $hq = 0, $loop = 0)
{
  $url = $video_url;

  if ($preview_image)
    $url = '&image='.$preview_image;

  // using flowplayer or jwplayer
  $o = array (
    array ('type', 'application/x-shockwave-flash'),
    array ('data', 'misc/flowplayer.swf'), 
//    array ('data', 'misc/jwplayer.swf'),  
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', 'misc/flowplayer.swf'),
//    array ('movie', 'misc/jwplayer.swf'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('flashvars', 'config={"clip":"'.$video_url.'"}'),
//    array ('flashvars', 'file='.$video_url),  
  );

  return widget_media_object_func ($o, $p, NULL);
}


function
widget_video_html5 ($video_url, $width = 400, $height = 300, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';
  $p .= '<video src="'.$video_url.'" width="'.$width.'" height="'.$height.'"'
       .' controls="controls"'
       .($autoplay ? ' autoplay="autoplay"' : '')
       .($loop ? ' loop="loop"' : '')
       .' preload="meta">'
       // fallback to html4 (flash)
//       .widget_video_html4 ($video_url, $width, $height, $autoplay, $hq, $loop)
       .'</video>'
//       .'<br>'
//       .'Video codec: <a href="http://www.webmproject.org/users/">WebM</a>'
;

  return $p;
}


function
widget_video_youtube ($video_url, $width = 425, $height = 344, $autoplay = 0, $hq = 0, $loop = 0)
{
  $tor_enabled = 0;
  if (strstr ($video_url, '?v='))   
    $video_url = substr ($video_url, strpos ($video_url, '?v=') + 3);
  else
    $video_url = substr ($video_url, strpos ($video_url, 'watch') + 12);
  $video_url = str_replace ('&feature=youtube_gdata', '', $video_url);

//http://code.google.com/apis/youtube/player_parameters.html      
  $url = 'http://www.youtube.com/v/'
        .$video_url
       .'&fs=1'             // allow fullscreen
//       .'&rel=1'            // related
       .($autoplay ? '&autoplay=1' : '')
       .($loop ? '&loop=1' : '')
//       .'&color1=0x000000'
//       .'&color2=0x000000'
//       .'&start=30'         // skip to
//       .($hq ? '&hd=1' : '')  // high quality?
       .'&showinfo=0'
       .'&showsearch=0' // search
       .'&border=0'
;

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', $url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('autoplay', $autoplay ? 'true' : 'false'),
//    array ('wmode', 'transparent'),   
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('autoplay', $autoplay ? 'true' : 'false'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );
 
  return widget_media_object_func ($o, $p, $e);

  // iframe
//  return '<iframe class="youtube-player" type="text/html" width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$video_url
//        .($autoplay ? '&autoplay=1' : '')
//        .($loop ? '&loop=1' : '')
//        .'" frameborder="0"></iframe>';
}



function
widget_video_dailymotion ($video_url, $width=420, $height=336, $autoplay = 0, $hq = 0, $loop = 0)
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
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );

  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_xvideos ($video_url, $width=510, $height=400, $autoplay = 0, $hq = 0, $loop = 0)
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
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
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
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('pluginspage', 'http://www.macromedia.com/go/getflashplayer'),
    array ('flashvars', 'id_video='.$video_url),
  );

  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_xxxbunker ($video_url, $width=550, $height=400, $autoplay = 0, $hq = 0, $loop = 0)
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
//    array ('wmode', 'transparent'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('flashvars', 'config='.$url),
  );
  $e = array (
    array ('src', 'http://xxxbunker.com/flash/player.swf'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('flashvars', 'config='.$url),  
//    array ('wmode', 'transparent'),
//    array ('allowFullScreen', 'true'),  
//    array ('allowScriptAccess', 'always'),
  );
 
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_tnaflix ($video_url, $width=650, $height=515, $autoplay = 0, $hq = 0, $loop = 0)
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
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('FlashVars', 'value='.$url),  
  );
    
  return widget_media_object_func ($o, $p, NULL);
}


function
widget_video_xfire ($video_url, $width=425, $height=279, $autoplay = 0, $hq = 0, $loop = 0)
{
  $video_url = substr ($video_url, strpos ($video_url, '/video/') + 7, -1);
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
    array ('flashvars', 'videoid='.$video_url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );

  return widget_media_object_func ($o, NULL, $e);
}


function
widget_video_myspace ($video_url, $width=425, $height=360, $autoplay = 0, $hq = 0, $loop = 0)
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
//    array ('wmode', 'transparent'),   
//    array ('allowFullScreen', 'true'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('wmode', 'transparent'),
//    array ('allowFullScreen', 'true'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_veoh ($video_url, $width=410, $height=341, $autoplay = 0, $hq = 0, $loop = 0)
{
  $url = substr ($video_url, strrpos ($video_url, '/') + 1);
  $url = 'http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.5.3.1011&permalinkId='
         .$url
         .'&player=videodetailsembedded&videoAutoPlay=0&id=anonymous';

  $o = array (
    array ('width', $width),
    array ('height', $height),
    array ('id', 'veohFlashPlayer'),
    array ('name', 'veohFlashPlayer'),
  );
  $p = array (
    array ('movie', $url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('id', 'veohFlashPlayerEmbed'),
    array ('name', 'veohFlashPlayerEmbed'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_google ($video_url, $width=400, $height=326, $autoplay = 0, $hq = 0, $loop = 0)
{
  $url = 'http://video.google.com/googleplayer.swf?docid='.$video_url.'&fs=true';

  // original: 400x326
  $e = array (
    array ('id', 'VideoPlayback'),
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('style', 'width:'.$width.'px;height:'.$height.'px;'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );
 
  return widget_media_object_func (NULL, NULL, $e);
}


function
widget_video_yahoo ($video_url, $width=512, $height=322, $autoplay = 0, $hq = 0, $loop = 0)
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
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('bgcolor', '#000000'),
    array ('flashvars', 'id='.$id.'&vid='.$vid.'&lang=es-mx&intl=e1&thumbUrl=http%3A//l.yimg.com/a/p/i/bcst/videosearch/9707/88446579.jpeg&embed=1'),
  );
  $e = array (
    array ('src', 'http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('bgcolor', '#000000'),
    array ('flashvars', 'id='.$id.'&vid='.$vid.'&lang=es-mx&intl=e1&thumbUrl=http%3A//l.yimg.com/a/p/i/bcst/videosearch/9707/88446579.jpeg&embed=1'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_own3d ($video_url, $width=640, $height=360, $autoplay = 0, $hq = 0, $loop = 0)
{
  //http://www.own3d.tv/video/25617
  //http://www.own3d.tv/stream/25617
  $url = str_replace ('watch', 'stream', $video_url);

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', $url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
//    array ('wmode', 'transparent'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
//    array ('wmode', 'transparent'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_archive ($video_url, $width=640, $height=506, $autoplay = 0, $hq = 0, $loop = 0)
{
  $o = array (
    array ('width', $width),
    array ('height', $height),
//    array ('classid', 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'),
  );
  $p = array (
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('quality', 'high'),           
    array ('cachebusting', 'true'),
//    array ('bgcolor', '#000000'),
    array ('movie', 'http://www.archive.org/flow/flowplayer.commercial-3.2.1.swf'),
    array ('flashvars', "config={'key':'#$aa4baff94a9bdcafce8','playlist':['format=Thumbnail?.jpg',{'autoPlay':false,'url':'Consolevania-03x25677-3-The_Black_Episode_512kb.mp4'}],'clip':{'autoPlay':true,'baseUrl':'http://www.archive.org/download/Consolevania-03x25677-3-The_Black_Episode/','scaling':'fit','provider':'h264streaming'},'canvas':{'backgroundColor':'#000000','backgroundGradient':'none'},'plugins':{'controls':{'playlist':false,'fullscreen':true,'height':26,'backgroundColor':'#000000','autoHide':{'fullscreenOnly':true}},'h264streaming':{'url':'http://www.archive.org/flow/flowplayer.pseudostreaming-3.2.1.swf'}},'contextMenu':[{},'-','Flowplayer v3.2.1']}"),
  );
  $e = array (
    array ('src', 'http://www.archive.org/flow/flowplayer.commercial-3.2.1.swf'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('quality', 'high'),
    array ('cachebusting', 'true'),
//    array ('bgcolor', '#000000'),
    array ('flashvars', "config={'key':'#$aa4baff94a9bdcafce8','playlist':['format=Thumbnail?.jpg',{'autoPlay':false,'url':'Consolevania-03x25677-3-The_Black_Episode_512kb.mp4'}],'clip':{'autoPlay':true,'baseUrl':'http://www.archive.org/download/Consolevania-03x25677-3-The_Black_Episode/','scaling':'fit','provider':'h264streaming'},'canvas':{'backgroundColor':'#000000','backgroundGradient':'none'},'plugins':{'controls':{'playlist':false,'fullscreen':true,'height':26,'backgroundColor':'#000000','autoHide':{'fullscreenOnly':true}},'h264streaming':{'url':'http://www.archive.org/flow/flowplayer.pseudostreaming-3.2.1.swf'}},'contextMenu':[{},'-','Flowplayer v3.2.1']}"),
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
  else if (//strstr ($media_url, 'http://') && 
           in_array (strtolower (get_suffix ($media_url)), array ('.flv', '.mp4', '.mp3')))
    return 4; // jwplayer or flowplayer
  else if (//strstr ($media_url, 'http://') && 
           in_array (strtolower (get_suffix ($media_url)), array ('.webm', '.ogg')))
    return 5; // <video>
  else if (//strstr ($media_url, 'http://') &&
           in_array (strtolower (get_suffix ($media_url)), array ('.weba', '.wav')))
    return 6; // <audio>
  else if (strstr ($media_url, '.veoh.com'))
    return 7;
  else if (strstr ($media_url, 'xvideos.com'))
    return 8;
  else if (strstr ($media_url, 'xxxbunker.com'))
    return 9;
  else if (strstr ($media_url, 'video.google'))
    return 10;
  else if (strstr ($media_url, 'tnaflix.com'))
    return 11;
  else if (strstr ($media_url, 'own3d.tv'))
    return 12;
  else if (strstr ($media_url, 'archive.org'))
    return 13;
  else if (//strstr ($media_url, 'http://') &&
           in_array (strtolower (get_suffix ($media_url)), array ('.jpg', '.png', '.webp', '.gif')))
    return 14; // <img>

  return 0;
}


function
widget_media_demux_func ($media_url)
{
  $demux = widget_media_demux ($media_url);
  $a = array (
         'widget_video_youtube',
         'widget_video_dailymotion',
         'widget_video_xfire',
         'widget_video_html4',  
         'widget_video_html5',    
         'widget_audio_html5',    
         'widget_video_veoh',     
         'widget_video_xvideos',  
         'widget_video_xxxbunker',
         'widget_video_google', 
         'widget_video_tnaflix',
         'widget_video_own3d',  
         'widget_video_archive',
         'widget_image_html4',
);
  if ($demux > 0)
    if (isset ($a[$demux - 1])) 
      return $a[$demux - 1];
  return NULL;
}


function
widget_media_embed_code ($media_url)
{
  $func = widget_media_demux_func ($media_url);
  $p = '';

  if ($func)
    {
      $c = $func ($media_url);
      $p .= '<input type="text" readonly="readonly" value="'.htmlentities ($c).'">';
    }
  return $p;
}


function
widget_media_download ($media_url)
{
  $demux = widget_media_demux ($media_url);
  $p = '';

  if ($demux == 1)
    {
  $yt_array = youtube_download ($video_url, $tor_enabled, 0);

  // DEBUG
//  echo '<pre><tt>';
//  echo $video_url."\n";
//  print_r ($a); 

  $yt = $yt_array[0];

  if ($yt['status'] == 'fail') // youtube fail
    {
      $p .= $yt['errorcode'].': '.$yt['reason'];
 
      switch ($yt['errorcode'])
        {
          case 150: // copyright
            $p .= '<br>'
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

      $p .= '<br>';

      // download
      $p .= '<a href="'.$yt['video_url'].'">Best</a>';

      for ($q = 0; isset ($yt[$q]); $q++)
        {
          $b = explode ('/', $a[$q]);
          $fmt = substr ($yt[$q], 0, strpos ($yt[$q], '|'));
          $t = substr ($yt[$q], strpos ($yt[$q], '|') + 1);
          $p .= ' <a href="'.$t.'" title="&fmt='.$fmt.'">'.$b[1].'</a>';
        }

      // direct link
//      $p .= ' <a href="'.$yt['ad_eurl'].'">Direct</a>';

      $p .= '<br>';

      $p .= ''
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

      $p .= '<br>';
      $p .= widget_collapse ('Details', '<pre><tt>'.sprint_r ($yt).'</tt></pre>', 1);
    }
    }

  return $p;
}


function
widget_media ($media_url, $width = NULL, $height = NULL, $ratio = NULL, $autoplay = 0, $hq = 0, $loop = 0)
{
  $func = widget_media_demux_func ($media_url);
  $p = '';

  $scale = 0;

  if ($width == -1 || $height == -1)
    $scale = 1;

  if ($scale)
    {
      $bg_width = '\'+Math.floor (misc_getwh ()[0])+\''; // width of black background
      if ($ratio)
        $width = '\'+Math.floor (misc_getwh ()[1] * '.$ratio.')+\'';
      else
        $width = $bg_width;
      $height = '\'+Math.floor (misc_getwh ()[1])+\'';
      $p .= ''
           .'<script type="text/javascript">'."\n"
           .'document.write (\'';
    } 
  else
    {
      $bg_width = floor ($width); // width of black background
      if ($ratio)
        $width = floor ($height * $ratio);
      else
        $width = $bg_width;
      $height = floor ($height);
    }

  if ($func)
      {
        $c = $func ($media_url, $width, $height, $autoplay, $hq, $loop);
        $p .= '<div width="'.$bg_width.'" height="'.$height.'" style="background-color:#000;text-align:center;">'
             .$c
             .'</div>';
      }

  if ($scale)
    {   
      $p .= '\');'."\n\n"
           .'</script>'
;
    }

  return $p;
}


function
widget_video_youtube_playlist ($video_urls, $width = 425, $height = 344, $ratio = NULL, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';
  return $p;
}


function
widget_media_playlist ($media_urls, $width = NULL, $height = NULL, $ratio = NULL, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';
  return $p;
}


}

?>