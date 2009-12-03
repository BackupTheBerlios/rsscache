<?php
/*
widget.php - new HTML widgets

Copyright (c) 2006 - 2008 NoisyB


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
if (!defined ('MISC_WIDGET_PHP'))
{
define ('MISC_WIDGET_PHP', 1);  
include_once ('misc/misc.php');


function
widget_video_flowplayer ($video_url, $width = 400, $height = 300, $preview_image = NULL)
{
  $fgcolor = '#ffffff';
  $bgcolor = '#000000';
  $bgcolor2 = '#444444';
  $bgcolor3 = '#ff0000';
  $url = $video_url;

  $p = '<script type="text/javascript" src="misc/flowplayer-3.1.4.min.js"></script>'
      .'<a href="'.$url.'" id="player"></a>'
      .'<script><!--'."\n"
      .'flowplayer('."\n"
      .'  "player",'."\n"
      .'  {'."\n"
      .'    src: "misc/flowplayer-3.1.4.swf",'."\n"
      .'    width:'.$width.','."\n"
      .'    height:'.$height."\n"
      .'  },'."\n"
      .'  {'."\n"
      .'    canvas: {backgroundColor: "'.$bgcolor.'"'."\n"
      .'  },'."\n"
      .'  plugins:'."\n"
      .'    {'."\n"
      .'      controls:'."\n"
      .'        {'."\n"
      .'          buttonOverColor: "'.$bgcolor2.'",'."\n"
      .'          timeColor: "'.$fgcolor.'",'."\n"
      .'          sliderColor: "'.$bgcolor2.'",'."\n"
      .'          buttonColor: "'.$bgcolor.'",'."\n"
      .'          bufferColor: "'.$bgcolor3.'",'."\n"
      .'          progressColor: "'.$bgcolor2.'",'."\n"
      .'          durationColor: "'.$fgcolor.'",'."\n"
      .'          progressGradient: "none",'."\n"
      .'          sliderGradient: "none",'."\n"
      .'          borderRadius: "0px",'."\n"
      .'          backgroundColor: "'.$bgcolor.'",'."\n"
      .'          backgroundGradient: "none",'."\n"
      .'          bufferGradient: "none",'."\n"
      .'          opacity:1.0'."\n"
      .'        }'."\n"
      .'    }'."\n"
      .'});'."\n"
      .'//-->'
      .'</script>';

  return $p;
}


function
widget_video_jwplayer ($video_url, $width = 400, $height = 300, $preview_image = NULL)
{
  $url = $video_url;

  if ($preview_image)
    $url = '&image='.$preview_image;

  $p = ''
//      .'<script type="text/javascript" src="misc/swfobject.js"></script>'
//      .'<script type="text/javascript">'."\n"
//      .'swfobject.registerObject("player","9.0.98","misc/expressInstall.swf");'."\n"
//      .'</script>'
      .'<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="'.$width.'" height="'.$height.'">'
      .'<param name="movie" value="misc/player.swf" />'
      .'<param name="allowfullscreen" value="true" />' 
      .'<param name="allowscriptaccess" value="always" />' 
      .'<param name="flashvars" value="file='.$url.'" />'
      .'<object type="application/x-shockwave-flash" data="misc/player.swf" width="'.$width.'" height="'.$height.'">'
      .'<param name="movie" value="misc/player.swf" />'
      .'<param name="allowfullscreen" value="true" />'
      .'<param name="allowscriptaccess" value="always" />'
      .'<param name="flashvars" value="file='.$url.'" />'
//      .<p><a href="http://get.adobe.com/flashplayer">Get Flash</a> to see this player.</p>'
      .'</object>'
      .'</object>';

  return $p;
}


function
widget_audio_jwplayer ($audio_url, $start = 0, $stream = 0, $next_stream = NULL)
{
  $url = 'misc/widget_audio.swf?url='.$audio_url.'&start='.$start.'&stream='.$stream
        .($next_stream ? '&next='.$next_stream : '');

  $p = ''
      .'<object>'
      .'<embed'
      .' src="'.$url.'"'
      .' type="application/x-shockwave-flash"'
      .' width="1"'
      .' height="1"'
//      .' pluginspace="http://www.macromedia.com/go/flashplayer/"'
      .'></embed>'
      .'</object>';

/*
else if (isset($_GET['mp3']))
  {
    $mp3_width = "150";				// Set the width of your FLV Player
    $mp3_height = "120";			// Set the total height of your FLV Player
    $mp3_dispheight = "100";		// Set the display height (above the control bar) of your FLV Player
    
    $mp3_bgcolor = "000000";		// Set the background color
    $mp3_lightcolor = "CC9900";		// Set the light color
    $mp3_backcolor = "000000";		// Set the back color
    $mp3_frontcolor = "CCCCCC";		// Set the front color
    
    $mp3_site = "";
    $mp3_file = "block_playlist.xml";	// Set the file location of the MP3 or XML file (XML must be in the "player" directory)	
    $mp3_auto = "true";				// Set the FLV player to automatically start when the page loads
    $mp3_shuffle = "true";			// Set the playlist to shuffle the songs
    $mp3_digits = "false";			// Turn on/off the elapsed time digits
    $mp3_repeat = "true";			// Set the music to auto repeat
    $mp3_showeq = "true";			// Display faux equalizers (if your display height is high enough)
    $mp3_showfs = "false";			// Display the Full Screen button
    $mp3_showthumbs = "false";		// Display the thumbnail from the RSS <image> tag in the playlist
    
    $p .= '<object type="application/x-shockwave-flash" data="'.$mp3_site.'player/mediaplayer.swf" height="'.$mp3_height.'" width="'.$mp3_width.'">
    	<param name="movie" value="'.$mp3_site.'player/mediaplayer.swf" />
    	<param name="allowScriptAccess" value="never" />
    	<param name="allowNetworking" value="internal" />
    	<param name="wmode" value="transparent" />
    	<param name="bgcolor" value="#FFFFFF" />
    	<param name="flashvars" value="file='.$mp3_file.'&autostart='.$mp3_auto.'&shuffle='.$mp3_shuffle.'&showdigits='.$mp3_digits.'&showeq='.$mp3_showeq.'&showfsbutton='.$mp3_showfs.'&displayheight='.$mp3_dispheight.'&repeat='.$mp3_repeat.'&thumbsinplaylist='.$mp3_showthumbs.'&lightcolor=0x'.$mp3_lightcolor.'&backcolor=0x'.$mp3_backcolor.'&frontcolor=0x'.$mp3_frontcolor.'&bufferlength=10" />
    	<embed allowScriptAccess="never" allowNetworking="internal" enableJavaScript="false" 
    	src="'.$mp3_site.'player/mediaplayer.swf" wmode="transparent" width="'.$mp3_width.'" height="'.$mp3_height.'" bgcolor="#'.$mp3_bgcolor.'" 
    	type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" 
    	flashvars="file='.$mp3_file.'&autostart='.$mp3_auto.'&shuffle='.$mp3_shuffle.'&showdigits='.$mp3_digits.'&showeq='.$mp3_showeq.'&showfsbutton='.$mp3_showfs.'&displayheight='.$mp3_dispheight.'&repeat='.$mp3_repeat.'&thumbsinplaylist='.$mp3_showthumbs.'&lightcolor=0x'.$mp3_lightcolor.'&backcolor=0x'.$mp3_backcolor.'&frontcolor=0x'.$mp3_frontcolor.'&bufferlength=10" />
    	</embed></object>';
  }
*/

  return $p;
}


