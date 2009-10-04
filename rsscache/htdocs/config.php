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
$tv2_title = 'pwnoogle \:D/ - upload, download, and watch videos and demos of games';
$tv2_body_tag = '<body style="font-family:sans-serif;font-size:13px;text-align:center;opacity:1.0;">';
$tv2_logo = '<span style="font-size:32px;font-family:sans-serif;color:#000;font-weight:bolder;width:100%;text-align:center;">'
           .'pwnoogle \:D/'
//           .' <img src="images/preview.png" border="0">'
           .'</span>'
           .'<br>'
           .'<br>'
;
$tv2_videos_s = 'videos and demos';
$tv2_days_s = 'days';
$tv2_related_s = 'Find realted '.$tv2_videos_s;


$tv2_root = dirname(__FILE__);
$tv2_link = 'http://pwnoogle.com';
$tv2_cookie_expire = time() + 3600 * 24 * 180; // 6 months


// player settings
$tv2_player_w = 400;
$tv2_player_h = 300;
//$tv2_player_w = -1; // max. width
//$tv2_player_h = -1; // max. height
$tv2_isnew = 3600 * 6;
$tv2_results = 10;


// database settings
$tv2_dbname = 'jack';
$tv2_dbuser = 'jack';
$tv2_dbpass = 'poopoo';
$tv2_dbhost = 'localhost';



}


?>