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

  $db->sql_write ($sql_query_s, $debug);

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

  $db->sql_write ($sql_query_s, $debug);

  $db->sql_close ();
}


function
tv2_sql_stats ($category = NULL)
{
  global $tv2_dbhost,
         $tv2_dbuser,
         $tv2_dbpass,
         $tv2_dbname;
  global $memcache_expire;
  $debug = 0;

  $stats = array ('videos' => 0, 'days' => 0);

  $db = new misc_sql;  
  $db->sql_open ($tv2_dbhost,
                 $tv2_dbuser,
                 $tv2_dbpass,
                 $tv2_dbname);


  // videos
  // all at once
//  $sql_query_s = 'SELECT COUNT(*) AS rows, tv2_moved FROM rsstool_table WHERE 1';
//  $sql_query_s .= ' GROUP BY tv2_category ';
//  $sql_query_s .= ';';

  $sql_query_s = 'SELECT COUNT(*) FROM rsstool_table WHERE 1';

  if ($category)
    $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';

  $sql_query_s .= ';';

  $db->sql_write ($sql_query_s, $debug);
  $r = $db->sql_read ($debug);

  $stats['videos'] = (int) $r[0][0];

  // days
  $sql_query_s = 'SELECT rsstool_dl_date FROM rsstool_table WHERE 1';

  if ($category)
    $sql_query_s .= ' AND tv2_moved = \''.$category.'\'';

  $sql_query_s .= ' ORDER BY rsstool_dl_date ASC'
                   .' LIMIT 1'
                   .';';

/*
  $sql_query_s = 'SELECT rsstool_dl_date';

  if ($category)
    $sql_query_s .= ' FROM ( SELECT rsstool_dl_date FROM rsstool_table WHERE ( tv2_moved LIKE \''.$category.'\' ) )';
  else
    $sql_query_s .= ' FROM ( SELECT rsstool_dl_date FROM rsstool_table WHERE 1 )';

  $sql_query_s .= ''
                   .' WHERE 1'
                   .' ORDER BY rsstool_dl_date ASC'
                   .' LIMIT 1'
                   .';';
*/

  $db->sql_write ($sql_query_s, $debug);
  $r = $db->sql_read ($debug);

  $stats['days'] = (int) ((time () - (int) $r[0][0]) / 86400);
  $db->sql_close ();


  return $stats;
}


