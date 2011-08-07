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
$rsscache_icon = 'images/icon.png';
$rsscache_charset = 'utf-8';
$rsscache_search_s = 'Search';
//$rsscache_title = 'video games - pwnoogle \:D/'; //everything emulation, long-plays, oldskool 8 Bit and 16 Bit
//$rsscache_logo = 'video games';
//$rsscache_videos_s = 'videos';
//$rsscache_related_s = 'Find related '.$rsscache_videos_s;
$rsscache_default_category = '';  // default category
$rsscache_collapsed = 2; // collapse categories? -1 == always, 0 == no (default), 1 == yes, 2 == never
$rsscache_qrcode = 1; // generate QR codes for links

$rsscache_root = dirname(__FILE__).'/';
//$rsscache_link = 'http://emulive.pwnoogle.com/';
//$rsscache_link_static = 'http://emulive.pwnoogle.com/'; // remote static content
$rsscache_cookie_expire = time() + 3600 * 24 * 30 * 1; // 1 month
// DEBUG (show the SQL query)
$rsscache_rss_head = 1;
$rsscache_debug_sql = 0;
$rsscache_isnew = 3600 * 6; // how long new files are marked as new  
$rsscache_results = 10; // results per page
$rsscache_cloud_results = 100;  // number of tumbnails shown in cloud
$rsscache_wall_results = 100;  // number of tumbnails shown in wall
$rsscache_wall_view_title = 0; // wall view is the default
$rsscache_download_video = 0; // show link for downloading videos
$rsscache_buttons_only = 0;   // use only logos as category buttons
$rsscache_enable_search = 1; // allow users to search
$rsscache_related_search = 1; // make use of related searches (requires keywords)
$rsscache_use_dl_date = 0;
$rsscache_item_ttl = 1000; // time to life of an item in the db (in days)
//$rsscache_banner = 1;

// database settings
$rsscache_dbprefix = '';
$rsscache_dbhost = 'localhost';
$rsscache_dbname = 'rsscache';
$rsscache_dbuser = 'root';
$rsscache_dbpass = 'nb';


$rsscache_config_xml = 'config.xml';
$rsscache_thumbnails_prefix = '';

$rsscache_link = 'http://videos.pwnoogle.com/';
$rsscache_link_static = 'http://videos.pwnoogle.com/'; // static content
$rsscache_debug_sql = 0;

//  $rsscache_default_category = 'baseqz';


// set user agent for downloads
require_once ('misc/misc.php');
ini_set('rsscache_user_agent', random_user_agent ());
$rsscache_user_agent = random_user_agent ();


}


?>