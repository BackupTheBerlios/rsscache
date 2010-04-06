<?php
if (!defined ('TV2_CONFIG_PHP'))
{
define ('TV2_CONFIG_PHP', 1);


// compression 1/0
$use_gzip = 1;
// use memcache? 0 == off
$memcache_expire = 0;


// localization and style
//$tv2_charset = 'utf-8';
$tv2_search_s = 'Search';
$tv2_title = 'pwnoogle \:D/ - watch videos of games';
$tv2_body_tag = '<body style="font-family:sans-serif;font-size:13px;opacity:1.0;">'
               .'<a href="http://pwnoogle.com">Videos</a>&nbsp;&nbsp;'
               .'<a href="http://demos.pwnoogle.com/demos.php">Demos</a>&nbsp;&nbsp;'
               .'<a href="http://aa2map.y7.ath.cx">Map Generator</a>&nbsp;&nbsp;'
               .'<a href="http://clanarena.org">Forum @ <img src="images/ca.png" border="0" height="24"></a><br><hr>';
$tv2_table_tag = '<table style="font-family:sans-serif;font-size:13px;opacity:1.0;" cellspacing="0" cellpadding="0" border="0">';
$tv2_logo = '<span style="font-size:32px;font-family:sans-serif;color:#000;font-weight:bolder;width:100%;text-align:center">'
           .'pwnoogle \:D/'
//           .' <img src="images/preview.png" border="0">'
           .'</span> '
//           .'<br>'
//           .'<br>'
;
$tv2_videos_s = 'videos and demos';
$tv2_related_s = 'Find related '.$tv2_videos_s;


$tv2_root = dirname(__FILE__).'/';
$tv2_link = 'http://pwnoogle.com/';
$tv2_link_static = 'http://allowdl.com/pwnoogle/'; // static content
$tv2_cookie_expire = time() + 3600 * 24 * 180; // 6 months
// DEBUG (show the SQL query)
$tv2_debug_sql = 0;
$tv2_isnew = 3600 * 6; // how long new files are marked as new  
$tv2_results = 10; // results per page
$tv2_cloud_results = 500;  // number of tumbnails shown in cloud
$tv2_wall_results = 500;  // number of tumbnails shown in cloud
$tv2_download_video = 0; // show link for downloading videos


// player settings
$tv2_player_w = 400; // max. width
$tv2_player_h = 300; // max. height
//$tv2_player_w = -1; $tv2_player_h = -1; // fullscreen


// database settings
$tv2_dbname = 'jack';
$tv2_dbuser = 'jack';
$tv2_dbpass = 'poopoo';
$tv2_dbhost = 'localhost';


// rescue
//$rescue_default = 'http://www.youtube.com/watch?v=MkDrWsBhFjo';
//$rescue_default = 'http://www.youtube.com/watch?v=4Inr22ZBmdw';
//$rescue_default = 'http://www.youtube.com/watch?v=dh3bleXWaCk';
//$rescue_default = 'http://www.youtube.com/watch?v=rvNTrvJorbs';
//$rescue_default = 'http://www.youtube.com/watch?v=CzAYDtOD8UY';
$rescue_default = 'http://www.youtube.com/watch?v=CzVsu4f4iz0';

$rescue_videos_horizontal = 4;
$rescue_w = 1024; $rescue_h = 768;
$rescue_autoplay = 0;
$rescue_hq = 1; // high quality
$rescue_max_videos = 64;
$rescue_max_search_results = 32;
// connect to sites using TOR
$rescue_tor_enabled = 1;


}


?>