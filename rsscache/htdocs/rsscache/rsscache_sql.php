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
/*
  // move item to different category
  global $tv2_sql_db;
  $debug = 0;

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = \''.$tv2_sql_db->sql_stresc ($new_category).'\''
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32);

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
*/
}


function
tv2_sql_vote ($rsstool_url_crc32, $new_score)
{
/*
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
*/
}


function
tv2_sql_restore ($rsstool_url_crc32)
{
/*
  // restore original category
  global $tv2_sql_db;
  $debug = 0;

  $sql_query_s = 'UPDATE rsstool_table SET tv2_moved = tv2_category'
                .' WHERE rsstool_url_crc32 = '.$tv2_sql_db->sql_stresc ($rsstool_url_crc32);

  $tv2_sql_db->sql_write ($sql_query_s, 0, $debug);
*/
}


function
tv2_sql_stats_func ($category = NULL, $t = 0)
{
  $a = array ();
  if ($category)
    $a[] = 'tv2_moved = \''.$category.'\'';
  if ($t > 0)
    $a[] = 'rsstool_dl_date > '.$t;
  $sql_query_s = 'SELECT SQL_CACHE tv2_moved, MIN(rsstool_dl_date), COUNT(*) AS tv2_rows'
        .' FROM rsstool_table'
        .(count ($a) ? ' WHERE ( '.implode (' AND ', $a).' )' : '')
        .' GROUP BY tv2_moved'
//        .' ORDER BY tv2_rows DESC'
;
  // DEBUG
//echo $sql_query_s.'<br>';
  return $sql_query_s;
}


function
tv2_sql_stats ($db, $category = NULL)
{
  global $tv2_time;

  $debug = 0;
//$category = 'amiga';
  $stats = array ();

  // total items and days since creation
  $sql_query_s = tv2_sql_stats_func ($category, 0);
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    {
      $a = array ('category' => $r[$i][0],
                  'items' => (int) $r[$i][2],
                  'days' => (int) (($tv2_time - $r[$i][1]) / 86400));
      $stats[] = $a;
    }

  // downloaded items today
  $sql_query_s = tv2_sql_stats_func ($category, mktime (0, 0, 0));
//  $sql_query_s = tv2_sql_stats_func ($category, mktime (0, 0, 0, date ('n'), date ('j')));
//  $sql_query_s = tv2_sql_stats_func ($category, $tv2_time);
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    for ($j = 0; isset ($stats[$j]); $j++)
      if ($r[$i]['tv2_moved'] == $stats[$j]['category'])
        {
          $stats[$j]['items_today'] = (int) $r[$i][2];
          break;
        }

  // downloaded items last 7 days   
  $sql_query_s = tv2_sql_stats_func ($category, mktime (0, 0, 0, date ('n'), date ('j') - 7));
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    for ($j = 0; isset ($stats[$j]); $j++)
      if ($r[$i]['tv2_moved'] == $stats[$j]['category'])
        {
          $stats[$j]['items_7_days'] = (int) $r[$i][2];
          break;
        }

  // downloaded items last 30 days
  $sql_query_s = tv2_sql_stats_func ($category, mktime (0, 0, 0, date ('n'), date ('j') - 30));
  $db->sql_write ($sql_query_s, 0, $debug);
  $r = $db->sql_read (0, $debug);
  for ($i = 0; isset ($r[$i]); $i++)
    for ($j = 0; isset ($stats[$j]); $j++)
      if ($r[$i]['tv2_moved'] == $stats[$j]['category'])
        {
          $stats[$j]['items_30_days'] = (int) $r[$i][2];
          break;
        }

  // DEBUG
//echo '<pre><tt>';
//print_r ($stats);
//exit;
  return $stats;
}


