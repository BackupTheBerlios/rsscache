#!/usr/bin/php-cgi -q
<?php
//error_reporting(E_ALL | E_STRICT);
require_once ('../htdocs/config.php');
require_once ('../htdocs/misc/misc.php');
require_once ('../htdocs/misc/sql.php');


if ($argc < 3)
  {
    echo 'USAGE: tv2.php CONFIG_XML CATEGORY ORIGINAL_RSS RSSTOOL_SQL RSSTOOL_RSS'."\n\n";
    exit;
  }


$config = simplexml_load_file ($argv[1]);
$tv2_category = $argv[2];

// enhance quality and reduce db size by using the filter in this stage
//   (one should not rely on the search engine of the RSS feed source for quality)
$filter = $config->category[$tv2_category]->filter;

//$rss_orig = file_get_contents ($argv[3]);
$sql_array = file ($argv[4], FILE_SKIP_EMPTY_LINES|FILE_TEXT);
$rss = simplexml_load_file ($argv[5]);
// DEBUG
//print_r ($rss);
//exit;
   

function
get_youtube_thumbnail ($rsstool_url)
{
  global $tv2_root;
  global $thumbnails_path;

  // DEBUG
//  echo $rsstool_url."\n";

  $p = urldecode ($rsstool_url);
  $start = strpos ($p, 'watch?v=');
  if ($start)
    $start += 8;
  $p = substr ($p, $start);
  if (strpos ($p, '&'))
    {
      $len = strpos ($p, '&');
      $s = substr ($p, 0, $len);
    }
  else $s = $p;

  // DEBUG
//  echo $s."\n";

  if (!($s[0]))
    return;

  for ($i = 0; $i < 4; $i++)
    {
      // download thumbnail
      $url = 'http://i.ytimg.com/vi/'.$s.'/'.$i.'.jpg';

      $filename = $s.'_'.$i.'.jpg';
      $path = $tv2_root.'/thumbnails/youtube/'.$filename;

      // DEBUG
//      echo $url."\n";

      if (file_exists ($path)) // do not overwrite existing files
        {
          echo 'WARNING: file '.$path.' exists, skipping'."\n";
          return;
        }
      else echo $path."\n";

      misc_download ($url, $path);
//      echo 'wget -nc "'.$url.'" -O "'.$filename.'"'."\n"; // -N?
//      echo 'rm "'.$filename.'"'."\n"; // remove old thumbs
    }
}


$db = new misc_sql;   
$db->sql_open ($tv2_dbhost, $tv2_dbuser, $tv2_dbpass, $tv2_dbname);


for ($i = 0; $sql_array[$i]; $i++)
{
  $sql_query_s = $sql_array[$i];

//  $sql_query_s = str_replace ('INSERT IGNORE INTO', 'INSERT INTO', $sql_query_s);
//  $sql_query_s = str_replace ('rsstool_table', 'quakeunity', $sql_query_s);

  if (!strncmp ($sql_query_s, '-- UPDATE', 9))
    {
      // activate UPDATE (fixes old broken inserts on dupes)
//      $sql_query_s = str_replace ('-- UPDATE rsstool_table', 'UPDATE rsstool_table', $sql_query_s);
    }
  else if (strstr ($sql_query_s, '`rsstool_desc`') &&
           strstr ($sql_query_s, '\');'))
    {
      for ($j = 0; isset ($rss->channel->item[$j]); $j++)
        {
          $item = $rss->channel->item[$j];

          if (strstr ($sql_query_s, (string) $item->link))
            {
              // get the keywords
              $tv2_related = misc_get_keywords ($item->title, 1); // isalpha
              $tv2_keywords = misc_get_keywords_html ($item->title.' '.$item->description, 0); // isalnum
              $tv2_duration = $item->media_duration;

              // extend the rsstool_table with tv2 columns
              $p = str_replace ('`rsstool_media_duration`',
                                '`rsstool_media_duration`, `tv2_category`, `tv2_moved`, `tv2_duration`, `tv2_related`, `tv2_keywords`',
                                $sql_query_s);
              $sql_query_s = str_replace ('\');',
                                '\', \''
                               .$tv2_category
                               .'\', \''
                               .$tv2_category
                               .'\', '
                               .$tv2_duration
                               .', \''
                               .$db->sql_stresc ($tv2_related)
                               .'\', \''
                               .$db->sql_stresc ($tv2_keywords)
                               .'\');',
                                $p);

              // get thumbnails
              if (strstr ($rss->channel->item[$j]->link, '.youtube.'))
                get_youtube_thumbnail ($rss->channel->item[$j]->link);

              break;
            }
        }
    }

  $db->sql_write ($sql_query_s, 0, 0);
}

$db->sql_close ();



exit;


?>