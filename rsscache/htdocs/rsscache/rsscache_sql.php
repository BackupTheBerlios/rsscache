<?php
if (!defined ('TV2_SQL_PHP'))
{
define ('TV2_SQL_PHP', 1);
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('misc/sql.php');


function
tv2_sql_move ($rsstool_url_crc32, $new_category)
{
  // move item to different category
  global $tv2_dbhost,
         $tv2_dbuser,
         $tv2_dbpass,
         $tv2_dbname;
  $debug = 0;

  $db = new misc_sql;  
  $db->sql_open ($tv2_dbhost,
                 $tv2_dbuser,
                 $tv2_dbpass,
                 $tv2_dbname);

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = \''.$db->sql_stresc ($new_category).'\''
                .' WHERE rsstool_url_crc32 = '.$db->sql_stresc ($rsstool_url_crc32).';';

  $db->sql_write ($sql_query_s, 0, $debug);

  $db->sql_close ();
}


function
tv2_sql_vote ($rsstool_url_crc32, $new_score)
{
  global $tv2_dbhost,
         $tv2_dbuser,
         $tv2_dbpass,
         $tv2_dbname;
  $debug = 0;

  $db = new misc_sql;  
  $db->sql_open ($tv2_dbhost,
                 $tv2_dbuser, 
                 $tv2_dbpass, 
                 $tv2_dbname);

  $sql_query_s = 'SELECT tv2_votes,tv2_score FROM rsstool_table'
                .' WHERE rsstool_url_crc32 = '.$db->sql_stresc ($rsstool_url_crc32).';';
  $db->sql_write ($p, 0, $debug);
  $r = $db->sql_read (1, $debug);

  if ($new_score > 0)
    $new_score = ($r[0]['tv2_votes'] * $r[0]['tv2_score'] + $new_score) / ($r[0]['tv2_votes'] + 1);
  else
    $new_score = $r[0]['tv2_score'];

  $sql_query_s = 'UPDATE rsstool_table SET tv2_votes = '.($r[0]['tv2_votes'] + 1).',tv2_score = '.$new_score
                .' WHERE rsstool_url_crc32 = '.$db->sql_stresc ($rsstool_url_crc32).';';

  $db->sql_write ($p, 1, $debug);

  $db->sql_close ();
}


function
tv2_sql_restore ($rsstool_url_crc32)
{
  // restore original category
  global $tv2_dbhost,
         $tv2_dbuser,
         $tv2_dbpass,
         $tv2_dbname;
  $debug = 0;

  $db = new misc_sql;  
  $db->sql_open ($tv2_dbhost,
                 $tv2_dbuser,
                 $tv2_dbpass,
                 $tv2_dbname);

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = tv2_category'
                .' WHERE rsstool_url_crc32 = '.$db->sql_stresc ($rsstool_url_crc32).';';

  $db->sql_write ($sql_query_s, 0, $debug);

  $db->sql_close ();
}


function
tv2_sql_stats ($category = NULL)
{
  global $tv2_dbhost,
         $tv2_dbuser,
         $tv2_dbpass,
         $tv2_dbname;
//  global $memcache_expire;
  $debug = 0;
  $f = get_request_value ('f');

  $stats = array ('videos' => 0, 'videos_today' => 0, 'videos_7_days' => 0, 'videos_30_days' => 0, 'days' => 0);

  $db = new misc_sql;  
  $db->sql_open ($tv2_dbhost,
                 $tv2_dbuser,
                 $tv2_dbpass,
                 $tv2_dbname);

  // downloaded items since...
  // ...always
  $sql_query_s = 'SELECT COUNT(*) FROM rsstool_table WHERE 1';
  if ($category)
    $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';
  $sql_query_s .= ';';

  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);

  $stats['videos'] = (int) $r[0][0];

  if ($f == 'stats')
    {
      // ...today
      $sql_query_s = 'SELECT COUNT(*) FROM rsstool_table WHERE rsstool_dl_date > '.mktime (0, 0, 0);
      if ($category)
        $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';
      $sql_query_s .= ';';

      $db->sql_write ($sql_query_s, 0, $debug);
      $r = $db->sql_read (0, $debug);

      $stats['videos_today'] = (int) $r[0][0];


      // ...last 7 days
      $sql_query_s = 'SELECT COUNT(*) FROM rsstool_table WHERE rsstool_dl_date > '.mktime (0, 0, 0, date ('n'), date ('j') - 7);
      if ($category)
        $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';
      $sql_query_s .= ';';

      $db->sql_write ($sql_query_s, 0, $debug);
      $r = $db->sql_read (0, $debug);

      $stats['videos_7_days'] = (int) $r[0][0]; 


      // ...last 30 days
      $sql_query_s = 'SELECT COUNT(*) FROM rsstool_table WHERE rsstool_dl_date > '.mktime (0, 0, 0, date ('n'), date ('j') - 30);
      if ($category)
        $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';
      $sql_query_s .= ';';

      $db->sql_write ($sql_query_s, 0, $debug);
      $r = $db->sql_read (0, $debug);

      $stats['videos_30_days'] = (int) $r[0][0];
    }

  // total items downloaded...
  $sql_query_s = 'SELECT rsstool_dl_date FROM rsstool_table WHERE 1';
  if ($category)
    $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';
  $sql_query_s .= ' ORDER BY rsstool_dl_date ASC'
                   .' LIMIT 1'
                   .';';

  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);

  if (isset ($r[0]))
    $stats['days'] = (int) ((time () - (int) $r[0][0]) / 86400);

  $db->sql_close ();

  return $stats;
}