function
tv2_sql_normalize ($d)
{
  global $tv2_root,
         $tv2_link,
         $tv2_related_search;
  $debug = 0;

  for ($i = 0; isset ($d[$i]); $i++)
    {
      // demux
      $d[$i]['tv2_demux'] = widget_media_demux ($d[$i]['rsstool_url']);
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
tv2_sql ($c, $q, $f, $v, $start, $num, $table_suffix = NULL)
{
  /*
    $c == category
    $q == query
    $f == function
    $v == video (rsstool_url_crc32)
    LIMIT $start, $num
    $table_suffix is optional and set in config.xml
  */
  global $tv2_sql_db,
         $tv2_isnew,
         $tv2_root,
         $tv2_enable_search,
         $tv2_related_search,
         $tv2_use_dl_date,
         $tv2_wall_results,
         $tv2_cloud_results;
  global $tv2_debug_sql;
  $debug = $tv2_debug_sql;
//  $debug = 1;

  $v_segments = get_request_value ('v_segments');
//  $q = get_request_value ('q'); // we ignore the arg and make sure we get an unescaped one
//  $c = $tv2_sql_db->sql_stresc ($c);
//  $v = $tv2_sql_db->sql_stresc ($v);
//  $start = $tv2_sql_db->sql_stresc ($start);
//  $num = $tv2_sql_db->sql_stresc ($num);

//SELECT rsstool_table.*

  $rsstool_table = 'rsstool_table';
  $keyword_table = 'keyword_table';
  if ($table_suffix)
    if (trim ($table_suffix) != '')
      {
        $rsstool_table = 'rsstool_table_'.$table_suffix;
  // TODO
//      $keyword_table = 'keyword_table_'.$table_suffix;
      }

  if ($f == 'stats')
    return tv2_sql_stats ($tv2_sql_db, $c);

  if ($f == 'extern')
    return tv2_sql_extern ($c, $q, $v, $start, $num);

  // local SQL
  $sql_query_s = '';
  $sql_query_s .= ''
//                  .'EXPLAIN ';
                  .'SELECT'
//                  .' SQL_CACHE'
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
;

//FROM (
//  SELECT SQL_CACHE DISTINCT rsstool_url_crc32
//  FROM keyword_table
//  WHERE rsstool_keyword_crc16
//  IN ( 65124, 37526 )
//  LIMIT 1024
//)
//AS temp
//JOIN rsstool_table ON rsstool_table.rsstool_url_crc32 = temp.rsstool_url_crc32
//WHERE ( rsstool_media_duration BETWEEN 0 AND 301 )
//ORDER BY rsstool_date DESC
//LIMIT 0,10

  $a = array ();
  if ($v) // direct
    {
      $sql_query_s .= ' FROM '.$rsstool_table;
      $sql_query_s .= ' WHERE rsstool_url_crc32 = '.$v
                     .' LIMIT 1';
    }
  else if ($tv2_related_search && $f == 'related')
    {
      $sql_query_s .= ' FROM '.$rsstool_table;

      // category
      if ($c)
        $a[] = 'tv2_moved = \''.$c.'\'';

      $a[] = 'rsstool_related_id = '.misc_related_string_id ($q); // super fast

      if (isset ($a[0]))
        $sql_query_s .= ' WHERE ( '.implode (' AND ', $a).' )';

      // we sort related by title for playlist
      $sql_query_s .= ' ORDER BY rsstool_title ASC';

      // limit
      $sql_query_s .= ' LIMIT '.$tv2_wall_results;
    }
  else if ($tv2_enable_search && $q) // search
    {
      $sql_query_s .= ' FROM '.$rsstool_table;

      // category
      if ($c)
        $a[] = 'tv2_moved = \''.$c.'\'';

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
//            echo 'any: '.$v_any.'<br>require: '.$v_require.'<br>exclude: '.$v_exclude.'<br>';
          // keyword_search
          $s = tv2_sql_keyword_func ($v_any, $v_require, $v_exclude);
          if ($s != NULL)
            $a[] = $s;

      if (isset ($a[0]))
        $sql_query_s .= ' WHERE ( '.implode (' AND ', $a).' )';
    }
  else // default
    {
      $sql_query_s .= ' FROM '.$rsstool_table;   

      // category
      if ($c)
        $a[] = 'tv2_moved = \''.$c.'\'';

      // functions
      if ($f == 'new')
        $a[] = 'rsstool_dl_date > '.($tv2_time - $tv2_isnew).'';
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
//      if ($f == 'score')
//        $sql_query_s .= ' ORDER BY tv2_score ASC';
//      else
if ($f == 'new' || $tv2_use_dl_date)
        $sql_query_s .= ' ORDER BY rsstool_dl_date DESC';
      else
        $sql_query_s .= ' ORDER BY rsstool_date DESC';

      // limit
      $sql_query_s .= ' LIMIT '.$start.','.$num;
    }

  // DEBUG
//  if ($debug == 1)
//    echo $sql_query_s.'<br>';
  $tv2_sql_db->sql_write ($sql_query_s, 1, $debug);

  $debug = 0;
  $d = $tv2_sql_db->sql_read (1, $debug);

  $d = tv2_sql_normalize ($d);

  // DEBUG
//  echo '<tt><pre>';
//  print_r ($d);

  return $d;
}


function
tv2_sql_extern ($c, $q, $v, $start, $num)
{
  // wrapper for searching other websites
  //   interchangeable with tv2_sql()
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
        $rss = youtube_get_rss (NULL, trim ($v_user), NULL, $tv2_tor_enabled);
  // DEBUG
//  echo '<pre><tt>';
//print_r ($rss);
        for ($i = 0; isset ($rss->channel->item[$start + $i]) && $i < $num; $i++)
          if (isset ($rss->channel->item[$start + $i]->link))
            $links .= ' '.$rss->channel->item[$start + $i]->link;
//echo $start;
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
  $links = str_replace ("\n", ' ', $links);
  $a = explode (' ', $links);
  $d = array ();
  for ($i = 0; isset ($a[$i]); $i++)
    {
      $p = trim ($a[$i]);
      if ($p == '')
        continue;

          $p = youtube_get_videoid ($p);
            $d[] = array ('rsstool_url' => 'http://www.youtube.com/watch?v='.$p,
                          'rsstool_url_crc32' => sprintf ("%u", crc32 ('http://www.youtube.com/watch?v='.$p)),
                          'rsstool_title' => 'title',
                          'rsstool_desc' => 'desc',
                          'rsstool_dl_date' => $tv2_time,
                          'rsstool_date' => $tv2_time,
                          'tv2_moved' => '',
                          'rsstool_media_duration' => 0,
                          'rsstool_keywords' => '',
);
    }

  if ($v_stripdir)
    {
      $a = tv2_stripdir ($v_stripdir, $start, $num);
      for ($i = 0; isset ($a[$start + $i]) && $i < $num; $i++)
        $d[] = array ('rsstool_url' => $a[$start + $i],
                      'rsstool_url_crc32' => sprintf ("%u", crc32 ($a[$start + $i])),
                      'rsstool_title' => 'title',
                      'rsstool_desc' => 'desc',
                      'rsstool_dl_date' => $tv2_time,
                      'rsstool_date' => $tv2_time,
                      'tv2_moved' => '',
                      'rsstool_media_duration' => 0,
                      'rsstool_keywords' => '',
);
    }

  $d = tv2_sql_normalize ($d);

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($d);

  return $d;
}


}


?>