function
widget_video_youtube_array ($video_id_array, $width = 425, $height = 344, $autoplay = 1)
{
  $p = '';

  $p .= '<script src="http://www.google.com/jsapi"></script>'

.'<script>
google.load ("swfobject", "2.1");
</script>'
//      .'<script type="text/javascript" src="misc/swfobject.js"></script>'

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
  $url = 'http://www.youtube.com/v/'
        .$video_id
       .'&fs=1'             // allow fullscreen
       .'&ap=%2526fmt%3D'.($hq ? 18 : 5) // high(er) quality?
       .'&showsearch=0'     // no search
       .'&rel=0'            // no related
       .($autoplay ? '&autoplay=1' : '')
       .($loop ? '&loop=1' : '')
//       .'&color1=0x000000'
//       .'&color2=0x000000'
//       .'#t=03m22s'         // skip to
//       .'&start=30'         // skip to (2)
;

  $p = ''
      .'<object width="'.$width.'" height="'.$height.'">'
      .'<param name="movie" value="'.$url.'">'
      .'</param><param name="allowFullScreen" value="true"></param>'
      .'</param><param name="autoplay" value="true"></param>'
      .'<embed src="'.$url.'"'
      .' type="application/x-shockwave-flash"'
      .' allowfullscreen="true"'
      .($autoplay ? ' autoplay="true"' : '')
      .' width="'.$width.'" height="'.$height.'"'
      .'></embed>'
      .'</object>';

  return $p;
}


function
widget_video_dailymotion ($video_id, $width=420, $height=336)
{
//  $video_id = 'k4H0eU9uhV7waa1XXp';
  $url = 'http://www.dailymotion.com/swf/'.$video_id.'&related=1';

  $p = ''
      .'<object width="'.$width.'" height="'.$height.'">'
      .'<param name="movie" value="'.$url.'"></param>'
      .'<param name="allowFullScreen" value="true"></param>'
      .'<param name="allowScriptAccess" value="always"></param>'
      .'<embed src="'
      .$url
      .'" type="application/x-shockwave-flash" width="'
      .$width
      .'" height="'
      .$height
      .'" allowFullScreen="true" allowScriptAccess="always"></embed>'
      .'</object>'
;
  return $p;
}


function
widget_video_xvideos ($video_id, $width=510, $height=400)
{

  $p = '<object width="'.$width.'" height="'.$height.'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"'
      .' codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" >'
      .'<param name="quality" value="high" />'
      .'<param name="bgcolor" value="#000000" />'
      .'<param name="allowScriptAccess" value="always" />'
      .'<param name="movie" value="http://static.xvideos.com/swf/flv_player_site_v4.swf" />'
      .'<param name="allowFullScreen" value="true" />'
      .'<param name="flashvars" value="id_video='.$video_id.'" />'
      .'<embed src="http://static.xvideos.com/swf/flv_player_site_v4.swf"'
      .' allowscriptaccess="always" width="'.$width.'" height="'.$height.'"'
      .' menu="false" quality="high" bgcolor="#000000" allowfullscreen="true"'
      .' flashvars="id_video='.$video_id.'"'
      .' type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />'
      .'</object>';

  return $p;
}


function
widget_video_xxxbunker ($video_id, $width=550, $height=400)
{
  $url = 'http://xxxbunker.com/playerConfig.php?videoid='.$video_id.'&autoplay=false';
  $url = urlencode ($url);

  $p = '<object width="'.$width.'" height="'.$height.'">'
      .'<param name="movie" value="http://xxxbunker.com/flash/player.swf"></param>'
      .'<param name="wmode" value="transparent"></param>'
      .'<param name="allowfullscreen" value="true"></param>'
      .'<param name="allowscriptaccess" value="always"></param>'
      .'<param name="flashvars" value="config='.$url.'">'
      .'</param>'
      .'<embed src="http://xxxbunker.com/flash/player.swf"'
      .' type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="transparent"'
      .' width="'.$width.'" height="'.$height.'"'
      .' flashvars="config='.$url.'">'
      .'</embed>'
      .'</object>'
;
  return $p;
}


function
widget_video_xfire ($video_id, $width=425, $height=279)
{
//  $video_id = '1';
  $url = 'http://media.xfire.com/swf/embedplayer.swf';

  $p = ''
      .'<object width="'.$width.'" height="'.$height.'">'
      .'<embed src="'.$url.'"'
      .' type="application/x-shockwave-flash" allowscriptaccess="always"'
      .' allowfullscreen="true"'
      .' width="'.$width.'" height="'.$height.'"'
      .' flashvars="videoid='.$video_id.'">'
      .'</embed>'
      .'</object>'
;
  return $p;
}


function
widget_video_myspace ($video_id, $width=425, $height=360)
{
//  $video_id = 'k4H0eU9uhV7waa1XXp';
  $video_id = '6773592';
  $url = 'http://mediaservices.myspace.com/services/media/embed.aspx/m='.$video_id.',t=1,mt=video';

  $p = ''
      .'<object width="'.$width.'" height="'.$height.'">'
      .'<param name="allowFullScreen" value="true"/>'
      .'<param name="wmode" value="transparent"/>'
      .'<param name="movie" value="'.$url.'"/>'
      .'<embed'
      .' src="'.$url.'"'
      .' width="'.$width.'"'
      .' height="'.$height.'"'
      .' allowFullScreen="true"'
      .' type="application/x-shockwave-flash"'
      .' wmode="transparent"></embed>'
      .'</object>'
;
  return $p;
}


function
widget_video_veoh ($video_id, $width=410, $height=341)
{
  $url = 'http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.4.3.1014&permalinkId='
         .$video_id
         .'&player=videodetailsembedded&videoAutoPlay=0&id=anonymous';

  $p = ''
      .'<object width="'.$width.'" height="'.$height.'" id="veohFlashPlayer" name="veohFlashPlayer">'
      .'<param name="movie" value="'.$url.'"></param>'
      .'<param name="allowFullScreen" value="true"></param>'
      .'<param name="allowscriptaccess" value="always"></param>'
      .'<embed src="'.$url.'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$width.'" height="'.$height.'" id="veohFlashPlayerEmbed" name="veohFlashPlayerEmbed">'
      .'</embed>'
      .'</object>'
;
  return $p;
}


function
widget_video_google ($video_id, $width=400, $height=326)
{
  $url = 'http://video.google.com/googleplayer.swf?docid='.$video_id.'&fs=true';

  // original: 400x326
  $p .= '<embed id="VideoPlayback" src="'.$url.'"'
       .' style="width:'.$width.'px;height:'.$height.'px"'
       .' allowFullScreen="true"'
       .' allowScriptAccess="always"'
       .' type="application/x-shockwave-flash">'
       .'</embed>'
;
  return $p;
}


