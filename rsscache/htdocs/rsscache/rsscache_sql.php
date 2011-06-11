<?php
if (!defined ('TV2_SQL_PHP'))
{
define ('TV2_SQL_PHP', 1);
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('misc/sql.php');


$tv2_sql_db = NULL;


function
tv2_sql_open ()
{
  // move item to different category
  global $tv2_sql_db;
  global $tv2_dbhost,
         $tv2_dbuser,
         $tv2_dbpass,
         $tv2_dbname;
  $debug = 0;
  $tv2_sql_db = new misc_sql;
  $tv2_sql_db->sql_open ($tv2_dbhost,
                 $tv2_dbuser,
                 $tv2_dbpass,
                 $tv2_dbname);
}


function
tv2_sql_close ()
{
  global $tv2_sql_db;
  $tv2_sql_db->sql_close ();
}


function
tv2_sql_move ($rsstool_url_crc32, $new_category)
{
  // move item to different category
  global $tv2_sql_db;
  $debug = 0;

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = \''.$tv2_sql_db->sql_stresc ($new_category).'\''
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32);

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
}


function
tv2_sql_vote ($rsstool_url_crc32, $new_score)
{
  global $tv2_sql_db;
  $debug = 0;

  $sql_query_s = 'SELECT tv2_votes,tv2_score FROM rsstool_table'
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32);
  $tv2_sql_db->sql_write ($p, 0, $debug);
  $r = $tv2_sql_db->sql_read (1, $debug);

  if ($new_score > 0)
    $new_score = ($r[0]['tv2_votes'] * $r[0]['tv2_score'] + $new_score) / ($r[0]['tv2_votes'] + 1);
  else
    $new_score = $r[0]['tv2_score'];

  $sql_query_s = 'UPDATE rsstool_table SET tv2_votes = '.($r[0]['tv2_votes'] + 1).',tv2_score = '.$new_score
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32);

  $tv2_sql_db->sql_write ($p, 1, $debug);
}


function
tv2_sql_restore ($rsstool_url_crc32)
{
  // restore original category
  global $tv2_sql_db;
  $debug = 0;

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = tv2_category'
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32);

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
}


function
tv2_sql_stats ($category = NULL)
{
  global $tv2_sql_db;
  $debug = 0;
  $f = get_request_value ('f');

  $stats = array ('items' => 0, 'items_today' => 0, 'items_7_days' => 0, 'items_30_days' => 0, 'days' => 0);
/*
  $a = array ();
  if ($category)
    $a[] = 'tv2_moved = \''.$category.'\'';

  // downloaded items since start
  $sql_query_s = 'SELECT COUNT( 1 ) AS tv2_rows FROM rsstool_table';
  if (isset ($a[0]))
    $sql_query_s .= ' WHERE ( '.implode (' AND ', $a).' )';

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
  $r = $tv2_sql_db->sql_read (0, $debug);
  if (isset ($r[0]))
    $stats['items'] = (int) $r[0][0];

  // days since start
  $sql_query_s = 'SELECT MIN(rsstool_dl_date) FROM rsstool_table';
  if (isset ($a[0]))
    $sql_query_s .= ' WHERE ( '.implode (' AND ', $a).' )';

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
  $r = $tv2_sql_db->sql_read (0, $debug);
  if (isset ($r[0]))
    $stats['days'] = (int) ((time () - (int) $r[0][0]) / 86400);
*/
/*
  if ($category)
  if ($f == 'stats')
    {
      $p = 'SELECT COUNT( 1 ) FROM rsstool_table';

      // downloaded items today
      $a[1] = 'rsstool_dl_date > '.mktime (0, 0, 0);
      $sql_query_s = $p.' WHERE ( '.implode (' AND ', $a).' )';

      $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
      $r = $tv2_sql_db->sql_read (0, $debug);
      if (isset ($r[0]))
        $stats['items_today'] = (int) $r[0][0];

      // downloaded items last 7 days
      $a[1] = 'rsstool_dl_date > '.mktime (0, 0, 0, date ('n'), date ('j') - 7);
      $sql_query_s = $p.' WHERE ( '.implode (' AND ', $a).' )';

      $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
      $r = $tv2_sql_db->sql_read (0, $debug);
      if (isset ($r[0]))
        $stats['items_7_days'] = (int) $r[0][0]; 

      // downloaded items last 30 days
      $a[1] = 'rsstool_dl_date > '.mktime (0, 0, 0, date ('n'), date ('j') - 30);
      $sql_query_s = $p.' WHERE ( '.implode (' AND ', $a).' )';

      $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
      $r = $tv2_sql_db->sql_read (0, $debug);
      if (isset ($r[0]))
        $stats['items_30_days'] = (int) $r[0][0];
    }
*/
  return $stats;
}


