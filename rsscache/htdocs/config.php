<?php
if (!defined ('TV2_CONFIG_PHP'))
{
define ('TV2_CONFIG_PHP', 1);


// compression 1/0
$use_gzip = 1;
// use memcache? 0 == off
$memcache_expire = 0;

// wget path and options
$wget_path = '/usr/bin/torify /usr/bin/wget'; // use TOR for greedy RSS feeds
//$wget_path = '/usr/bin/wget';
// rsstool path and options
$rsstool_path = '/usr/local/bin/rsstool';

// localization and style
$tv2_icon = 'images/icon.png';
$tv2_charset = 'utf-8';
$tv2_search_s = 'Search';
//$tv2_title = 'video games - pwnoogle \:D/'; //everything emulation, long-plays, oldskool 8 Bit and 16 Bit
//$tv2_logo = 'video games';
//$tv2_videos_s = 'videos';
//$tv2_related_s = 'Find related '.$tv2_videos_s;
$tv2_default_category = '';  // default category
$tv2_collapsed = 0;   // collapse categories

$tv2_root = dirname(__FILE__).'/';
//$tv2_link = 'http://emulive.pwnoogle.com/';
//$tv2_link_static = 'http://emulive.pwnoogle.com/'; // remote static content
$tv2_cookie_expire = time() + 3600 * 24 * 30 * 1; // 1 month
// DEBUG (show the SQL query)
$tv2_rss_head = 1;
$tv2_debug_sql = 0;
$tv2_isnew = 3600 * 6; // how long new files are marked as new  
$tv2_results = 10; // results per page
$tv2_cloud_results = 500;  // number of tumbnails shown in cloud
$tv2_wall_results = 500;  // number of tumbnails shown in wall
$tv2_wall_view_title = 0; // wall view is the default
$tv2_download_video = 0; // show link for downloading videos
$tv2_buttons_only = 0;   // use only logos as category buttons
$tv2_enable_search = 1; // allow users to search db?
$tv2_related_search = 0; // make use of related searches (requires keywords)

// player settings
//$tv2_player_w = 400; // max. width
//$tv2_player_h = 300; // max. height
//$tv2_player_w = -1; $tv2_player_h = -1; // scaled
$tv2_player_w = 640; // max. width
$tv2_player_h = 480; // max. height
$tv2_player_ratio = 4/3; // default ratio of player
$tv2_preview_w = 400;   
$tv2_preview_h = 300;

// database settings
$tv2_use_database = 1;
$tv2_dbprefix = '';
$tv2_dbhost = 'localhost';
$tv2_dbname = 'pwnoogle_videos';
$tv2_dbuser = 'pwnoogle_db';
$tv2_dbpass = 'pwn44553';

//if ($tv2_subdomain == 'emulive')
{
  $tv2_config_xml = 'videos_config.xml';
  $tv2_include_php = 'videos_include.php';
  $tv2_thumbnails_prefix = '';
  
//  $tv2_title = 'video games - pwnoogle \:D/';
//  $tv2_logo = 'video games';

//  $tv2_link = 'http://emulive.pwnoogle.com/';
//  $tv2_link_static = 'http://emulive.pwnoogle.com/'; // remote static content
//  $tv2_debug_sql = 0;
//  $tv2_enable_search = 1; // allow users to search db?

//  $tv2_dbname = 'pwnoogle_jack';
  $tv2_dbname = 'pwnoogle_videos'; 
  if ($tv2_domain != 'pwnoogle.com' &&
      $tv2_domain != 'videos.pwnoogle.com' &&
      $tv2_domain != 'minecraft.pwnoogle.com' &&
      $tv2_domain != 'quakelive.pwnoogle.com' &&
      $tv2_domain != 'live.pwnoogle.com')
    {
      $tv2_dbname = 'pwnoogle_videos'; 
      $tv2_dbuser = 'root';
      $tv2_dbpass = 'nb';
    }
}


if ($tv2_subdomain == 'videos' || $_SERVER['SERVER_NAME'] == 'pwnoogle.com')
{
  $tv2_config_xml = 'videos_config.xml';
  $tv2_include_php = 'videos_include.php';
  $tv2_thumbnails_prefix = '';
  
  $tv2_title = //'&#x2590;&#x2598;&#x2599;&#x2599;&#x258c;&#x259b;&#x259c;'
    'videos - pwnoogle \:D/';
  $tv2_logo = 'pwnoogle';
  $tv2_videos_s = 'videos';
  $tv2_related_s = 'Find related '.$tv2_videos_s;

  $tv2_enable_search = 0; // allow users to search db?
  $tv2_link = 'http://videos.pwnoogle.com/';
  $tv2_link_static = 'http://videos.pwnoogle.com/'; // static content
  $tv2_debug_sql = 0;
  $tv2_related_search = 0; // make use of related searches (requires keywords)
}
else if ($tv2_subdomain == 'quakelive')
{
  // localization and style
  $tv2_title = 'quakelive - pwnoogle \:D/';
  $tv2_logo = 'QuakeLive';
  $tv2_videos_s = 'videos';
  $tv2_related_s = 'Find related '.$tv2_videos_s;
  $tv2_default_category = 'baseqz';
  $tv2_collapsed = 1;   // collapse categories

  $tv2_enable_search = 0; // allow users to search db?
  $tv2_link = 'http://videos.pwnoogle.com/';
  $tv2_link_static = 'http://videos.pwnoogle.com/'; // static content
  $tv2_debug_sql = 0;
  $tv2_related_search = 0; // make use of related searches (requires keywords)
}
else if ($tv2_subdomain == 'minecraft')
{
  // localization and style
  $tv2_title = 'minecraft - pwnoogle \:D/';
  $tv2_logo = '<img src="images/logos/minecraft32.png" border="0">';
  $tv2_videos_s = 'videos';
  $tv2_related_s = 'Find related '.$tv2_videos_s;
  $tv2_default_category = 'minecraft';
  $tv2_collapsed = 1;   // collapse categories

  $tv2_enable_search = 0; // allow users to search db?
  $tv2_link = 'http://videos.pwnoogle.com/';
  $tv2_link_static = 'http://videos.pwnoogle.com/'; // static content
  $tv2_debug_sql = 0;
  $tv2_related_search = 0; // make use of related searches (requires keywords)
}
else if ($tv2_subdomain == 'live')
{
  // localization and style
  $tv2_title = 'live broadcasts - pwnoogle \:D/';
  $tv2_logo = 'live broadcasts';
  $tv2_videos_s = 'broadcasts';
  $tv2_related_s = 'Find related '.$tv2_videos_s;
  $tv2_default_category = 'live';
  $tv2_collapsed = 1;   // collapse categories

  $tv2_enable_search = 0; // allow users to search db?
  $tv2_link = 'http://videos.pwnoogle.com/';
  $tv2_link_static = 'http://videos.pwnoogle.com/'; // static content
  $tv2_debug_sql = 0;
  $tv2_related_search = 0; // make use of related searches (requires keywords)
}
}


?>