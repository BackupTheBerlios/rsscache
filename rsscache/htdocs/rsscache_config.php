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
// rsstool path and options
$rsstool_path = '/usr/bin/torify /usr/local/bin/rsstool';
$rsstool_opts = '--hack-google --sbin --shtml';


// configuration
//$rsscache_config_xml = 'rsscache_config.xml';
//$rsscache_config_xml = $config_subdomain.'_config.xml';
$rsscache_config_xml = array (
'../htdocs/videos_config.xml',
//'demos_config.xml',
'../htdocs/emulive_config.xml',
//'live_config.xml',
//'maps_config.xml',
//'servers_config.xml',
);
$rsscache_thumbnails_prefix = '';
$rsscache_debug_sql = 0;
$rsscache_enable_search = 1; // allow users to search
$rsscache_related_search = 1; // make use of related searches (requires keywords)
//$rsscache_xsl_stylesheet = 'http://'.$_SERVER['SERVER_NAME'].'/rsscache.xsl';
//$rsscache_js = 'http://'.$_SERVER['SERVER_NAME'].'/rsscache.js';


// database settings
$rsscache_dbhost = 'localhost';
$rsscache_dbname = 'rsscache';
$rsscache_dbuser = 'root';
$rsscache_dbpass = 'nb';


}


?>