/*
function
widget_video_yahoo ($video_id, $width=512, $height=322)
{
// vid id
//http://espanol.video.yahoo.com/watch/5410123/14251443
//  $video_id = 'k4H0eU9uhV7waa1XXp';
  $video_id = '6773592';
  $video_vid = '6773592';
  $url = 'http://mediaservices.myspace.com/services/media/embed.aspx/m='.$video_id.',t=1,mt=video';

  $p = ''
      .'<object width="'.$width.'" height="'.$height.'">'
      .'<param name="movie" value="http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46" />'
      .'<param name="allowFullScreen" value="true" />'
      .'<param name="AllowScriptAccess" VALUE="always" />'
      .'<param name="bgcolor" value="#000000" />'
      .'<param name="flashVars"'
          .' value="id='.$id.'&vid='.$vid.'&lang=es-mx&intl=e1&thumbUrl=http%3A//l.yimg.com/a/p/i/bcst/videosearch/9707/88446579.jpeg&embed=1" />'
      .'<embed src="http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46" type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" allowFullScreen="true" AllowScriptAccess="always" bgcolor="#000000"'
      .' flashVars="id='.$id.'&vid='.$vid.'&lang=es-mx&intl=e1&thumbUrl=http%3A//l.yimg.com/a/p/i/bcst/videosearch/9707/88446579.jpeg&embed=1" >'
      .'</embed>'
      .'</object>'
;
  return $p;
}
*/


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

  return 0;
}


function
widget_media_fullscreen_js ()
{
  // javascript:getinnerwidth() and javascript:getinnerheight()

  $p = '<script type="text/javascript">'."\n"
      .'function getinnerwidth ()'."\n"
      .'  {'."\n"
      .'    var w = screen.width;'."\n"
      .'    if (self.innerWidth != undefined)'."\n"
      .'      w = self.innerWidth;'."\n"
      .'    else'."\n"
      .'      {'."\n"
      .'        var d = document.documentElement;'."\n"
      .'        if (d)'."\n"
      .'          w = d.clientWidth;'."\n"
      .'      }'."\n"
      .'    return w;'."\n"
      .'  }'."\n"
      ."\n\n"
      .'function getinnerheight ()'."\n"
      .'  {'."\n"
      .'    var h = screen.height;'."\n"
      .'    if (self.innerWidth != undefined)'."\n"
      .'        h = self.innerHeight;'."\n"
      .'    else'."\n"
      .'      {'."\n"
      .'        var d = document.documentElement;'."\n"
      .'        if (d)'."\n"
      .'          h = d.clientHeight;'."\n"
      .'      }'."\n"
      .'    return h;'."\n"
      .'  }'."\n"
      ."\n\n"
//      .'document.write (getinnerwidth ()+\' \'+getinnerheight ());'."\n"
      .'</script>';

  return $p;
}


function
widget_media ($media_url, $width = NULL, $height = NULL, $autoplay = 1, $hq = 1, $loop = 0)
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


 if ($fullscreen)
    {   
      $s .= '\');'
           ."\n\n"
           .'</script>'
;
    }


  return $s;
}


function
widget_media_playlist ($media_url, $width = NULL, $height = NULL, $autoplay = 1, $hq = 1, $loop = 0)
{
  return '';
}


function
widget_onhover_link ($url, $name, $image1, $image2)
{
  $p = '';
  $p .= '<a href="'.$url.'"'
       .' onmouseover="document.'.$name.'.src='.$image1.'"'
       .' onmouseout="document.'.$name.'.src='.$image2.'"'
       .'><img src="'.$image1.'" border="0" name="'.$name.'"></a>';

  return $p;
}


function
widget_window_open ($url, $fullscreen = 0, $window_name = '')
{
  $p = '';

  $p .= '<script type="text/javascript">'."\n";

  $p .= 'function widget_window_open ()'."\n"
       .'{'."\n";

//  $p .= 'var w=screen.width;var h=screen.height;';

//  $p .= 'var win=';

  $p .= 'window.open(\''
       .$url
       .'\',\''
       .str_replace ("'", "\'", $window_name)
       .'\',\'';

// https://developer.mozilla.org/en/Gecko_DOM_Reference
// https://developer.mozilla.org/en/DOM/window.open
  if ($fullscreen)
    $p .= ''
         .'top=0'
         .',left=0'
//         .',width=\'+w+\''
//         .',height=\'+h+\''
         .',fullscreen'
//         .',menubars'
         .',status=0'
//         .',toolbar'
         .',location=0'
//         .',menubar=no'
//         .',directories=no'
//         .',resizable=no'
//         .',scrollbars=no'
//         .',copyhistory'
;
  else
    $p .= ''
         .'width=400'
         .',height=300'
         .',status=no'
         .',toolbar=no'
         .',location=no'
         .',menubar=no'
         .',directories=no'
         .',resizable=yes'
         .',scrollbars=yes'
         .',copyhistory=yes'
;
  $p .= '\').focus();'."\n";

//  $p .= 'window.opener = top;'."\n"; // this will close opener in ie only (not Firefox)

  if ($fullscreen)
  $p .= 'window.moveTo(0,0);'."\n"
       ."\n"
       .'// changing bar states on the existing window)'."\n"
//       .'netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserWrite");'."\n"
       .'window.locationbar.visible=0;'."\n"
       .'window.statusbar.visible=0;'."\n"
       ."\n"
       .'if (document.all)'."\n"
       .'  window.resizeTo(screen.width, screen.height);'."\n"
       ."\n"
       .'else if (document.layers || document.getElementById)'."\n"
       .'  if (window.outerHeight < screen.height || window.outerWidth < screen.width)'."\n"
       .'    {'."\n"
       .'      window.outerHeight = screen.height;'."\n"
       .'      window.outerWidth = screen.width;'."\n"
       .'    }'."\n"
;

  $p .= '}'."\n";

  $p .= '</script>';

  return $p;
}


function
widget_carousel ($xmlfile, $width=200, $height=150)
{
  $p = ''
      .'<span class="carousel_container">'
      .'<span id="carousel1">'
      .'</span>'
      .'</span>'
      .'<script type="text/javascript" src="misc/swfobject.js"></script>'
      .'<script type="text/javascript">'."\n"
      .'swfobject.embedSWF ('."\n"
      .'  "misc/carousel.swf",'."\n"
      .'  "carousel1",'."\n"
      .'  "'.$width.'", "'.$height.'",'."\n"
      .'  "9.0.0",'."\n"
      .'  false,'."\n"
      .'  {'."\n"
      .'    xmlfile:"'.$xmlfile.'",'."\n"
      .'    loaderColor:"0xffffff",'."\n"
      .'    messages:"  ::  ::  ::  "'."\n"
      .'  },'."\n"
      .'  {bgcolor: "#ffffff"});'."\n"
      .'</script>';

  echo $p;
}


