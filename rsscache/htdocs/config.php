<?php

//$tv2_title = 'Upload, Download, and watch Demos and Videos of Games';
$tv2_title = 'like TV but cool';
$tv2_body_tag = '<body style="font-family:monospace;text-align:center;opacity:1.0;">';
$tv2_logo = '<div style="font-size:32px;font-family:sans;color:#000;font-weight:bolder;width:100%;text-align:center;">'
           .'pwnoogle \:D/ <img src="images/preview.png" border="0">'
           .'</div>'
           .'<br><br>';

$tv2_root = dirname(__FILE__);
$tv2_download = 'files';
$tv2_link = 'http://pwnoogle.com';

$tv2_player_w = 320;
$tv2_player_h = 240;
$tv2_isnew = 3600 * 6;
$tv2_results = 10;

$tv2_dbname = 'jack';
$tv2_dbuser = 'jack';
$tv2_dbpass = 'poopoo';
$tv2_dbhost = 'localhost';

// method used for requests
//$method = 'GET';

// compression on/off
$use_gzip = 1;
// memcache on/off
$use_memcache = 0;

// localization
//$charset = 'utf-8';
//$title_s = 'Portal';
//$search_s = 'Search';
//$search_exact_s = 'Search for exact phrase';
//$search_desc_s = 'Search in descriptions';

// misc
//$search_desc = 0;
//$search_exact = 1;
//$show_date = 1;
//$make_rss = 1;     // allow RSS feed generation (http://...?output=rss)
//$static_html = 1;  // allow htmldump (http://...?output=dump)
//$allow_stats = 1;  // allow stats output (http://...?output=stats)
//$tooltips = 1;     // show descriptions as tooltips
//$admin_ip = NULL;  // only these ip's and localhost are allowed to be admin
//$admin_public = 0;  // == 1 means everyone has admin access (add feeds or items and access to stats)
                    //  overrides $admin_ip
//$adsense_client = NULL; // client id for google adsense program

?>