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
include_once ('misc/misc.php');
include_once ('misc/widget.php');


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
//       .'&ap=%2526fmt%3D'.($hq ? 18 : 5) // high(er) quality?
       .'&showsearch=0'     // no search
       .'&rel=0'            // no related
//       .($autoplay ? '&autoplay=1' : '')
//       .($loop ? '&loop=1' : '')
//       .'&color1=0x000000'
//       .'&color2=0x000000'
//       .'#t=03m22s'         // skip to
//       .'&start=30'         // skip to (2)
;

  $p = ''
      .'<object width="'.$width.'" height="'.$height.'">'
      .'<param name="movie" value="'.$url.'"></param>'
      .'<param name="allowFullScreen" value="true"></param>'
      .'<param name="allowscriptaccess" value="always"></param>'
      .'<param name="autoplay" value="true"></param>'
      .'<embed src="'.$url.'"'
      .' type="application/x-shockwave-flash"'
      .' allowscriptaccess="always"'
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


/*
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
*/


function
widget_video_tnaflix ($video_id, $width=650, $height=515)
{
  $url = 'config=embedding_feed.php?viewkey='.$video_id;

  $p = '<object type="application/x-shockwave-flash" data="http://www.tnaflix.com/embedding_player/player_v0.2.1.swf"'
      .' width="'.$width.'" height="'.$height.'">'
      .'<param name="allowFullScreen" value="true" />'
      .'<param name="allowScriptAccess" value="always" />'
      .'<param name="movie" value="http://www.tnaflix.com//embedding_player/player_v0.2.1.swf" />'
      .'<param name="FlashVars" value="'.$url.'"/>'
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
  else if (strstr ($media_url, 'tnaflix.com'))
    return 10;

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