<?php
if (!defined ('RSSCACHE_CONFIG_PHP'))
{
define ('RSSCACHE_CONFIG_PHP', 1);


// compression 1/0
$use_gzip = 0;
// use memcache? 0 == off
$memcache_expire = 0;

// wget path and options
$wget_path = '/usr/bin/torify /usr/bin/wget'; // use TOR for greedy RSS feeds
//$wget_path = '/usr/bin/wget';
$wget_opts = '-q'
            .' --limit-rate=100000'
;
// rsstool path and options
$rsstool_path = '/usr/bin/torify /usr/local/bin/rsstool';
//$rsstool_path = '/usr/local/bin/rsstool';
$rsstool_opts = '--hack-google --sbin --shtml';


// localization and style
$tv2_icon = 'images/icon.png';
$tv2_charset = 'utf-8';
$tv2_search_s = 'Search';
//$tv2_title = 'video games - pwnoogle \:D/'; //everything emulation, long-plays, oldskool 8 Bit and 16 Bit
//$tv2_logo = 'video games';
//$tv2_videos_s = 'videos';
//$tv2_related_s = 'Find related '.$tv2_videos_s;
$tv2_default_category = '';  // default category
$tv2_collapsed = 2; // collapse categories? -1 == always, 0 == no (default), 1 == yes, 2 == never
$tv2_qrcode = 1; // generate QR codes for links

$tv2_root = dirname(__FILE__).'/';
//$tv2_link = 'http://emulive.pwnoogle.com/';
//$tv2_link_static = 'http://emulive.pwnoogle.com/'; // remote static content
$tv2_cookie_expire = time() + 3600 * 24 * 30 * 1; // 1 month
// DEBUG (show the SQL query)
$tv2_rss_head = 1;
$tv2_debug_sql = 0;
$tv2_isnew = 3600 * 6; // how long new files are marked as new  
$tv2_results = 10; // results per page
$tv2_cloud_results = 100;  // number of tumbnails shown in cloud
$tv2_wall_results = 100;  // number of tumbnails shown in wall
$tv2_wall_view_title = 0; // wall view is the default
$tv2_download_video = 0; // show link for downloading videos
$tv2_buttons_only = 0;   // use only logos as category buttons
$tv2_enable_search = 1; // allow users to search
$tv2_related_search = 1; // make use of related searches (requires keywords)
$tv2_use_dl_date = 0;
$tv2_item_ttl = 1000; // time to life of an item in the db (in days)
//$tv2_banner = 1;

// database settings
$tv2_use_database = 1;
$tv2_dbprefix = '';
$tv2_dbhost = 'localhost';
$tv2_dbname = 'rsscache';
  $tv2_dbuser = 'root';
  $tv2_dbpass = 'nb';


$tv2_config_xml = 'config.xml';
$tv2_thumbnails_prefix = '';

$tv2_link = 'http://videos.pwnoogle.com/';
$tv2_link_static = 'http://videos.pwnoogle.com/'; // static content
$tv2_debug_sql = 0;

//  $tv2_default_category = 'baseqz';

}


?>