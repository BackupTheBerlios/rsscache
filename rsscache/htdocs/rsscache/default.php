<?php
if (!defined ('RSSCACHE_DEFAULT_PHP'))
{
define ('RSSCACHE_DEFAULT_PHP', 1);


// compression 1/0
$use_gzip = 0;
// use memcache? 0 == off
$memcache_expire = 0;


// wget path and options
//$wget_path = '/usr/bin/torify /usr/bin/wget'; // use TOR for greedy RSS feeds
$wget_path = '/usr/bin/wget';
$wget_opts = '-q --limit-rate=100000';


// rsstool path and options
//$rsstool_path = '/usr/bin/torify /usr/local/bin/rsstool';
$rsstool_path = '/usr/local/bin/rsstool';
$rsstool_opts = '--hack-google --hack-event --sbin --shtml';
//$rsstool_opts = '--hack-google --sbin --shtml';


// configuration
$rsscache_config_xml = 'rsscache/default.xml';
$rsscache_thumbnails_prefix = '';
$rsscache_maintenance = 0; // down for maintenance?
$rsscache_root = dirname(__FILE__).'/';
$rsscache_link = 'http://'.$_SERVER['SERVER_NAME'].'/';
$rsscache_link_static = 'http://'.$_SERVER['SERVER_NAME'].'/'; // static content

/*
// queries (change only if you know what you do)
$rsscache_query = array ('c' => 'c', // category
                    'q' => 'q', // search query
                    'f' => 'f', // function
                    'item' => 'item', // item
                    'start' => 'start',
                    'num' = 'num');
*/
$rsscache_icon = 'http://'.$_SERVER['SERVER_NAME'].'/images/rsscache_icon.png';
$rsscache_charset = 'UTF-8';
$rsscache_title = 'RSScache';
$rsscache_logo = 'http://'.$_SERVER['SERVER_NAME'].'/images/rsscache_logo.png';
$rsscache_time = time (0);
$rsscache_debug_sql = 0; // show the SQL queries
$rsscache_isnew = (3600 * 6); // how long new files are marked as new (in seconds)
$rsscache_results = 10; // results per query
$rsscache_max_results = 50; // max. results per query
$rsscache_use_dl_date = 0; // show and sort items by date of RSS download? (default: 0)
$rsscache_enable_search = 0; // allow users to search db?
$rsscache_enable_search_extern = 0; // allow users to search youtube db?
$rsscache_related_search = 0; // make use of related searches (requires keywords)
$rsscache_page_backwards = 0; // count pages starting with highest
$rsscache_default_category = ''; // default category
$rsscache_default_function = ''; // default function
$rsscache_tor_enabled = 0;  // connect to extern sites using TOR
$rsscache_item_ttl = 0; // delete older (in seconds) items from db (<=0 == never delete)
$rsscache_xsl_trans = 0;  // XSL transformation for HTML output (0 == server, 1 == client, 2 == auto)
$rsscache_xsl_stylesheet_path = 'http://'.$_SERVER['SERVER_NAME'].'/rsscache/xsl/';
$rsscache_js = 'http://'.$_SERVER['SERVER_NAME'].'/rsscache/rsscache.js';


// database settings
$rsscache_dbprefix = '';
$rsscache_dbname = 'rsscache_dbname';
$rsscache_dbuser = 'rsscache_dbuser';
$rsscache_dbpass = 'rsscache_dbpass';
$rsscache_dbhost = 'rsscache_dbhost';


// set user agent for downloads
ini_set('rsscache_user_agent', 'rsscache');
$rsscache_user_agent = 'rsscache';

}

?>