function
tv2_sql_normalize ($db, $d, $c, $f)
{
  global $tv2_root,
         $tv2_link;
  $debug = 0;

  // make array contents unique by their title
  if ($f == 'related')
    for ($i = 0; isset ($d[$i]) && isset ($d[$i + 1]); $i++)
      while (trim ($d[$i]['rsstool_title']) == trim ($d[$i + 1]['rsstool_title']))
        $d = array_splice ($d, $i + 1, 1);

  for ($i = 0; isset ($d[$i]); $i++)
    {
      // HACK: fix garbage coming from the database
      if (strstr ($d[$i]['rsstool_url'], 'www.google.com'))
        {
          // remove eventual google redirect
          $offset = strpos ($d[$i]['rsstool_url'], '?q=') + 3;
          $len = strpos ($d[$i]['rsstool_url'], '&source=') - $offset;
          $d[$i]['rsstool_url'] = substr ($d[$i]['rsstool_url'], $offset, $len);

          // desc
          $offset = 0;
          $len = strrpos ($d[$i]['rsstool_desc'], '<div ');
          if ($len)
            $d[$i]['rsstool_desc'] = substr ($d[$i]['rsstool_desc'], $offset, $len);
        }
      else if (strstr ($d[$i]['rsstool_url'], 'news.google.com'))
        {
          // remove eventual google redirect
          $offset = strpos ($d[$i]['rsstool_url'], '&url=') + 5;
          $len = strpos ($d[$i]['rsstool_url'], '&usg=') - $offset;
          $d[$i]['rsstool_url'] = substr ($d[$i]['rsstool_url'], $offset, $len);
        }
      else if (strstr ($d[$i]['rsstool_url'], 'www.youtube.com'))
        {
          $d[$i]['rsstool_url'] = str_replace ('&feature=youtube_gdata', '', $d[$i]['rsstool_url']);
        }
      if (strstr ($d[$i]['rsstool_url'], '.xvideos.com'))
        {
          $d[$i]['rsstool_desc'] = '';
        }

      // HACK: fix
      $d[$i]['tv2_category'] = trim ($d[$i]['tv2_category']);
      $d[$i]['tv2_moved'] = trim ($d[$i]['tv2_moved']);

      // demux
      $d[$i]['tv2_demux'] = widget_media_demux ($d[$i]['rsstool_url']);

      // strip any tags from the desc
      $p = $d[$i]['rsstool_desc'];
      $p = str_replace ('>', '> ', $p);
      $p = strip_tags ($p);
      $p = str_replace (array ('  ', '  ', '  ', '  ', '  '), ' ', $p);
      $d[$i]['rsstool_desc'] = $p;

      // TODO: search highlights
//      $d[$i]['highlight'] = array ();
    }

  return $d;
}



function
tv2_sql_query2boolean_escape_func ($s)
{
  if (strlen (trim ($s, ' +-')) < 4)
//  if (strlen (trim ($s)) < 4)
    return false;

  for ($i = 0; $s[$i]; $i++)
    if (!isalnum ($s[$i]) && !in_array ($s[$i], array ('-', '+', /* '(', ')', '"' */)))
      return false;
 
  return true;
}
  

function
tv2_sql_query2boolean_escape ($s)
{
  $a = explode (' ', strtolower ($s));
  for ($i = 0; isset ($a[$i]); $i++)
    $a[$i] = trim ($a[$i]);
  // TODO: more sensitivity instead of array_filter()
  $a = array_filter ($a, 'tv2_sql_query2boolean_escape_func');
  $a = array_merge (array_unique ($a));
  
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);

  $s = implode (' ', $a);
  $s = trim ($s);

  return $s;
}


function
tv2_sql_query2boolean ($q)
{
  /*
    parses google style search query into
      boolean full-text search query

    IMPORTANT: replaces mysql_real_escape_string()
  */

  global $tv2_debug_sql;
  $debug = $tv2_debug_sql;

  /*
    google style

    ALL of these words: test1 test2
    the exact wording or phrase: "test3  " "test4  "
    ONE OR MORE of these words: test5 OR test6
    ANY of these unwanted words: -test7 -test8

    1) test1 test2 test5 OR test6 "test3  " "test4  " -test7 -test8

    2) http://www.google.com/search?q=test1+test2+test5+OR+test6+%22test3++%22+%22test4++%22+-test7+-test8
  */

  $p = str_ireplace (' OR ', ' ', $q);
  $p = str_ireplace ('\\', '', $p); // unescape query
  $p = tv2_sql_query2boolean_escape ($p); 
  $match = $p;

  // DEBUG
  if ($debug)
    echo '<pre><tt>'
        .'query: "'.$q.'"'."\n"
//        .sprint_r ($a)."\n"
        .'match: \''.$match.'\''."\n";

  return $match;
}


