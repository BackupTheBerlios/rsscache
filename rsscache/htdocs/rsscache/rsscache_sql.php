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
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32).';';

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
}


function
tv2_sql_vote ($rsstool_url_crc32, $new_score)
{
  global $tv2_sql_db;
  $debug = 0;

  $sql_query_s = 'SELECT tv2_votes,tv2_score FROM rsstool_table'
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32).';';
  $tv2_sql_db->sql_write ($p, 0, $debug);
  $r = $tv2_sql_db->sql_read (1, $debug);

  if ($new_score > 0)
    $new_score = ($r[0]['tv2_votes'] * $r[0]['tv2_score'] + $new_score) / ($r[0]['tv2_votes'] + 1);
  else
    $new_score = $r[0]['tv2_score'];

  $sql_query_s = 'UPDATE rsstool_table SET tv2_votes = '.($r[0]['tv2_votes'] + 1).',tv2_score = '.$new_score
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32).';';

  $tv2_sql_db->sql_write ($p, 1, $debug);
}


function
tv2_sql_restore ($rsstool_url_crc32)
{
  // restore original category
  global $tv2_sql_db;
  $debug = 0;

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = tv2_category'
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32).';';

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
}


function
tv2_sql_stats ($category = NULL)
{
  global $tv2_sql_db;
  $debug = 0;
  $f = get_request_value ('f');

  $stats = array ('items' => 0, 'items_today' => 0, 'items_7_days' => 0, 'items_30_days' => 0, 'days' => 0);

  // downloaded items since...
  // ...always
  $sql_query_s = 'SELECT COUNT(*) FROM rsstool_table WHERE 1';
  if ($category)
    $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';
  $sql_query_s .= ';';

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
  $r = $tv2_sql_db->sql_read (0, $debug);

  $stats['items'] = (int) $r[0][0];

  if ($f == 'stats')
    {
      // ...today
      $sql_query_s = 'SELECT COUNT(*) FROM rsstool_table WHERE rsstool_dl_date > '.mktime (0, 0, 0);
      if ($category)
        $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';
      $sql_query_s .= ';';

      $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
      $r = $tv2_sql_db->sql_read (0, $debug);

      $stats['items_today'] = (int) $r[0][0];


      // ...last 7 days
      $sql_query_s = 'SELECT COUNT(*) FROM rsstool_table WHERE rsstool_dl_date > '.mktime (0, 0, 0, date ('n'), date ('j') - 7);
      if ($category)
        $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';
      $sql_query_s .= ';';

      $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
      $r = $tv2_sql_db->sql_read (0, $debug);

      $stats['items_7_days'] = (int) $r[0][0]; 


      // ...last 30 days
      $sql_query_s = 'SELECT COUNT(*) FROM rsstool_table WHERE rsstool_dl_date > '.mktime (0, 0, 0, date ('n'), date ('j') - 30);
      if ($category)
        $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';
      $sql_query_s .= ';';

      $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
      $r = $tv2_sql_db->sql_read (0, $debug);

      $stats['items_30_days'] = (int) $r[0][0];
    }

  // total items downloaded...
  $sql_query_s = 'SELECT rsstool_dl_date FROM rsstool_table WHERE 1';
  if ($category)
    $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';
  $sql_query_s .= ' ORDER BY rsstool_dl_date ASC'
                   .' LIMIT 1'
                   .';';

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
  $r = $tv2_sql_db->sql_read (0, $debug);

  if (isset ($r[0]))
    $stats['days'] = (int) ((time () - (int) $r[0][0]) / 86400);

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

      // HACK: keywords
//      if ($d[$i]['rsstool_keywords'] = '')
//        $d[$i]['rsstool_keywords'] = misc_get_keywords ($d[$i]['rsstool_title'].' '.$d[$i]['rsstool_desc']);

      // strip any tags from the desc
      $p = $d[$i]['rsstool_desc'];
      $p = str_replace ('>', '> ', $p);
      $p = strip_tags2 ($p);
      $p = str_replace (array ('  ', '  ', '  ', '  ', '  '), ' ', $p);
      $d[$i]['rsstool_desc'] = $p;
      $d[$i]['rsstool_desc'] = str_replace ('youtube.com', '', $d[$i]['rsstool_desc']);

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
tv2_sql_match_func ($tv2_sql_db, $q, $filter)
{
  $s = '';

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
tv2_sql_leftjoin_func ($tv2_sql_db, $q, $filter)
{
  $debug = 0;

  if (!$q)
    return '';

  $a = explode (' ', strtolower ($q));
  $a = misc_array_unique_merge ($a);
  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);

  $p = '';
  $p .= ' AND rsstool_table.rsstool_url_crc32'
       .' IN (';

  $p .= 'SELECT rsstool_url_crc32'
       .' FROM keyword_index'
       .' LEFT JOIN keyword_table'
       .' ON keyword_index.keyword_id = keyword_table.keyword_id'
       .' WHERE keyword_table.keyword IN (';

  for ($i = 0; isset ($a[$i]); $i++)
    {
      if ($i > 0)
        $p .= ', ';
      $p .= '\''.trim ($a[$i]).'\'';
    }

  $p .= ')';
  $p .= ')';

  // DEBUG
//  echo $p;

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

  $q = get_request_value ('q'); // we ignore the arg and make sure we get an unescaped one

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
  $q = $tv2_sql_db->sql_stresc ($q);
  $c = $tv2_sql_db->sql_stresc ($c);
  $v = $tv2_sql_db->sql_stresc ($v);
  $start = $tv2_sql_db->sql_stresc ($start);
  $num = $tv2_sql_db->sql_stresc ($num);

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
                  .' rsstool_keywords'
//                  .' tv2_votes,'
//                  .' tv2_score'
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
        $sql_query_s .= ' AND ( tv2_moved = \''.$c.'\' )';

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
//        $sql_query_s .= tv2_sql_match_func ($tv2_sql_db, $q, $filter);
        $sql_query_s .= tv2_sql_leftjoin_func ($tv2_sql_db, $q, $filter);

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
//        $sql_query_s .= ' AND ( rsstool_url LIKE \'%youtube%\' )'; // TODO: thumbnails of all videos
        $sql_query_s .= '';

      // sort
      if ($tv2_related_search && $f == 'related') // we sort related by title for playlist
        $sql_query_s .= ' ORDER BY rsstool_title ASC';
      else if ($f == 'score')
        $sql_query_s .= ' ORDER BY tv2_score ASC';
      else if ($f == 'new')
        $sql_query_s .= ' ORDER BY rsstool_dl_date DESC';
      else if ($tv2_use_dl_date)
        $sql_query_s .= ' ORDER BY rsstool_dl_date DESC';
      else
        $sql_query_s .= ' ORDER BY rsstool_date DESC';

      // limit
      $sql_query_s .= ' LIMIT '.$start.','.$num;
    }

  // DEBUG
//  echo $sql_query_s;
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