function
tv2_sql_normalize ($tv2_sql_db, $d, $c, $f)
{
  global $tv2_root,
         $tv2_link,
         $tv2_related_search;
  $debug = 0;

  // make array contents unique by their title
  if ($tv2_related_search == 1)
    if ($f == 'related')
      if (isset ($d[0]))
        for ($i = 0; isset ($d[$i + 1]); $i++)
          while (trim ($d[$i]['rsstool_title']) == trim ($d[$i + 1]['rsstool_title']))
            $d = array_splice ($d, $i + 1, 1);

  for ($i = 0; isset ($d[$i]); $i++)
    {
      // demux
      $d[$i]['tv2_demux'] = widget_media_demux ($d[$i]['rsstool_url']);

      // TODO: search highlights
//      $d[$i]['highlight'] = array ();
    }

  return $d;
}


function
tv2_sql_keyword_func_func ($a)
{
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);

  $p = '';
//  $p .= ' keyword_table.rsstool_keyword_crc32 IN ( ';
//    $func = 'crc32'; // 0xffffffff keywords
//  $p .= ' keyword_table.rsstool_keyword_crc24 IN ( ';
//  $func = 'misc_crc24'; // 0xffffff keywords
  $p .= ' keyword_table.rsstool_keyword_crc16 IN ( ';
  $func = 'misc_crc16'; // 0xffff keywords
  for ($i = 0; isset ($a[$i]); $i++)
    {
      if ($i > 0)
        $p .= ', ';
      $p .= $func ($a[$i]).'';
    }
  return $p;
}


function
tv2_sql_keyword_func ($any = NULL, $require = NULL, $exclude = NULL)
{
  $debug = 0;

  $p = '';
  if (trim ($require) != '')
    {
  $p .= ' rsstool_table.rsstool_url_crc32'
       .' IN ( ';
      $p .= ' SELECT temp.rsstool_url_crc32'
           .' FROM ('
           .' SELECT keyword_table.rsstool_url_crc32, COUNT( keyword_table.rsstool_url_crc32 ) AS \'found\''
           .' FROM keyword_table'
           .' WHERE';

      $s = misc_get_keywords ($require, 0); // isalnum()
      $a = explode (' ', $s);
      $a = misc_array_unique_merge ($a);
      $p .= tv2_sql_keyword_func_func ($a);

      $p .= ' )'
           .' GROUP BY keyword_table.rsstool_url_crc32'
           .' HAVING found = '.count ($a)
           .' ORDER BY found DESC';
      $p .= ' ) temp';
  $p .= ' )';
      // DEBUG
      if ($debug == 1)
        echo 'require: '.$p.' )<br><br>';
    }
  else if (trim ($any) != '')
    {
  $p .= ' rsstool_table.rsstool_url_crc32'
       .' IN ( ';
      $p .= ' SELECT DISTINCT rsstool_url_crc32'
           .' FROM keyword_table'
           .' WHERE';

      $s = misc_get_keywords ($any, 0); // isalnum()
      $a = explode (' ', $s);
      $a = misc_array_unique_merge ($a);
      $p .= tv2_sql_keyword_func_func ($a);

      $p .= ' )';
  $p .= ' )';
      // DEBUG
      if ($debug == 1)
        echo 'any: '.$p.' )<br><br>';
    }

  // DEBUG
//  echo $p;
//exit;
  return $p;
}


