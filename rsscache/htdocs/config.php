<?php
if (!defined ('TV2_CONFIG_PHP'))
{
define ('TV2_CONFIG_PHP', 1);


// compression 1/0
$use_gzip = 0;
// use memcache? 0 == off
$memcache_expire = 0;


// localization and style
$tv2_charset = 'utf-8';
$tv2_icon = 'images/icon.png';
$tv2_search_s = 'Search';
$tv2_title = //'&#x2590;&#x2598;&#x2599;&#x2599;&#x258c;&#x259b;&#x259c;'
  'pwnoogle \:D/ - watch videos of games';
$tv2_head_tag = '<title>'.$tv2_title.'</title>'
.'<meta name="google-site-verification" content="akU6AtYoOtUZ5n8IGHTC3s5uc9AOAnPeqxkckHSi224" />';
$tv2_body_tag = '<body>';
$tv2_logo = 'pwnoogle';
$tv2_videos_s = 'videos';
$tv2_related_s = 'Find related '.$tv2_videos_s;


$tv2_enable_search = 0; // allow users to search db?
$tv2_rss_head = 1;
$tv2_root = dirname(__FILE__).'/';
$tv2_link = 'http://videos.pwnoogle.com/';
$tv2_link_static = 'http://videos.pwnoogle.com/'; // static content
$tv2_cookie_expire = time() + 3600 * 24 * 180; // 6 months
// DEBUG (show the SQL query)
$tv2_debug_sql = 0;
$tv2_isnew = 3600 * 6; // how long new files are marked as new  
$tv2_results = 10; // results per page
$tv2_cloud_results = 500;  // number of tumbnails shown in cloud
$tv2_wall_results = 500;  // number of tumbnails shown in wall
$tv2_download_video = 0; // show link for downloading videos
$tv2_related_search = 0; // make use of related searches (requires keywords)

// player settings
//$tv2_player_w = 400; // max. width
//$tv2_player_h = 300; // max. height
//$tv2_player_w = -1; $tv2_player_h = -1; // scaled
$tv2_player_w = 640; // max. width
$tv2_player_h = 480; // max. height
$tv2_player_ratio = 4/3; // default ratio of player

// database settings
$tv2_use_database = 1;
if (stristr ($_SERVER['SERVER_NAME'], 'pwnoogle.com'))
  {
    $tv2_dbname = 'pwnoogle_jack';
    $tv2_dbuser = 'pwnoogle_db';
    $tv2_dbpass = 'pwn44553';
  }
else if (stristr ($_SERVER['SERVER_NAME'], 'aa.k.vu') || stristr ($_SERVER['USER'], 'root'))
  {
    $tv2_dbname = 'pwnoogle_videos';
    $tv2_dbuser = 'root';
    $tv2_dbpass = 'perfect';  
  }
/*
else
  {
    $tv2_dbname = 'pwnoogle_videos';
    $tv2_dbuser = 'root';
    $tv2_dbpass = 'nb';
  }
*/
$tv2_dbhost = 'localhost';


}


?>
