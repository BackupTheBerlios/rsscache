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


// configuration
$rsscache_config_xml = 'rsscache/default.xml';
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
$rsscache_time = time (0);
$rsscache_title = 'RSScache'          .' 0.9.6beta-'.sprintf ("%u", $rsscache_time);
$rsscache_logo = 'http://'.$_SERVER['SERVER_NAME'].'/images/rsscache_logo.png';
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
$rsscache_default_output = 'rss';  // if no &output= is given
$rsscache_xsl_trans = 0;  // XSL transformation for HTML output (0 == server, 1 == client, 2 == auto)
$rsscache_xsl_stylesheet_path = 'rsscache/xsl';  // path to XSL files relative to htdocs/
$rsscache_js = 'http://'.$_SERVER['SERVER_NAME'].'/rsscache/rsscache.js';


// database settings
$rsscache_dbprefix = '';
$rsscache_dbname = 'rsscache_dbname';
$rsscache_dbuser = 'rsscache_dbuser';
$rsscache_dbpass = 'rsscache_dbpass';
$rsscache_dbhost = 'rsscache_dbhost';
$rsscache_item_ttl = 0; // delete older (in seconds) items from db (<=0 == never delete)


// set user agent for downloads
ini_set('rsscache_user_agent', 'rsscache');
$rsscache_user_agent = 'rsscache';


$rsscache_description = 'RSScache uses RSS 2.0 specification with new namespaces (rsscache and cms) for configuration<br>'."\n"
      .'<br>'."\n"
      .'format:<br>'."\n"
      .'rss                       even config files are made of RSS :)<br>'."\n"
      .'  channel[]               site<br>'."\n"
      .'    title                 title<br>'."\n"
      .'    link                  site link<br>'."\n"
      .'    description<br>'."\n"
      .'    image                 optional<br>'."\n"
      .'      url                 image url<br>'."\n"
      .'      link                optional, image link<br>'."\n"
      .'TODO:      width               optional, image width<br>'."\n"
      .'TODO:      height              optional, image height<br>'."\n"
//      .'TODO:    rsscache:filter  optional<br>'."\n"
      .'    item[]                    feed downloads<br>'."\n"
      .'      title                 category title<br>'."\n"
      .'      link                  optional, url of button or select<br>'."\n"
      .'                              &amp;q=SEARCH       search<br>'."\n"
      .'                              *** functions ***<br>'."\n"
      .'                              &amp;f=all          show all categories (sorted by time of RSS feed download)<br>'."\n"
      .'                              &amp;f=new          show all categories (sorted by time of RSS item)<br>'."\n"
      .'                              &amp;f=0_5min       show media with <5 minutes duration<br>'."\n"
      .'                              &amp;f=5_10min<br>'."\n"
      .'                              &amp;f=10_30min<br>'."\n"
      .'                              &amp;f=30_60min<br>'."\n"
      .'                              &amp;f=60min<br>'."\n"
      .'                              &amp;f=related<br>'."\n"
      .'                              &amp;f=stats<br>'."\n"
      .'                              &amp;f=author<br>'."\n"
      .'                              &amp;f=sitemap<br>'."\n"
      .'                              &amp;f=robots<br>'."\n"
      .'                              &amp;f=cache<br>'."\n"
      .'                              &amp;f=config<br>'."\n"
      .'                              &amp;f=4_3          4:3 ratio for videos (CMS only)<br>'."\n"
      .'                              &amp;f=16_9         16:9 ratio for videos (CMS only)<br>'."\n"
//      .'TODO:                              &amp;f=score        sort by score/votes/popularity<br>'."\n"
      .'                              *** output ***<br>'."\n"
      .'                              &amp;output=rss     output page as RSS feed<br>'."\n"
      .'                              &amp;output=mirror  output page as static HTML<br>'."\n"
      .'                              &amp;output=wall    show search results as wall<br>'."\n"
      .'                              &amp;output=cloud   same as wall<br>'."\n"
      .'                              &amp;output=stats   show RSS feed download stats<br>'."\n"
      .'                              &amp;output=1col    show videos in 1 column<br>'."\n"
      .'                              &amp;output=2cols   show videos in 2 columns<br>'."\n"
      .'      description<br>'."\n"
      .'      category              category name<br>'."\n"
      .'      enclosure             optional, category logo/image<br>'."\n"
      .'        url                 image url<br>'."\n"
      .'        length<br>'."\n"
      .'        type<br>'."\n"