function
widget_trace ($ip)
{
// shows google maps by ip(geoip?), country, city, or long/lat
//http://maps.google.com/?ie=UTF8&ll=37.0625,-95.677068&spn=31.013085,55.634766&t=h&z=4
}


/*
  In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
  of $_FILES. Use phpversion() for version information.

  $_FILES['userfile']['name']
    The original name of the file on the client machine. 
  $_FILES['userfile']['type']
    The mime type of the file, if the browser
    provided this information. An example would be "image/gif". This mime
    type is however not checked on the PHP side and therefore don't take its
    value for granted.
  $_FILES['userfile']['size']
    The size, in bytes, of the uploaded file. 
  $_FILES['userfile']['tmp_name']
    The temporary filename of the file in which the uploaded file was stored on the server. 
  $_FILES['userfile']['error']
    The error code associated with this file upload. This element was added in PHP 4.2.0 

  UPLOAD_ERR_OK          0; There is no error, the file uploaded with success. 
  UPLOAD_ERR_INI_SIZE    1; The uploaded file exceeds the upload_max_filesize directive in php.ini. 
  UPLOAD_ERR_FORM_SIZE   2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form. 
  UPLOAD_ERR_PARTIAL     3; The uploaded file was only partially uploaded. 
  UPLOAD_ERR_NO_FILE     4; No file was uploaded. 
  UPLOAD_ERR_NO_TMP_DIR  6; Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3. 
  UPLOAD_ERR_CANT_WRITE  7; Failed to write file to disk. Introduced in PHP 5.1.0. 
  UPLOAD_ERR_EXTENSION   8; File upload stopped by extension. Introduced in PHP 5.2.0. 

  related php.ini settings
    if (post_max_size > upload_max_filesize) in php.ini
      otherwise you will not be able to report the correct error in case of a
      too big upload ! Also check the max-execution-time (upload-time could be
      added to execution-time)

    if (post >post_max_size) in php.ini
      $_FILES and $_POST will return empty

  The data encoding type, enctype, MUST be specified as enctype="multipart/form-data"
  MAX_FILE_SIZE must precede the file input field
  Name of input element determines name in $_FILES array
*/
function
widget_upload ($upload_path, $max_file_size, $mime_type, $submit_button_html, $uploaded_html)
{
  $debug = 0;
  $p = '';

  if (!$_FILES)
    return '<form action="'
      .$_SERVER['PHP_SELF']
      .'" method="POST" enctype="multipart/form-data"'
//      .' style="margin:0;"'
      .' style="display:inline;"'
      .'>'
      .'<input type="hidden" name="MAX_FILE_SIZE" value="'
      .$max_file_size
      .'">'
      .'<input type="file"'
      .' name="widget_upload"'
//      .' title="'
//      .$tooltip
//      .'"'
      .($max_file_size ? ' maxlength="'.$max_file_size.'"' : '')
      .($mime_type ? ' accept="'.$mime_type.'"' : '')
      .'>'
      .($submit_button_html ? $submit_button_html :
       '<input type="submit" name="widget_upload" value="Upload"'
//      .' tooltip="'
//      .$tooltip
//      .'"'
)
      .'>'
      .'</form>'
;

  if ($debug)
    {
      $p .= '<pre><tt>'
           .sprint_r ($_FILES);
    }

  $d = $upload_path.'/'
//      .str_replace (' ', '_', basename($_FILES['widget_upload']['name']));
      .basename($_FILES['widget_upload']['name']);

  if (file_exists ($d))
    $p .= 'ERROR: file already exists';
  else if (move_uploaded_file ($_FILES['widget_upload']['tmp_name'], $d) == FALSE)
    $p .= 'ERROR: move_uploaded_file() failed';

  $s = Array (
           UPLOAD_ERR_OK =>         'OK',
           UPLOAD_ERR_INI_SIZE =>   'The uploaded file exceeds the upload_max_filesize directive ('
                                   .ini_get ('upload_max_filesize')
                                   .') in php.ini',
           UPLOAD_ERR_FORM_SIZE =>  'The uploaded file exceeds the MAX_FILE_SIZE directive ('
                                   .$max_file_size
                                   .') that was specified in the HTML form',
           UPLOAD_ERR_PARTIAL =>    'The uploaded file was only partially uploaded',
           UPLOAD_ERR_NO_FILE =>    'No file was uploaded',
           UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
//           UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
//           UPLOAD_ERR_EXTENSION =>  'File upload stopped by extension'
         );

  if (!empty ($_FILES['widget_upload']) &&
      $_FILES['widget_upload']['error'] == UPLOAD_ERR_OK)
    {
      $p .= $uploaded_html;
    }
  else
    {
      $e = $s[$_FILES['widget_upload']['error']];
      if (!$e)
        $e .= 'An unknown error occured';
      $p .= 'ERROR: '.$e;
    }

  if ($debug)
    {
      $p .= '<pre><tt>'
           .sprint_r ($s)
           .sprint_r ($_FILES);
    }

  return $p;
}


function
widget_captcha ($captcha_path)
{
  global $tv2_root;

  // use random captcha image
  if (!($handle = opendir ($tv2_root.'/'.$captcha_path)))
    return 'ERROR: problem with CAPTCHA';

  $a = array ();
  $count = 0;
  while (false !== ($p = readdir ($handle)))
    if (get_suffix ($p) == '.jpg')
      $a[$count++] = $p;

  closedir ($handle);

  srand (microtime () * time ());
  $r = rand (0, sizeof ($a) - 1);

  $captcha_md5 = set_suffix ($a[$r], '');
  $widget_captcha_key = md5 ($captcha_md5.$_SERVER['REMOTE_ADDR']); // key
  $img = $captcha_path.'/'.$captcha_md5.'.jpg'; // image name is md5 of the captcha in the image

  $p = '';
  $p .= '<input type="hidden" name="widget_captcha_key" value="'.$widget_captcha_key.'">';
  $p .= '<img src="'.$img.'" border="0" title="enter this CAPTCHA in the field to the right">';
  $p .= '<input type="text" size="3" maxsize="3" name="widget_captcha" title="enter the CAPTCHA you see left from here">';

  return $p;
}


function
widget_captcha_check ()
{
  $widget_captcha = get_request_value ('widget_captcha');
  $widget_captcha_key = get_request_value ('widget_captcha_key');

  // DEBUG
//  echo md5 (md5 ($widget_captcha).$_SERVER["REMOTE_ADDR"]).' == '.$widget_captcha_key.'<br>';

  if (md5 (md5 ($widget_captcha).$_SERVER["REMOTE_ADDR"]) == $widget_captcha_key)
    return TRUE;
  return FALSE;
}


/*
function
widget_table ($title_array, $content_array)
{
  // $cols == number of titles in $title_array 
  $cols = sizeof ($title_array);
  $rows = $cols * 0.5;

  $p = '';

  $p .= '<table class="widget_table" border="0" cellpadding="1" cellspacing="0">';

  // titles
  $p .= '<tr class="widget_table_title">';
  for ($i = 0; $title_array[$i]; $i++)
    $p .= '<td class="widget_table_td">'.$title_array[$i].'</td>';
  $p .= '</tr>';

  // content
  for ($i = 0; $i < $rows; $i++)
    {
      $p .= '<tr class="widget_table_tr'.(($i & 1) + 1).'">';
      for ($j = $rows * $cols; $j < $cols; $j++)
        $p .= '<td class="widget_table_td">'.$content_array[$j].'</td>';
      $p .= '</tr>';
    }

  $p .= '</table>';

  return $p;
}
*/