function
tv2_sql_extern ($q, $start, $num)
{
  // like tv2_sql() but uses the youtube db instead ;)
  global $tv2_feature;
  global $tv2_tor_enabled;

  $v_segments = get_request_value ('v_segments');
  $v_textarea = get_request_value ('v_textarea');
  $v_user = get_request_value ('v_user');
  $v_playlist_id = get_request_value ('v_playlist_id');
  $v_stripdir = get_request_value ('v_stripdir');

  $links = '';

  // links or playlist file contents
  if ($v_textarea)
    $links .= ' '.$v_textarea;

  // search
  if ($q)
      {
        $s = $q;
        if ($v_segments)
          if ($v_segments != '')
            {
              $s .= ' +(part OR pl';
              for ($i = 0; $i < 20; $i++)
                $s .= ' OR "'.($i + 1).'/"';
              $s .= ')';
            }
        $rss = youtube_get_rss ($s, NULL, NULL, $tv2_tor_enabled);

        for ($i = 0; isset ($rss->channel->item[$start + $i]) && $i < $num; $i++)
          if (isset ($rss->channel->item[$start + $i]->link))
            $links .= ' '.$rss->channel->item[$start + $i]->link;
      }

  if ($v_user)
      {
        $rss = youtube_get_rss ($s, trim ($v_user), NULL, $tv2_tor_enabled);

        for ($i = 0; isset ($rss->channel->item[$start + $i]) && $i < $num; $i++)
          if (isset ($rss->channel->item[$start + $i]->link))
            $links .= ' '.$rss->channel->item[$start + $i]->link;
      }

  if ($v_playlist_id)
      {
        $rss = youtube_get_rss ('', NULL, trim ($v_playlist_id), $tv2_tor_enabled);

        for ($i = 0; isset ($rss->channel->item[$start + $i]) && $i < $num; $i++)
          if (isset ($rss->channel->item[$start + $i]->link))
            $links .= ' '.$rss->channel->item[$start + $i]->link;
      }

  if ($v_stripdir)
      {
        $a = tv2_stripdir ($v_stripdir, $start, $num);
        $links .= implode ($a, ' ');
      }

  // normalize youtube links
  $links = trim (strip_tags (urldecode ($links)));
  if ($links == '')
    $links = $tv2_feature;
  $a = explode (' ', urldecode ($links));
  $v = array ();
  for ($i = 0; isset ($a[$i]); $i++)
    {
      $p = trim ($a[$i]);
      if ($p != '')
        {
          $p = youtube_get_videoid ($p);
          if ($p != '')
            $v[] = 'http://www.youtube.com/watch?v='.$p;
        }
    }

  if ($v_stripdir)
      {
        $a = tv2_stripdir ($v_stripdir, $start, $num);
        for ($i = 0; isset ($a[$start + $i]) && $i < $num; $i++)
          $v[] = $a[$start + $i];
      }

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($v);

  return $v;
}


