<?php
if (!defined ('TV2_MISC_PHP'))
{
define ('TV2_MISC_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('tv2_sql.php');


function
tv2_get_category ()
{
  global $config;
  $c = get_request_value ('c'); // category

  if (!($c)) // default category
    for ($i = 0; isset ($config->category[$i]); $i++)
      if ($config->category[$i]->default == 1)
        return $config->category[$i]->name;
  return NULL;
}


function
config_xml_normalize ($config)
{
  global $tv2_use_database;

  if ($tv2_use_database == 1)
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
        $rss_link_array[$i] = $tv2_link.'?'.tv2_link ($d_array[$i]);

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
tv2_sitemap_video ()
{
/*
     <video:video>
       <video:thumbnail_loc>http://www.example.com/thumbs/123.jpg</video:thumbnail_loc> 
       <video:title>Grilling steaks for summer</video:title>
       <video:description>Alkis shows you how to get perfectly done steaks every            
         time</video:description>
       <video:content_loc>http://www.example.com/video123.flv</video:content_loc>
       <video:player_loc allow_embed="yes" autoplay="ap=1">
         http://www.example.com/videoplayer.swf?video=123</video:player_loc>
       <video:duration>600</video:duration>
       <video:expiration_date>2009-11-05T19:20:30+08:00</video:expiration_date>
       <video:rating>4.2</video:rating> 
       <video:view_count>12345</video:view_count>    
       <video:publication_date>2007-11-05T19:20:30+08:00</video:publication_date>
       <video:tag>steak</video:tag> 
       <video:tag>meat</video:tag> 
       <video:tag>summer</video:tag> 
       <video:category>Grilling</video:category>
       <video:family_friendly>yes</video:family_friendly>   
       <video:restriction relationship="allow">IE GB US CA</video:restriction> 
       <video:gallery_loc title="Cooking Videos">http://cooking.example.com</video:gallery_loc>
       <video:price currency="EUR">1.99</video:price>
       <video:requires_subscription>yes</video:requires_subscription>
       <video:uploader info="http://www.example.com/users/grillymcgrillerson">GrillyMcGrillerson
         </video:uploader>
     </video:video> 
<loc> 	Required 	The tag specifies the landing page (aka play page, referrer page) for the video. When a user clicks on a video result on a search results page, they will be sent to this landing page. Must be a unique URL.
<video:video> 	Required
	
<video:thumbnail_loc> 	Required 	A URL pointing to the URL for the video thumbnail image file. We can accept most image sizes/types but recommend your thumbs are at least 160x120 pixels in .jpg, .png, or. gif formats.
<video:title> 	Required 	The title of the video. Limited to 100 characters.
<video:description> 	Required 	The description of the video. Descriptions longer than 2048 characters will be truncated.
<video:content_loc> 	Depends 	At least one of <video:player_loc> or <video:content_loc> is required. The URL should point to a .mpg, .mpeg, .mp4, .m4v, .mov, .wmv, .asf, .avi, .ra, .ram, .rm, .flv, or other video file format, and can be omitted if <video:player_loc> is specified. However, because Google needs to be able to check that the Flash object is actually a player for video (as opposed to some other use of Flash, e.g. games and animations), it's helpful to provide both.

Best practice: Help ensure that only Googlebot accesses your content by using a reverse DNS lookup.
<video:player_loc> 	Depends 	At least one of <video:player_loc> or <video:content_loc> is required. A URL pointing to a Flash player for a specific video. In general, this is the information in the src element of an <embed> tag and should not be the same as the content of the <loc> tag. #Since each video is uniquely identified by its content URL (the location of the actual video file) or, if a content URL is not present, a player URL (a URL pointing to a player for the video), you must include either the <video:player_loc> or <video:content_loc> tags. If these tags are omitted and we can't find this information, we'll be unable to index your video.

The optional attribute allow_embed specifies whether Google can embed the video in search results. Allowed values are Yes or No.

The optional attribute autoplay has a user-defined string (in the example above, ap=1) that Google may append (if appropriate) to the flashvars parameter to enable autoplay of the video. For example: <embed src="http://www.example.com/videoplayer.swf?video=123" autoplay="ap=1"/>.

Examples:

Youtube: http://www.youtube.com/swf/l.swf?swf=http%3A//s.ytimg.com/yt/swf/cps-vfl87635.swf&video_id=v65Ud3VqChY

Dailymotion: http://www.dailymotion.com/swf/x1o2g

Best practice:Help ensure that only Googlebot accesses your content by using a reverse DNS lookup.
<video:duration> 	Strongly recommended 	The duration of the video in seconds. Value must be between 0 and 28800 (8 hours). Non-digit characters are disallowed.
<video:expiration_date> 	Recommended when applicable 	The date after which the video will no longer be available, in W3C format. Acceptable values are complete date (YYYY-MM-DD) and complete date plus hours, minutes and seconds, and timezone (YYYY-MM-DDThh:mm:ss+TZD). For example, 2007-07-16T19:20:30+08:00. Don't supply this information if your video does not expire.
<video:rating> 	Optional 	The rating of the video. The value must be float number in the range 0.0-5.0.
<video:content_segment_loc> 	Optional 	Note: Use <video:content_segment_loc> only in conjunction with <video:player_loc>. If you publish your video as a series of raw videos (for example, if you submit a full movie as a continuous series of shorter clips), you can use the <video:content_segment_loc> to supply us with a series of URLs, in the order in which they should be concatenated to recreate the video in its entirety. Each URL should point to a .mpg, .mpeg, .mp4, .m4v, .mov, .wmv, .asf, .avi, .ra, .ram, .rm, .flv, or other video file format. It should not point to any Flash content. The value of the optional attribute duration specifies the length of each clip in seconds.

For example:

<video:content_segment_loc duration="600">http://example.com/url1</video:content_segment_loc>
<video:content_segment_loc duration="500">http://example.com/url2</video:content_segment_loc>
<video:content_segment_loc duration="700">http://example.com/url3</video:content_segment_loc>
<video:content_segment_loc duration="600">http://example.com/url4</video:content_segment_loc>

<video:view_count> 	Optional 	The number of times the video has been viewed.
<video:publication_date> 	Optional 	The date the video was first published, in W3C format. Acceptable values are complete date (YYYY-MM-DD) and complete date plus hours, minutes and seconds, and timezone (YYYY-MM-DDThh:mm:ss+TZD). For example, 2007-07-16T19:20:30+08:00.
<video:tag> 	Optional 	A tag associated with the video. Tags are generally very short descriptions of key concepts associated with a video or piece of content. A single video could have several tags, but it should belong to onlyone category. For example, a video about grilling food may belong in the Grilling category, but could be tagged "steak", "meat", "summer", and "outdoor". Create a new <video:tag> element for each tag associated with a video. A maximum of 32 tags is permitted.
<video:category> 	Optional 	The video's category. For example, cooking. The value should be a string no longer than 256 characters. In general, categories are broad groupings of content by subject. Each video can belong to only a single category. For example, a site about cooking could have categories for Broiling, Baking, and Grilling.
<video:family_friendly> 	Optional 	No if the video should be available only to users with SafeSearch turned off.
<video:restriction> 	Optional 	A list of countries where the video may or may not be played, in space-delimited ISO 3166 format. The required attribute "relationship" specifies whether the video is restricted or permitted for the specified countries. Allowed values are allow or deny. Only one <video:restriction> tag can appear for each video. If there is no <video:restriction> tag, it is assumed that the video can be played in all territories.
<video:gallery_loc> 	Optional 	A link to the gallery (collection of videos) in which this video appears. Only one <video:gallery_loc> tag can be listed for each video. The value of the optional attribute title indicates the title of the gallery.
<video:price> 	Optional 	The price to download or view the video. The required attribute currency specifies the currency in ISO 4217 format. More than one <video:price> element can be listed (for example, in order to specify various currencies).
<video:requires_subscription> 	Optional 	Indicates whether a subscription (either paid or free) is required to view the video. Allowed values are yes or no.
<video:uploader> 	Optional 	A name or handle of the video's uploader. Only one <video:uploader> is allowed per video. The optional attribute info specifies the URL of a webpage with additional information about this uploader. This URL must be on the same domain as the <loc> tag.


*/
  return '';
}


function
tv2_sitemap ()
{
//    header ('Content-type: text/xml');
  header ('Content-type: application/xml');
//    header ('Content-type: text/xml-external-parsed-entity');
//    header ('Content-type: application/xml-external-parsed-entity');
//    header ('Content-type: application/xml-dtd');
  $config_xml = config_xml ();

//  echo '<pre>';
//  print_r ($config_xml);

  $p = '';
  $p .= '<?xml version="1.0" encoding="UTF-8"?>'."\n"
       .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

  for ($i = 0; isset ($config_xml->category[$i]); $i++)
    if ($config_xml->category[$i]->name[0])
    $p .= '<url>'."\n"
         .'  <loc>http://'.$_SERVER['SERVER_NAME'].'/?c='.$config_xml->category[$i]->name.'</loc>'."\n"
/*
The formats are as follows. Exactly the components shown here must be present, with exactly this punctuation. Note that the "T" appears literally in the string, to indicate the beginning of the time element, as specified in ISO 8601.

   Year:
      YYYY (eg 1997)
   Year and month:
      YYYY-MM (eg 1997-07)
   Complete date:
      YYYY-MM-DD (eg 1997-07-16)
   Complete date plus hours and minutes:
      YYYY-MM-DDThh:mmTZD (eg 1997-07-16T19:20+01:00)
   Complete date plus hours, minutes and seconds:
      YYYY-MM-DDThh:mm:ssTZD (eg 1997-07-16T19:20:30+01:00)
   Complete date plus hours, minutes, seconds and a decimal fraction of a second
      YYYY-MM-DDThh:mm:ss.sTZD (eg 1997-07-16T19:20:30.45+01:00)
*/
         .'<lastmod>'.strftime ('%F' /* 'T%T%Z' */).'</lastmod>'."\n"
         .'<changefreq>always</changefreq>'."\n"
         .tv2_sitemap_video ()
         .'</url>'."\n";
  $p .= '</urlset>';

  return $p;
}


}


?>