/*
0  	dev  	device number
1 	ino 	inode number *
2 	mode 	inode protection mode
3 	nlink 	number of links
4 	uid 	userid of owner *
5 	gid 	groupid of owner *
6 	rdev 	device type, if inode device
7 	size 	size in bytes
8 	atime 	time of last access (Unix timestamp)
9 	mtime 	time of last modification (Unix timestamp)
10 	ctime 	time of last inode change (Unix timestamp)
11 	blksize 	blocksize of filesystem IO **
12 	blocks 	number of blocks allocated **

* On Windows this will always be 0.

** Only valid on systems supporting the st_blksize type - other systems (e.g. Windows) return -1.

In case of error, stat() returns FALSE
*/


/*
function
widget_index_sort_time ($a, $b)
{
  if ($a[3] == $b[3])
    return 0;
  return ($a[3] < $b[3]) ? 1 : -1;
}


function
widget_index_tree ($name, $path, $mime_type, $flags)
{
  $p = '';
  $dir = opendir ($path);
  while (($file = readdir ($dir)) != false)
    {
      if (is_dir ($file))
        {
          $p .= '<img src="images/widget_tree_closed.png" border="0" alt="images/widget_tree_open.png">'
                 .basename ($file);
        }
      else if (is_file ($file))
        {
          $stat = stat ($file);
          $p .= '<img src="images/widget_tree_file.png" border="0" alt="images/widget_tree_file.png">'
               .basename ($file)
               .$stat['size'];
        }
      else // ?
        {
          $p .= '<img src="images/widget_tree_file.png" border="0" alt="images/widget_tree_file.png">'
               .basename ($file);
        }

      $p .= '<br>'."\n";
    }
  closedir ($dir);

  return $p;
}
*/


/*
function
widget_index_func ($b)
{
// DEBUG
//  return '<pre><tt>'.sprint_r ($b).'</tt></pre>';  

  $p = '<table><tr>';
  $j = sizeof ($b);
  for ($i = 0; $i < $j; $i++)
    {
      if ($i > 0)
        $p .= '</tr><tr>';

      $p .= '<td>';

      $p .= '<a href="'
           .$b[$i][0].$b[$i][1]
           .'">'
           .$b[$i][1]
           .'</a>';

      $p .= '</td><td>';

      $p .= $b[$i][2];

      $p .= '</td><td>';

      $p .= strftime ("%a %d-%b-%y %T %Z", $b[$i][3]);

      $p .= '</td><td>';

      if ($b[$i][5] == '.flv' || $b[$i][5] == '.mp4')
        $p .= '<a href="?video='.$b[$i][1].'">Play</a>';
      else if ($b[$i][5] == '.mp3')
        $p .= '<a href="?audio='.$b[$i][1].'">Play</a>';

      $p .= '</td>';
    }

  $p .= '</tr></table>';

  return $p;
}


function
widget_index ($dir, $recursive, $suffix, $index_func)
{
  // TODO: make recursive work
  $recursive = 0;

  // cached
//  static $b = array (array ());
//  if ($b)
//    if ($b[0])
//      if ($b[0][0])
//        return $b;
  $b = array (array ());
 
  $a = array ();
  $a = scandir ($dir, 0);

  // read filenames, sizes and mtime
  $i_max = sizeof ($a);
  for ($i = 0, $j = 0; $i < $i_max; $i++)
    {
      $dirname = str_replace ('//', '/', $dir.'/');
      $basename = str_replace ("\n", '', $a[$i]);
      $path = $dirname.'/'.$basename;

      if (!file_exists ($path))
        continue;

      if ($basename == '.' || $basename == '..')
        continue;

      if (!is_file ($path) && !($recursive && is_dir ($path)))
        continue;

      if ($suffix)
        if (get_suffix ($basename) != $suffix)
          continue;

      $b[$j][0] = $dirname;
      $b[$j][1] = $basename;

      $s = stat ($path);
      $b[$j][2] = $s['size'];
      $b[$j][3] = $s['mtime'];
      $b[$j][4] = is_file ($path) ? '1' : '0';
      $b[$j][5] = get_suffix ($path);

      $j++;
    }

  // sort by date or size or suffix
  usort ($b, 'widget_index_sort_time');

  return $index_func ? $index_func ($b) : widget_index_func ($b);
}
*/


// widget_relate() flags
//define ('WIDGET_RELATE_BOOKMARK',    1<<6);  // browser bookmark
//define ('WIDGET_RELATE_STARTPAGE',   1<<7);  // use as start page
//define ('WIDGET_RELATE_SEARCH',      1<<8);  // add search plugin to browser
//define ('WIDGET_RELATE_LINKTOUS',    1<<9);  // link-to-us code for link sections of other sites
define ('WIDGET_RELATE_TELLAFRIEND', 1<<10); // send tell-a-friend email
define ('WIDGET_RELATE_SBOOKMARKS',  1<<11); // social bookmarks
//define ('WIDGET_RELATE_DIGGTHIS',    1<<12);
//define ('WIDGET_RELATE_DONATE',      1<<13); // donate button (paypal, etc..)
//define ('WIDGET_RELATE_RSSFEED',     1<<14); // generate RSS feed
define ('WIDGET_RELATE_ALL',
//                                     WIDGET_RELATE_BOOKMARK|
//                                     WIDGET_RELATE_STARTPAGE|
//                                     WIDGET_RELATE_SEARCH|
//                                     WIDGET_RELATE_LINKTOUS|
                                     WIDGET_RELATE_TELLAFRIEND|
                                     WIDGET_RELATE_SBOOKMARKS // |
//                                     WIDGET_RELATE_DIGGTHIS|
//                                     WIDGET_RELATE_DONATE|
//                                     WIDGET_RELATE_RSSFEED
);


