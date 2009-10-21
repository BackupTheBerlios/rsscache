<?php
if (!defined ('TV2_CONFIG_PHP'))
{
define ('TV2_CONFIG_PHP', 1);


// compression 1/0
$use_gzip = 1;
// use memcache? 0 == off
$memcache_expire = 120;


// localization and style
//$tv2_charset = 'utf-8';
$tv2_search_s = 'Search';
$tv2_title = 'pwnoogle \:D/ - upload, download, and watch videos and demos of games';
$tv2_body_tag = '<body style="font-family:sans-serif;font-size:13px;opacity:1.0;">';
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
//$tv2_link_static = 'http://allowdl.com/pwnoogle/'; // static content
$tv2_link_static = 'http://pwnoogle.com/'; // static content
$tv2_cookie_expire = time() + 3600 * 24 * 180; // 6 months
// DEBUG (show the SQL query)
$tv2_debug_sql = 0;


// player settings
$tv2_player_w = 400; // max. width
$tv2_player_h = 300; // max. height
//$tv2_player_w = -1; $tv2_player_h = -1; // fullscreen
$tv2_isnew = 3600 * 6; // how long new files are marked as new
$tv2_results = 10; // results per page


// database settings
$tv2_dbname = 'jack';
$tv2_dbuser = 'jack';
$tv2_dbpass = 'poopoo';
$tv2_dbhost = 'localhost';



}


?>