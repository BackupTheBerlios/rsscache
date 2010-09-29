<?php
if (!defined ('TV2_MISC_PHP'))
{
define ('TV2_MISC_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('tv2_sql.php');


function
config_xml_normalize ($config)
{
  $stats = tv2_sql_stats ();

  // add new variables
  $config->videos = $stats['videos'];
  $config->videos_today = $stats['videos_today'];
  $config->videos_7_days = $stats['videos_7_days'];
  $config->videos_30_days = $stats['videos_30_days'];
  $config->days = $stats['days'];

  for ($i = 0; $config->category[$i]; $i++)
    if ($config->category[$i]->query)
      {
        $a = array();
        parse_str ($config->category[$i]->query, $a);
        if (isset ($a['c']))
          {
            $stats = tv2_sql_stats ($config->category[$i]->name);

            $config->category[$i]->videos = $stats['videos'];
            $config->category[$i]->videos_today = $stats['videos_today'];
            $config->category[$i]->videos_7_days = $stats['videos_7_days'];
            $config->category[$i]->videos_30_days = $stats['videos_30_days'];
            $config->category[$i]->days = $stats['days'];
          }
      }

  return $config;
}


function
config_xml ($memcache_expire = 0)
{
  static $config = NULL;

  if ($config)
    return $config;

if ($memcache_expire > 0)
  {
    $memcache = new Memcache;
    if ($memcache->connect ('localhost', 11211) == TRUE)
      {
        // data from the cache
        $p = $memcache->get (md5 ('config.xml'));

        if ($p != FALSE)
          {
            $p = unserialize ($p);

            // DEBUG
//            echo 'cached';

            echo $p;

            exit;
          }
      }
    else
      {
        echo 'ERROR: could not connect to memcached';
        exit;
      }
  }

  // DEBUG
//  echo 'read config';

  $config = simplexml_load_file ('config.xml');
  $config = config_xml_normalize ($config);

  // use memcache
if ($memcache_expire > 0)
  {
    $memcache->set (md5 ('config.xml'), serialize ($config), 0, $memcache_expire);
  }

  return $config;
}


function
config_xml_by_category ($category)
{
  $config = config_xml ();

  for ($i = 0; $config->category[$i]; $i++)
    if ($config->category[$i]->name == $category)
      return $config->category[$i];

  return NULL;
}


// HACK
function
tv2_normalize ($category)
{
  $p = strtolower ($category);

  if ($p == 'baseq3')
    $category = 'quake3';
  else if ($p == 'baseqz')
    $category = 'quakelive';

  return $category;
}


function
tv2_rss ($d_array)
{
  global $tv2_link;
  global $tv2_name;
  global $tv2_title;

//    header ('Content-type: text/xml');
    header ('Content-type: application/xml');
//    header ('Content-type: text/xml-external-parsed-entity');
//    header ('Content-type: application/xml-external-parsed-entity');
//    header ('Content-type: application/xml-dtd');

  $rss_title_array = array ();
  $rss_link_array = array ();
  $rss_desc_array = array ();

  for ($i = 0; isset ($d_array[$i]); $i++)
    {
      $rss_title_array[$i] = $d_array[$i]['rsstool_title'];
//      $rss_link_array[$i] = $d_array[$i]['rsstool_url'];
      if (substr (tv2_link ($d_array[$i]), 0, 7) == 'http://')
        $rss_link_array[$i] = tv2_link ($d_array[$i]);
      else
        $rss_link_array[$i] = $tv2_link.tv2_link ($d_array[$i]);

      $rss_desc_array[$i] = ''
                           .tv2_thumbnail ($d_array[$i], 120, 1)
                           .'<br>'
                           .$d_array[$i]['rsstool_desc'];
    }

  // DEBUG
//  print_r ($rss_title_array);
//  print_r ($rss_link_array);
//  print_r ($rss_desc_array);

  echo generate_rss ($tv2_name,
                     $tv2_link,
                     $tv2_title,
                     $rss_title_array, $rss_link_array, $rss_desc_array);
}


function
tv2_link_normalize ($link)
{
  // checks is file is on local server or on static server and returns correct link
  global $tv2_root,
         $tv2_link,
         $tv2_link_static;
  $p = $link; // $d['rsstool_url']

  if (strncmp ($p, $tv2_link, strlen ($tv2_link)) || // extern link
      !$tv2_link_static) // no static server
    return $link;

  $p = str_replace ($tv2_link, $tv2_root, $link); // file on local server?
  if (file_exists ($p))
    return $link;

  return str_replace ($tv2_link, $tv2_link_static, $link); // has to be on static server then
}


function
tv2_sitemap ()
{
/*
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <url>
      <loc>http://www.example.com/</loc>
      <lastmod>2005-01-01</lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.8</priority>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=12&amp;desc=vacation_hawaii</loc>
      <changefreq>weekly</changefreq>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=73&amp;desc=vacation_new_zealand</loc>
      <lastmod>2004-12-23</lastmod>
      <changefreq>weekly</changefreq>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=74&amp;desc=vacation_newfoundland</loc>
      <lastmod>2004-12-23T18:00:15+00:00</lastmod>
      <priority>0.3</priority>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=83&amp;desc=vacation_usa</loc>
      <lastmod>2004-11-23</lastmod>
   </url>
</urlset>

<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <sitemap>
      <loc>http://www.example.com/sitemap1.xml.gz</loc>
      <lastmod>2004-10-01T18:23:17+00:00</lastmod>
   </sitemap>
   <sitemap>
      <loc>http://www.example.com/sitemap2.xml.gz</loc>
      <lastmod>2005-01-01</lastmod>
   </sitemap>
</sitemapindex>
*/
}


}


?>