function
widget_relate ($title, $url, $rss_feed_url, $vertical, $flags)
{
  $p = '';
//  $p .= '<table border="0" cellpadding="0" cellspacing="0" style="background-color:#fff;">'
//       .'<tr><td>';
//  $p .= '<font size="-1" face="arial,sans-serif">';

  $title = trim ($title);
  $url = trim ($url);
  $lf = $vertical ? '<br>' : ' ';

/*
  // digg this button
  if ($flags & WIDGET_RELATE_DIGGTHIS)
    $p .= '<script><!--'."\n"
         .'digg_url = \''
         .$url
         .'\';'
         .'//--></script>'
         .'<script type="text/javascript" src="http://digg.com/api/diggthis.js"></script>'
         .$lf;

  // donate
  if ($flags & WIDGET_RELATE_DONATE)
    $p .= '<img class="widget_relate_img" src="images/widget_relate_paypal.png" border="0">'
         .'<a class="widget_relate_label" href="http://paypal.com">Donate</a>'
.'<pre>* * *   D O N A T I O N S   A R E   A C C E P T E D   * * *</pre><br>'
.'<br>'
.'<img src="images/widget_relate_refrigator.jpg" border="0"><br>'
.'<br>'
.'Individuals and companies can now donate funds to support me and keep me from<br>'
.'writing proprietary software.<br>'
.'<br>'
.'Thank You!<br>'
.'<br>'
.'<pre>* * *   D O N A T I O N S   A R E   A C C E P T E D   * * *</pre><br-->'
.'search widget to include in other pages'
         .$lf;

  // link-to-us code for link sections of other sites
  if ($flags & WIDGET_RELATE_LINKTOUS)
    $p .= '<textarea title="Add this code to your blog or website">Link to us</textarea>'
         .$lf;
*/

  if ($flags & WIDGET_RELATE_TELLAFRIEND)
    $p .= '<img class="widget_relate_img" src="images/widget_relate_tellafriend.png" border="0">'
         .'<a class="widget_relate_label" href="mailto:?body='
         .$url
         .'&subject='
         .$title
         .'"'
         .' title="Send this link to your friends">Tell a friend</a>'
         .$lf;
/*
  // add browser bookmark
  if ($flags & WIDGET_RELATE_BOOKMARK)
    $p .= '<img class="widget_relate_img" src="images/widget_relate_star.png" border="0">'
         .'<a class="widget_relate_label"'
         .' href="javascript:js_bookmark (\''
         .$url
         .'\', \''
         .$title
         .'\');"'
         .' border="0">Bookmark</a>'
         .$lf;

  // use as startpage
  if ($flags & WIDGET_RELATE_STARTPAGE)
    $p .= '<img class="widget_relate_img" src="images/widget_relate_home.png" border="0">'
         .'<a class="widget_relate_label"'
         .' href="http://"'
         .' onclick="this.style.behavior=\'url(#default#homepage)\';this.setHomePage(\'http://torrent-finder.com\');"'
         .'>'
         .'Make us your start page</a>'
         .$lf;

  // add search plugin to browser
  if ($flags & WIDGET_RELATE_SEARCH)
    $p .= '<img class="widget_relate_img" src="images/widget_relate_search.png" border="0">'
         .'<a class="widget_relate_label"'
         .' href="http://'
//         .' href=\"javascript:js_bookmark('"
//         .$title
//         .'\', \''
//         .$url
//         .'\')"'
         .' border="0">Add search</a>'
         .$lf;

  // generate rss feed
  if ($flags & WIDGET_RELATE_RSSFEED)
    $p .= '<img class="widget_relate_img" src="images/widget_relate_rss.png" border="0">'
         .'<a class="widget_relate_label"'
         .' href="'
         .$rss_feed_url
         .'"'
         .' border="0">RSS feed</a>'
         .$lf;
*/

  // social bookmarks
  if ($flags & WIDGET_RELATE_SBOOKMARKS)
    {
      $a = array (
//        array ('30 Day Tags',		'30_day_tags.png', NULL, NULL),
//        array ('AddToAny',		'addtoany.png', NULL, NULL),
//        array ('Ask',			'ask.png', NULL, NULL),
//        array ('BM Access',		'bm_access.png', NULL, NULL),
        array ('Backflip',		'backflip.png', 'http://www.backflip.com/add_page_pop.ihtml?url=', '&title='),
//        array ('BlinkBits',		'blinkbits.png', 'http://www.blinkbits.com/bookmarklets/save.php?v=1&source_url=', '&title='),
        array ('BlinkBits',		'blinkbits.png', 'http://www.blinkbits.com/bookmarklets/save.php?v=1&source_image_url=&rss_feed_url=&rss_feed_url=&rss2member=&body=&source_url=', '&title='),
        array ('Blinklist',		'blinklist.png', 'http://www.blinklist.com/index.php?Action=Blink/addblink.php&Description=&Tag=&Url=', '&Title='),
//        array ('Bloglines',		'bloglines.png', NULL, NULL),
        array ('BlogMarks',		'blogmarks.png', 'http://blogmarks.net/my/new.php?mini=1&simple=1&url=', '&content=&public-tags=&title='),
//        array ('BlogMarks',		'blogmarks.png', 'http://blogmarks.net/my/new.php?mini=1&simple=1&url=', '&title='),
        array ('Blogmemes',		'blogmemes.png', 'http://www.blogmemes.net/post.php?url=', '&title='),
//        array ('Blue Dot',		'blue_dot.png', NULL, NULL),
        array ('Buddymarks',		'buddymarks.png', 'http://buddymarks.com/s_add_bookmark.php?bookmark_url=', '&bookmark_title='),
//        array ('CiteULike',		'citeulike.png', NULL, NULL),
        array ('Complore',		'complore.png', 'http://complore.com/?q=node/add/flexinode-5&url=', '&title='),
//        array ('Connotea',		'connotea.png', NULL, NULL),
        array ('Del.icio.us',		'del.icio.us.png', 'http://del.icio.us/post?v=2&url=', '&notes=&tags=&title='),
//        array ('Del.icio.us',		'del.icio.us.png', 'http://del.icio.us/post?v=2&url=', '&title='),
//        array ('Del.icio.us',		'del.icio.us.png', 'http://del.icio.us/post?url=', '&title='),
        array ('De.lirio.us',		'de.lirio.us.png', 'http://de.lirio.us/bookmarks/sbmtool?action=add&address=', '&title='),
        array ('Digg',			'digg.png', 'http://digg.com/submit?phase=2&url=', '&bodytext=&tags=&title='),
//        array ('Digg',		'digg.png', 'http://digg.com/submit?phase=2&url=', '&title='),
        array ('Diigo',			'diigo.png', 'http://www.diigo.com/post?url=', '&tag=&comments=&title='),
//        array ('Dogear',		'dogear.png', NULL, NULL),
//        array ('Dotnetkicks',		'dotnetkicks.png', 'http://www.dotnetkicks.com/kick/?url=', '&title='),
//        array ('Dude, Check This Out',	'dude_check_this_out.png', NULL, NULL),
//        array ('Dzone',		'dzone.png', NULL, NULL),
//        array ('Eigology',		'eigology.png', NULL, NULL),
        array ('Fark',			'fark.png', 'http://cgi.fark.com/cgi/fark/edit.pl?new_url=', '&title='),
//        array ('Favoor',		'favoor.png', NULL, NULL),
//        array ('FeedMeLinks',		'feedmelinks.png', NULL, NULL),
//        array ('Feedmarker',		'feedmarker.png', NULL, NULL),
        array ('Folkd',			'folkd.png', 'http://www.folkd.com/submit/', NULL),
//        array ('Freshmeat',		'freshmeat.png', NULL, NULL)
        array ('Furl',			'furl.png', 'http://www.furl.net/storeIt.jsp?u=', '&keywords=&t='),
//        array ('Furl',		'furl.png', 'http://www.furl.net/storeIt.jsp?u=', '&t='),
//        array ('Furl',		'furl.png', 'http://www.furl.net/store?s=f&to=0&u=', '&ti='),
//        array ('Givealink',		'givealink.png', NULL, NULL),
        array ('Google',		'google.png', 'http://www.google.com/bookmarks/mark?op=add&hl=en&bkmk=', '&annotation=&labels=&title='),
//        array ('Google',		'google.png', 'http://www.google.com/bookmarks/mark?op=add&bkmk=', '&title='),
//        array ('Humdigg',		'humdigg.png', NULL, NULL),
//        array ('HLOM (Hyperlinkomatic)',		'hlom.png', NULL, NULL),
//        array ('I89.us',		'i89.us.png', NULL, NULL),
        array ('Icio',			'icio.png', 'http://www.icio.de/add.php?url=', NULL),
//        array ('Igooi',		'igooi.png', NULL, NULL),
//        array ('Jots',		'jots.png', NULL, NULL),
//        array ('Link Filter',		'link_filter.png', NULL, NULL),
//        array ('Linkagogo',		'linkagogo.png', NULL, NULL),
        array ('Linkarena',		'linkarena.png', 'http://linkarena.com/bookmarks/addlink/?url=', '&desc=&tags=&title='),
//        array ('Linkatopia',		'linkatopia.png', NULL, NULL),
//        array ('Linklog',		'linklog.png', NULL, NULL),
//        array ('Linkroll',		'linkroll.png', NULL, NULL),
//        array ('Listable',		'listable.png', NULL, NULL),
//        array ('Live',		'live.png', 'https://favorites.live.com/quickadd.aspx?marklet=1&mkt=en-us&url=', '&title='),
//        array ('Lookmarks',		'lookmarks.png', NULL, NULL),
        array ('Ma.Gnolia',		'ma.gnolia.png', 'http://ma.gnolia.com/bookmarklet/add?url=', '&description=&tags=&title='),
//        array ('Ma.Gnolia',		'ma.gnolia.png', 'http://ma.gnolia.com/bookmarklet/add?url=', '&title='),
//        array ('Maple',		'maple.png', NULL, NULL),
//        array ('MrWong',		'mrwong.png', NULL, NULL),
//        array ('Mylinkvault',		'mylinkvault.png', NULL, NULL),
        array ('Netscape',		'netscape.png', 'http://www.netscape.com/submit/?U=', '&T='),
        array ('NetVouz',		'netvouz.png', 'http://netvouz.com/action/submitBookmark?url=', '&popup=yes&description=&tags=&title='),
//        array ('NetVouz',		'netvouz.png', 'http://netvouz.com/action/submitBookmark?url=', '&title='),
        array ('Newsvine',		'newsvine.png', 'http://www.newsvine.com/_tools/seed&save?u=', '&h='),
//        array ('Newsvine',		'newsvine.png', 'http://www.newsvine.com/_wine/save?popoff=1&u=', '&tags=&blurb='),
//        array ('Nextaris',		'nextaris.png', NULL, NULL),
//        array ('Nowpublic',		'nowpublic.png', NULL, NULL),
//        array ('Oneview',		'oneview.png', 'http://beta.oneview.de:80/quickadd/neu/addBookmark.jsf?URL=', '&title='),
//        array ('Onlywire',		'onlywire.png', NULL, NULL),
//        array ('Pligg',		'pligg.png', NULL, NULL),
//        array ('Portachi',		'portachi.png', NULL, NULL),
//        array ('Protopage',		'protopage.png', NULL, NULL),
        array ('RawSugar',		'rawsugar.png', 'http://www.rawsugar.com/pages/tagger.faces?turl=', '&tttl='),
        array ('Reddit',		'reddit.png', 'http://reddit.com/submit?url=', '&title='),
//        array ('Rojo',		'rojo.png', NULL, NULL),
        array ('Scuttle',		'scuttle.png', 'http://www.scuttle.org/bookmarks.php/maxpower?action=add&address=', '&description='),
//        array ('Searchles',		'searchles.png', NULL, NULL),
        array ('Shadows',		'shadows.png', 'http://www.shadows.com/features/tcr.htm?url=', '&title='),
//        array ('Shadows',		'shadows.png', 'http://www.shadows.com/bookmark/saveLink.rails?page=', '&title='),
//        array ('Shoutwire',		'shoutwire.png', NULL, NULL),
        array ('Simpy',			'simpy.png', 'http://simpy.com/simpy/LinkAdd.do?href=', '&tags=&note=&title='),
//        array ('Simpy',		'simpy.png', 'http://simpy.com/simpy/LinkAdd.do?href=', '&title='),
        array ('Slashdot',		'slashdot.png', 'http://slashdot.org/bookmark.pl?url=', '&title='),
        array ('Smarking',		'smarking.png', 'http://smarking.com/editbookmark/?url=', '&tags=&description='),
//        array ('Spurl',		'spurl.png', 'http://www.spurl.net/spurl.php?url=', '&title='),
        array ('Spurl',			'spurl.png', 'http://www.spurl.net/spurl.php?v=3&tags=&url=', '&title='),
//        array ('Spurl',		'.png', 'http://www.spurl.net/spurl.php?v=3&url=', '&title='),
//        array ('Squidoo',		'squidoo.png', NULL, NULL),
        array ('StumbleUpon',		'stumbleupon.png', 'http://www.stumbleupon.com/submit?url=', '&title='),
//        array ('Tabmarks',		'tabmarks.png', NULL, NULL),
//        array ('Taggle',		'taggle.png', NULL, NULL),
//        array ('Tag Hop',		'taghop.png', NULL, NULL),
//        array ('Taggly',		'taggly.png', NULL, NULL),
//        array ('Tagtooga',		'tagtooga.png', NULL, NULL),
//        array ('TailRank',		'tailrank.png', NULL, NULL),
        array ('Technorati',		'technorati.png', 'http://technorati.com/faves?tag=&add=', NULL),
//        array ('Technorati',		'technorati.png', 'http://technorati.com/faves?add=', '&title='),
//        array ('Tutorialism',		'tutorialism.png', NULL, NULL),
//        array ('Unalog',		'unalog.png', NULL, NULL),
//        array ('Wapher',		'wapher.png', NULL, NULL),
        array ('Webnews',		'webnews.png', 'http://www.webnews.de/einstellen?url=', '&title='),
//        array ('Whitesoap',		'whitesoap.png', NULL, NULL),
//        array ('Wink',		'wink.png', NULL, NULL),
//        array ('WireFan',		'wirefan.png', NULL, NULL),
        array ('Wists',			'wists.png', 'http://wists.com/r.php?c=&r=', '&title='),
//        array ('Wists',		'wists.png', 'http://www.wists.com/?action=add&url=', '&title='),
        array ('Yahoo',			'yahoo.png', 'http://myweb2.search.yahoo.com/myresults/bookmarklet?u=', '&d=&tag=&t='),
//        array ('Yahoo',		'yahoo.png', 'http://myweb2.search.yahoo.com/myresults/bookmarklet?u=', '&t='),
//        array ('Yahoo',		'yahoo.png', 'http://myweb.yahoo.com/myresults/bookmarklet?u=', '&t='),
        array ('Yigg',			'yigg.png', 'http://yigg.de/neu?exturl=', NULL),
//        array ('Zumaa',		'zumaa.png', NULL, NULL),
//        array ('Zurpy',		'zurpy.png', NULL, NULL),
      );

      $i_max = sizeof ($a);
      for ($i = 0; $i < $i_max; $i++)
        $p .= '<a class="widget_relate_img" href="'
             .$a[$i][2]
             .urlencode ($url)
             .($a[$i][3] ? $a[$i][3].urlencode ($title) : '')
             .'" alt="Add to '
             .$a[$i][0]
             .'" title="Add to '
             .$a[$i][0]
             .'">'
             .'<img src="images/widget_relate_'
             .$a[$i][1]
             .'" border="0"></a>';

      $p .= $lf;
    }

  return $p; 
}