//      .'TODO:      rsscache:filter         optional, boolean full-text search query for SQL query using IN BOOLEAN MODE modifier<br>'."\n"
      .'      rsscache:feed[]<br>'."\n"
//      .'TODO:        rsscache:update                 optional, "cron" (default), "always" or "never"'."\n"
      .'        rsscache:link                   link of feed (RSS, etc.)<br>'."\n"
      .'                                http://gdata.youtube.com/feeds/api/videos?author=USERNAME&amp;vq=SEARCH&amp;max-results=50<br>'."\n"
      .'                                http://gdata.youtube.com/feeds/api/videos?vq=SEARCH&amp;max-results=50<br>'."\n"
      .'        NOTE: use link_prefix, link_suffix and link_search when getting more than one RSS feed from the same place<br>'."\n"
      .'        rsscache:link_prefix    same as link<br>'."\n"
      .'        rsscache:link_search[]<br>'."\n"
      .'        rsscache:link_suffix<br>'."\n"
      .'        rsscache:exec           cmdline where feed link(s) are passed to<br>'."\n"
//      .'TODO:      rsscache:filter       optional, boolean full-text search query for SQL query using IN BOOLEAN MODE modifier<br>'."\n"
      .'TODO:      rsscache:subdomain    optional, config is only read if subdomain matches<br>'."\n"
      .'      rsscache:table_suffix  <br>'."\n"
//      .'TODO:      rsscache:votable          if items of this category can be voted for<br>'."\n"
//      .'                                       0 = not (default)<br>'."\n"
//      .'                                       1 = by everyone<br>'."\n"
//      .'TODO:      rsscache:reportable       if items can be reported to the admins<br>'."\n"
//      .'                                       0 = not (default)<br>'."\n"
//      .'                                       1 = by everyone<br>'."\n"
//      .'TODO:      rsscache:movable          if items can be moved to another category<br>'."\n"
//      .'                                       0 = not (default)<br>'."\n"
//      .'                                       1 = by the admin only<br>'."\n"
//      .'                                       2 = by everyone<br>'."\n"
      .'<br>'."\n"
      .'CMS options, widget.php/widget_cms():<br>'."\n"
      .'    cms:separate     optional, adds a line-feed or separator before the next category<br>'."\n"
      .'                            0 == no separator (default)<br>'."\n"
      .'                            1 == line-feed<br>'."\n"
      .'                            2 == horizontal line (hr tag)<br>'."\n"
      .'    cms:button_only  optional, show only button<br>'."\n"
      .'    cms:status       optional, adds a small status note<br>'."\n"
      .'                            0 == nothing (default)<br>'."\n"
      .'                            1 == "New!"<br>'."\n"
      .'                            2 == "Soon!"<br>'."\n"
      .'                            3 == "Preview!"<br>'."\n"
      .'                            4 == "Update!"<br>'."\n"
      .'    cms:select       add to select menu<br>'."\n"
      .'<br>'."\n"
      .'    cms:local        optional, local file to embed<br>'."\n"
      .'    cms:iframe       optional, url to embed<br>'."\n"
      .'    cms:proxy        optional, url to embed (proxy-style)<br>'."\n"
      .'    cms:feed         optional, url of RSS feed to render<br>'."\n"
      .'<br>'."\n"
      .'optional:<br>'."\n"
      .'rss<br>'."\n"
      .'  channel[]<br>'."\n"
      .'    docs<br>'."\n"
      .'    item[]<br>'."\n"
      .'      pubDate<br>'."\n"
      .'      author<br>'."\n"
      .'      media:duration<br>'."\n"
      .'      media:keywords<br>'."\n"
      .'      media:thumbnail<br>'."\n"
      .'      rsscache:dl_date<br>'."\n"
      .'      rsscache:pubDate      same as pubDate but as integer<br>'."\n"
      .'      rsscache:related_id<br>'."\n"
      .'      rsscache:event_start<br>'."\n"
      .'      rsscache:event_end<br>'."\n"
      .'      rsscache:url_crc32<br>'."\n"
      .'      rsscache:stats_category<br>'."\n"
      .'      rsscache:stats_items<br>'."\n"
      .'      rsscache:stats_days<br>'."\n"
      .'      rsscache:stats_items_today<br>'."\n"
      .'      rsscache:stats_items_7_days<br>'."\n"
      .'      rsscache:stats_items_30_days<br>'."\n"
      .'      rsscache:download     admin, only<br>'."\n"
      .'TODO:      cms:button_html  set in config_xml_nomalize()<br>'."\n"
      .'TODO:      cms:option_html  for select boxes<br>'."\n"
      .'      cms:demux<br>'."\n"
      .'<br>'."\n"
      .'*** queries ***<br>'."\n"
      .'&amp;q=SEARCH     SEARCH query<br>'."\n"
      .'&amp;start=N      start from result N<br>'."\n"
      .'&amp;num=N        show N results<br>'."\n"
      .'&amp;c=NAME       category (leave empty for all categories)<br>'."\n"
      .'&amp;item=URL_CRC32   show single item<br>'."\n"
      .'&amp;f=FUNC       execute FUNCtion<br>'."\n"
      .'&amp;output=FORMAT   output in "rss", "atom", "html", "mediawiki", "json" or "sitemap" (default: rss)<br>'."\n"
      .'                     "pls" and "m3u" for admin, only<br>'."\n"
