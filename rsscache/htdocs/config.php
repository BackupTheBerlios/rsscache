<?php


// compression 1/0
$use_gzip = 1;
// use memcache? 0 == off
$memcache_expire = 0;


// localization and style
//$tv2_charset = 'utf-8';
$tv2_search_s = 'Search';
$tv2_title = 'pwnoogle \:D/ - upload, download, and watch videos and demos of games';
$tv2_body_tag = '<body style="font-family:monospace;text-align:center;opacity:1.0;">';
$tv2_logo = '<div style="font-size:32px;font-family:sans;color:#000;font-weight:bolder;width:100%;text-align:center;">'
           .'pwnoogle \:D/'
//           .' <img src="images/preview.png" border="0">'
           .'</div>'
           .'<br>'
;

$tv2_root = dirname(__FILE__);
$tv2_link = 'http://pwnoogle.com';
$tv2_cookie_expire = time() + 3600 * 24 * 180; // 6 months


// player settings
$tv2_player_w = 400;
$tv2_player_h = 300;
$tv2_isnew = 3600 * 6;
$tv2_results = 10;


// database settings
$tv2_dbname = 'jack';
$tv2_dbuser = 'jack';
$tv2_dbpass = 'poopoo';
$tv2_dbhost = 'localhost';


?>