function
tv2_sql_normalize ($db, $dest, $c)
{
  global $tv2_root,
         $tv2_link;
  $debug = 0;

  for ($i = 0; isset ($dest[$i]); $i++)
    {
      if (strstr ($dest[$i]['rsstool_url'], 'www.google.com'))
        {
          // remove eventual google redirect
          $offset = strpos ($dest[$i]['rsstool_url'], '?q=') + 3;
          $len = strpos ($dest[$i]['rsstool_url'], '&source=') - $offset;
          $dest[$i]['rsstool_url'] = substr ($dest[$i]['rsstool_url'], $offset, $len);

          // desc
          $offset = 0;
          $len = strrpos ($dest[$i]['rsstool_desc'], '<div ');
          if ($len)
            $dest[$i]['rsstool_desc'] = substr ($dest[$i]['rsstool_desc'], $offset, $len);
        }
      else if (strstr ($dest[$i]['rsstool_url'], 'www.youtube.com'))
        {
          $dest[$i]['rsstool_url'] = str_replace ('&feature=youtube_gdata', '', $dest[$i]['rsstool_url']);
        }

      // HACK: for development
//      $dest[$i]['tv2_related'] = misc_get_keywords ($dest[$i]['rsstool_title'], 1); // isalpha
//      $dest[$i]['tv2_keywords'] = misc_get_keywords ($dest[$i]['rsstool_title']
//                                                    .' '
//                                                    .strip_tags ($dest[$i]['rsstool_desc']), 0); // isalnum

      // local url
      if (strstr ($dest[$i]['rsstool_url'], $tv2_link))
        $dest[$i]['tv2_local_url'] = str_replace ($tv2_link, '', $dest[$i]['rsstool_url']);

      // demux
      $dest[$i]['tv2_demux'] = widget_media_demux ($dest[$i]['rsstool_url']);

      // is local media?
      if ($dest[$i]['tv2_demux'] == 4)
        if (strncmp ($dest[$i]['rsstool_url'], $tv2_link, strlen ($tv2_link))) // is local
        {
          // does file exist?
          $flv = str_replace ($tv2_link, $tv2_root.'/', $dest[$i]['rsstool_url']);
//          $flv = set_suffix ($flv, '.flv');
          if (!file_exists ($flv))
            $dest[$i]['tv2_demux'] = 0;
        }

      // strip tags from the desc
//      $dest[$i]['rsstool_desc'] = strip_tags ($dest[$i]['rsstool_desc'], '<img><br><br/><br />');
      $dest[$i]['rsstool_desc'] = strip_tags ($dest[$i]['rsstool_desc']);
    }

  return $dest;
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
    $s .= tv2_sql_query2boolean ($q);

  $s = trim ($s);

  if (!strlen ($s))
    return '';

  $p = '';

  $p .= ' AND MATCH ('
       .' tv2_related, tv2_keywords'
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
         $tv2_root;
  global $tv2_debug_sql;
  $debug = $tv2_debug_sql;

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

//  $sql_query_s = 'SELECT * FROM rsstool_table WHERE 1';
  $sql_query_s = 'SELECT'
                  .' rsstool_url,'
                  .' rsstool_url_crc32,'
                  .' rsstool_title,'
                  .' rsstool_desc,'
                  .' rsstool_dl_date,'
                  .' tv2_category,'
                  .' tv2_moved,'
                  .' tv2_duration,'
                  .' tv2_related,'
                  .' tv2_keywords'
                  .' FROM rsstool_table WHERE 1';

  if ($v) // direct
    $sql_query_s .= ' AND ( rsstool_url_crc32 = '.$v.' )';
  else
    {
      // category
      if ($c)
        $sql_query_s .= ' AND ( `tv2_moved` = \''.$c.'\' )';

      $filter = NULL;
      if ($c)
        {
          $category = config_xml_by_category ($c);

          if ($category)
            if ($category->filter)
              if (strlen ($category->filter))
                $filter = $category->filter;
        }

     if ($q && $f == 'related')
         {
           $s = str_replace (' ', '%', trim ($db->sql_stresc ($q)));
           $sql_query_s .= ' AND ( tv2_related LIKE \'%'.$s.'%\' )';
         }
      else
        $sql_query_s .= tv2_sql_match_func ($db, $q, $filter);

      // functions
      if ($f == 'new')
        $sql_query_s .= ' AND ( rsstool_dl_date > '.(time () - $tv2_isnew).' )';
      else if ($f == '0_5min')
        $sql_query_s .= ' AND ( tv2_duration > 0 && tv2_duration < 301 )';
      else if ($f == '5_10min')
        $sql_query_s .= ' AND ( tv2_duration > 300 && tv2_duration < 601 )';
      else if ($f == '10_min')
        $sql_query_s .= ' AND ( tv2_duration > 600 )';
      else if ($f == 'prev')
        $sql_query_s .= ' AND ( 1 )';
      else if ($f == 'next')
        $sql_query_s .= ' AND ( 1 )';

      // sort
      if ($f == 'related') // we sort related by title for playlist
        $sql_query_s .= ' ORDER BY rsstool_title ASC';
      else
        $sql_query_s .= ' ORDER BY rsstool_dl_date DESC';

      // limit
      $sql_query_s .= ' LIMIT '.$start.','.$num;
    }

  $db->sql_write ($sql_query_s, $debug);
//  $d = array ();
  $d = $db->sql_read (0 /* $debug */);

  $d = tv2_sql_normalize ($db, $d, $c);

  // DEBUG
//  echo '<tt><pre>';
//  print_r ($d);

  $db->sql_close ();


  return $d;
}


}


?>