//      .'&amp;prefix=SUBDOMAIN   prefix or SUBDOMAIN (leave empty for current subdomain)<br>'."\n"
      .'<br>'."\n"           
      .'*** functions ***<br>'."\n"
      .'&amp;f=author     find user/author/channel (requires &amp;q=SEARCH)<br>'."\n"
      .'&amp;<a href="?f=0_5min&output=html">f=0_5min</a>     media with duration 0-5 minutes<br>'."\n"
      .'&amp;<a href="?f=5_10min&output=html">f=5_10min</a>    media with duration 5-10 minutes<br>'."\n"
      .'&amp;<a href="?f=10_30min&output=html">f=10_30min</a>   media with duration 10-30 minutes<br>'."\n"
      .'&amp;<a href="?f=30_60min&output=html">f=30_60min</a>   media with duration 30-60 minutes<br>'."\n"
      .'&amp;<a href="?f=60min&output=html">f=60_min</a>     media with duration 60+ minutes<br>'."\n"
      .'&amp;<a href="?f=new&output=html">f=new</a>        show only newly created items (default: download time)<br>'."\n"
      .'&amp;f=related    find related items (requires &amp;q=RELATED_ID)<br>'."\n"
      .'&amp;<a href="?f=stats&output=html">f=stats</a>      statistics<br>'."\n"
//      .'&amp;f=error404      <br>'."\n"
//      .'&amp;f=error304      <br>'."\n"
//      .'&amp;f=error300      <br>'."\n"
      .'<br>'."\n"
      .'*** admin functions ***<br>'."\n"
      .'&amp;<a href="?f=robots">f=robots</a>    robots.txt<br>'."\n"
      ."\n"
      .'requires access to <a href="admin.php?output=html">admin.php</a>:<br>'."\n"
      .'&amp;<a href="?f=cache&output=html">f=cache</a>      cache (new) items into database (requires &c=CATEGORY)<br>'."\n"
      .'&amp;<a href="?f=config&output=html">f=config</a>    indent and dump config.xml<br>'."\n"
//      .'&amp;<a href="?output=pls">output=pls</a>    generate playlist<br>'."\n"
//      .'&amp;<a href="?output=pls">output=m3u</a>    generate playlist<br>'."\n"
      .'<br>'."\n"
      .'*** install ***<br>'."\n"
      .'see apache2/sites-enabled/rsscache<br>'."\n"
;


}

?>