function
tv2_sql_match_func ($db, $q, $filter)
{
  // filter
  if ($filter)
    $s .= $filter.' '; // boolean full-text search query

  // query
  if ($q)
    $s .= '+('.tv2_sql_query2boolean ($q).')';

  $s = trim ($s);

  if (!strlen ($s))
    return '';

  $p = '';

  $p .= ' AND MATCH ('
       .' rsstool_keywords'
       .' ) AGAINST (\''
       .$s
       .'\''
       .' IN BOOLEAN MODE'
       .' )';

  return $p;
}


function
tv2_sql ($c, $q, $f, $v, $start, $num)
{
  global $tv2_dbhost,
         $tv2_dbuser,
         $tv2_dbpass, 
         $tv2_dbname,
         $tv2_isnew,
         $tv2_root,
         $tv2_enable_search;
  global $tv2_debug_sql;
  $debug = $tv2_debug_sql;
//  $debug = 1;

  $db = new misc_sql;
  $db->sql_open ($tv2_dbhost,
                 $tv2_dbuser,
                 $tv2_dbpass,
                 $tv2_dbname);

  $q = get_request_value ('q'); // we ignore the arg and make sure we get an unescaped one
  $c = $db->sql_stresc ($c);
  $v = $db->sql_stresc ($v);
  $start = $db->sql_stresc ($start);
  $num = $db->sql_stresc ($num);

//SELECT rsstool_table.desc FROM rsstool_table WHERE rsstool_table.id = (
// SELECT id_table.id FROM id_table LEFT_JOIN id_table ON
// id_table.keyword_id = keywords_table_id WHERE(keywords_table.  keyword =
// "search" OR keywords_table.keyword = "search2"));


  $sql_query_s = '';
//  $sql_query_s .= 'EXPLAIN ';
//  $sql_query_s .= 'SELECT * FROM rsstool_table WHERE 1';
  $sql_query_s .= 'SELECT'
                  .' rsstool_url,'
                  .' rsstool_url_crc32,'
                  .' rsstool_title,'
                  .' rsstool_desc,'
                  .' rsstool_dl_date,'
                  .' rsstool_date,'
                  .' tv2_category,'
                  .' tv2_moved,'
                  .' rsstool_media_duration,'
                  .' rsstool_keywords,'
                  .' tv2_votes,'
                  .' tv2_score'
                  .' FROM rsstool_table';

  if ($v) // direct
    {
      $sql_query_s .= ' WHERE ( rsstool_url_crc32 = '.$v.' )';
      $sql_query_s .= ' LIMIT 1';
    }
  else
    {
      $sql_query_s .= ' WHERE 1';

      // category
      if ($c)
        $sql_query_s .= ' AND ( `tv2_moved` = \''.$c.'\' )';

      // filter
      $filter = NULL;
      if ($c)
        {
          $category = config_xml_by_category ($c);

          if ($category)
            if ($category->filter)
              if (strlen ($category->filter))
                $filter = $category->filter;
        }

      if ($tv2_enable_search)
        $sql_query_s .= tv2_sql_match_func ($db, $q, $filter);

      // functions
      if ($f == 'new')
        $sql_query_s .= ' AND ( rsstool_dl_date > '.(time () - $tv2_isnew).' )';
      else if ($f == '0_5min')
        $sql_query_s .= ' AND ( rsstool_media_duration > 0 && rsstool_media_duration < 301 )';
      else if ($f == '5_10min')
        $sql_query_s .= ' AND ( rsstool_media_duration > 300 && rsstool_media_duration < 601 )';
      else if ($f == '10_min')
        $sql_query_s .= ' AND ( rsstool_media_duration > 600 )';
      else if ($f == 'cloud' || $f == 'wall')
        $sql_query_s .= ' AND ( rsstool_url LIKE \'%youtube%\' )'; // TODO: thumbnails of all videos

      // sort
      if ($f == 'related') // we sort related by title for playlist
        $sql_query_s .= ' ORDER BY rsstool_title ASC';
      else if ($f == 'score')
        $sql_query_s .= ' ORDER BY tv2_score ASC';
      else if ($f == 'new')
        $sql_query_s .= ' ORDER BY rsstool_dl_date DESC';
      else
        $sql_query_s .= ' ORDER BY rsstool_date DESC';

      // limit
      $sql_query_s .= ' LIMIT '.$start.','.$num;
    }

  $db->sql_write ($sql_query_s, 1, $debug);

  $d = $db->sql_read (1, 0 /* $debug */);

  $d = tv2_sql_normalize ($db, $d, $c, $f);

  // DEBUG
//  echo '<tt><pre>';
//  print_r ($d);

  $db->sql_close ();


  return $d;
}


}


?>