/*
function
widget_panel ($url_array, $img_array, $w, $h, $tooltip)
{
?>
<script type="text/javascript">
<!--

//var test_array = new array  (<?php

$p = "";
$i_max = sizeof ($img_array);  
for ($i = 0; $i < $i_max; $i++)
  {
    if ($i)
      $p .= ", ";
    $p .= "widget_panel_".$i;
  }

echo $p;
?>);

var img_w = <?php echo $w; ?>;
var img_h = <?php echo $h; ?>;
var img_n = <?php echo sizeof ($img_array); ?>;


function
js_panel_get_img_array ()
{
  var img = new array (<?php

$p = '';
$i_max = sizeof ($img_array);
for ($i = 0; $i < $i_max; $i++)
  {
    if ($i)
      $p .= ', ';
    $p .= 'widget_panel_'.$i;
  }

echo $p;

?>);
  return img;
}

//-->
</script><?

  $p = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n"
      ."<tr>\n"
      ."    <td height=\"10\" colspan=\"4\" onMouseOver=\"js_mouse_callback_func (js_panel_event_ignore);\">\n"
      ."    </td> \n"
      ."  </tr>\n"
      ."  <tr>\n"
      ."    <td width=\"10\" height=\"140\" valign=\"bottom\" onMouseOver=\"js_mouse_callback_func (js_panel_event_ignore);\">\n"
      ."    </td>\n"
      ."    <td width=\"14%\" valign=\"bottom\" onMouseOver=\"js_mouse_callback_func (js_panel_event);\">\n"
      ."    </td>\n"
      ."    <td width=\"86%\" valign=\"bottom\" onMouseOver=\"js_mouse_callback_func (js_panel_event);\">\n"
      ."<nobr>\n";

  $i_max = min (sizeof ($url_array), sizeof ($img_array));
  for ($i = 0; $i < $i_max; $i++)
    $p .= "<a href=\""
         .$url_array[$i]
         ."\" target=\"_blank\"><img name=\"widget_panel_"
         .$i
         ."\" src=\""
         .$img_array[$i]
         ."\" width=\""
         .$w
         ."\" height=\""
         .$h
         ."\" border=\"0\"></a>\n";

  $p .= "</nobr>\n"
       ."    </td>\n"
       ."    <td width=\"10\" valign=\"bottom\" onMouseOver=\"js_mouse_callback_func (js_panel_event_ignore);\">\n"
       ."    </td>\n"
       ."  </tr>\n"
       ."  <tr>\n"
       ."    <td height=\"10\" colspan=\"4\" onMouseOver=\"js_mouse_callback_func (js_panel_event_ignore);\">\n"
       ."    </td> \n"
       ."  </tr>\n"
       ."</table>\n";

  return $p;
}
*/