function
tv2_sql_query2boolean_escape_func ($s)
{
  if (strlen (trim ($s, ' +-')) < 4)
//  if (strlen (trim ($s)) < 4)
    return false;

  for ($i = 0; $i < strlen ($s); $i++)
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
  $a = misc_array_unique_merge ($a);
  
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
tv2_sql ($c, $q, $f, $v, $start, $num, $extern = 0)
{
  global $tv2_sql_db,
         $tv2_isnew,
         $tv2_root,
         $tv2_enable_search,
         $tv2_related_search,
         $tv2_use_dl_date;
  global $tv2_debug_sql;
  $debug = $tv2_debug_sql;
//  $debug = 1;

  $v_segments = get_request_value ('v_segments');
//  $q = get_request_value ('q'); // we ignore the arg and make sure we get an unescaped one
//  $c = $tv2_sql_db->sql_stresc ($c);
//  $v = $tv2_sql_db->sql_stresc ($v);
//  $start = $tv2_sql_db->sql_stresc ($start);
//  $num = $tv2_sql_db->sql_stresc ($num);

  // extern SQL
  if ($extern == 1)
    {
      $d = tv2_sql_extern ($q, $start, $num);

//  $d = tv2_sql_normalize ($tv2_sql_db, $d, $c, $f);

  // DEBUG
//  echo '<tt><pre>';
//  print_r ($d);

      return $d;
    }

  // local SQL
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
//                  .' tv2_category,'
                  .' tv2_moved,'
                  .' rsstool_media_duration,'
                  .' rsstool_keywords'
//                  .' tv2_votes,'
//                  .' tv2_score'
                  .' FROM rsstool_table'
;

  $a = array ();
  if ($v) // direct
    {
      $sql_query_s .= ' WHERE rsstool_url_crc32 = '.$v
                     .' LIMIT 1';
    }
  else
    {
      // category
      if ($c)
        $a[] = 'tv2_moved = \''.$c.'\'';
//}
//  if ($q)
//    {
      // filter
// TODO: merge filter with require, exclude code, etc.
      $filter = NULL;
      if ($c)
        {
          $category = config_xml_by_category ($c);

          if ($category)
            if ($category->filter)
              if (strlen ($category->filter))
                $filter = $category->filter;
        }

      // search
      if ($tv2_enable_search)
        {
          $v_any = '';
          $v_require = '';
          $v_exclude = '';
          $s = trim ($q.($filter ? ' '.$filter : ''));
          // DEBUG
          if ($debug == 1)
            echo 'search: '.$s.'<br>';
          $b = explode (' ', $s);
          for ($i = 0; isset ($b[$i]); $i++)
            {
              $s = trim ($b[$i]);
              if ($s == '') continue;
              if ($s[0] == '+')
                $v_require .= ' '.substr ($s, 1);
              else if ($s[0] == '-')
                $v_exclude .= ' '.substr ($s, 1);
              else
                $v_any .= ' '.$s;
            }

          if ($v_segments)
            if ($v_segments != '')
              $v_require .= ' part';

          // DEBUG
//          if ($debug == 1)
            echo 'any: '.$v_any.'<br>require: '.$v_require.'<br>exclude: '.$v_exclude.'<br>';
          // keyword_search
          $s = tv2_sql_keyword_func ($v_any, $v_require, $v_exclude);
          if ($s != NULL)
            $a[] = $s;
        }

      // functions
      if ($f == 'new')
        $a[] = 'rsstool_dl_date > '.(time () - $tv2_isnew).'';
      else if ($f == '0_5min')
        $a[] = 'rsstool_media_duration BETWEEN 0 AND 301';
      else if ($f == '5_10min')
        $a[] = 'rsstool_media_duration BETWEEN 300 AND 601';
      else if ($f == '10_min')
        $a[] = 'rsstool_media_duration > 600';
      else if ($f == '1_h')
        $a[] = 'rsstool_media_duration > 3600';

      if (isset ($a[0]))
        $sql_query_s .= ' WHERE ( '.implode (' AND ', $a).' )';

      // sort
      if ($tv2_related_search && $f == 'related') // we sort related by title for playlist
//        $sql_query_s .= ' ORDER BY rsstool_date DESC';
        $sql_query_s .= ' ORDER BY rsstool_title ASC';
//      else if ($f == 'score')
//        $sql_query_s .= ' ORDER BY tv2_score ASC';
      else if ($f == 'new' || $tv2_use_dl_date)
        $sql_query_s .= ' ORDER BY rsstool_dl_date DESC';
      else
        $sql_query_s .= ' ORDER BY rsstool_date DESC';

      // limit
      $sql_query_s .= ' LIMIT '.$start.','.$num;
    }

  // DEBUG
//  if ($debug == 1)
//    echo $sql_query_s;
  $tv2_sql_db->sql_write ($sql_query_s, 1, $debug);

  $d = $tv2_sql_db->sql_read (1, 0 /* $debug */);

  $d = tv2_sql_normalize ($tv2_sql_db, $d, $c, $f);

  // DEBUG
//  echo '<tt><pre>';
//  print_r ($d);

  return $d;
}




}


?>