function
widget_adsense ($client, $type, $border_color, $flags)
{
/*

"text_image"
"text"
"image"

<option value="728x90"> 728 x 90 Leaderboard 
<option value="468x60"> 468 x 60 Banner 
<option value="234x60"> 234 x 60 Half Banner 
<option value="120x600"> 120 x 600 Skyscraper 
<option value="160x600"> 160 x 600 Wide Skyscraper 
<option value="120x240"> 120 x 240 Vertical Banner 
<option value="336x280"> 336 x 280 Large Rectangle 
<option value="300x250"> 300 x 250 Medium Rectangle 
<option value="250x250"> 250 x 250 Square 
<option value="200x200"> 200 x 200 Small Square 
<option value="180x150"> 180 x 150 Small Rectangle 
<option value="125x125"> 125 x 125 Button 

format:
WxH_as

<option value="728x15"> 728 x 15 
<option value="468x15"> 468 x 15 
<option value="200x90"> 200 x 90 
<option value="180x90"> 180 x 90 
<option value="160x90"> 160 x 90 
<option value="120x90"> 120 x 90 

format:
WxH_0ads_al (4 lines)
WxH_0ads_al_s (5 lines)

*/

  $p = explode ("x", $flags, 2);
  $w = $p[0];
  $p = explode ("_", $p[1], 2);
  $h = $p[0];

  return "<script type=\"text/javascript\"><!--\n"
        ."google_ad_client = \""
        .$client
        ."\";\n"
        ."google_ad_width = "
        .$w
        .";\n"
        ."google_ad_height = "
        .$h
        .";\n"
        ."google_ad_format = \""
        .$flags
        ."\";\n"
        .($type ? "google_ad_type = \"".$type."\";\n" : "")
        ."google_ad_channel = \"\";\n"
        ."google_color_border = \""
        .$border_color
        ."\";\n"
        ."//google_color_bg = \"000000\";\n"
        ."//google_color_link = \"0000EF\";\n"
        ."//google_color_text = \"FFFFFF\";\n"
        ."//google_color_url = \"FFFFFF\";\n"
        ."//-->\n"
        ."</script>\n"
        ."<script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">"
        ."</script>\n";
}


/*
function
widget_adsense2 ($client, $w, $h, $border_color, $flags)
{
  // sizes in w and h
  $size = array (
      728 => 90,
      468 => 60,
      728 => 90,
      468 => 60,
      234 => 60,
      120 => 600,
      160 => 600,
      120 => 240,
      336 => 280,
      300 => 250,
      250 => 250,
      200 => 200,
      180 => 150,
      125 => 125
    );

  return "<script type=\"text/javascript\"><!--\n"
        ."google_ad_client = \""
        .$client
        ."\";\n"
        ."google_ad_width = "
        .$w
        .";\n"
        ."google_ad_height = "
        .$h
        .";\n"
        ."google_ad_format = \"".$w."x".$h."_as\";\n"
        .($flags ? "google_ad_type = \"".$flags."\";\n" : "")
        ."google_ad_channel = \"\";\n"
        ."google_color_border = \""
        .$border_color
        ."\";\n"
        ."//google_color_bg = \"000000\";\n"
        ."//google_color_link = \"0000EF\";\n"
        ."//google_color_text = \"FFFFFF\";\n"
        ."//google_color_url = \"FFFFFF\";\n"
        ."//-->\n"
        ."</script>\n"
        ."<script type=\"text/javascript\" src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">"
        ."</script>\n";
}
*/

}

?>