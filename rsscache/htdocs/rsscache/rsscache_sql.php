<?php
if (!defined ('TV2_SQL_PHP'))
{
define ('TV2_SQL_PHP', 1);
require_once ('config.php');
require_once ('misc/misc.php');
require_once ('misc/sql.php');


function
tv2_get_num_videos ($category)
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

  $sql_statement = 'SELECT COUNT(*) FROM rsstool_table WHERE 1';

  if ($category)
    $sql_statement .= ' AND ( tv2_category LIKE \''.$category.'\' )';

  $sql_statement .= ';';

  $db->sql_write ($sql_statement, $debug);
  $r = $db->sql_read ($debug);

  $db->sql_close ();

  return $r[0][0];
}


function
tv2_get_num_days ($category)
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

  $sql_statement = 'SELECT rsstool_dl_date FROM rsstool_table WHERE 1';

  if ($category)
    $sql_statement .= ' AND ( tv2_category LIKE \''.$category.'\' )';

  $sql_statement .= ' ORDER BY rsstool_dl_date ASC'
                   .' LIMIT 1'
                   .';';

  $db->sql_write ($sql_statement, $debug);
  $r = $db->sql_read ($debug);

  $db->sql_close ();

  return (int) ((time () - $r[0][0]) / 86400);
}


function
tv2_sql_normalize ($db, $dest, $c)
{
  $debug = 0;

  for ($i = 0; $dest[$i]; $i++)
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
          $dest[$i]['rsstool_desc'] = substr ($dest[$i]['rsstool_desc'], $offset, $len);
        }
      else if (strstr ($dest[$i]['rsstool_url'], 'www.youtube.com'))
        {
          $dest[$i]['rsstool_url'] = str_replace ('&feature=youtube_gdata', '', $dest[$i]['rsstool_url']);
        }

      // strip tags from the desc
      $dest[$i]['rsstool_desc'] = strip_tags ($dest[$i]['rsstool_desc'], '<img><br>');
    }

  return $dest;
}


function
tv2_sql ($c, $q, $desc, $f, $v, $start, $num)
{
  global $tv2_dbhost,
         $tv2_dbuser,
         $tv2_dbpass, 
         $tv2_dbname,
         $tv2_isnew,
         $tv2_root;
  $debug = 0;

  $db = new misc_sql;
  $db->sql_open ($tv2_dbhost,
                 $tv2_dbuser,
                 $tv2_dbpass,
                 $tv2_dbname);

  $c = $db->sql_stresc ($c);
  $q = $db->sql_stresc ($q);
  $v = $db->sql_stresc ($v);
  $start = $db->sql_stresc ($start);
  $num = $db->sql_stresc ($num);

  $sql_statement = 'SELECT * FROM ( rsstool_table ) WHERE 1';

  if ($v) // direct
    $sql_statement .= ' AND ( rsstool_url_crc32 = '.$v.' )';
  else
    {
      // functions
      if ($f == 'new')
        $sql_statement .= ' AND ( rsstool_dl_date > '.(time () - $tv2_isnew).' )';
      else if ($f == '0_5min')
        $sql_statement .= ' AND ( tv2_duration > 0 && tv2_duration < 301 )';
      else if ($f == '5_10min')
        $sql_statement .= ' AND ( tv2_duration > 300 && tv2_duration < 601 )';
      else if ($f == '10_min')
        $sql_statement .= ' AND ( tv2_duration > 600 )';

      // category and whitelist > blacklist
      if ($c)
        {
          // category
          $sql_statement .= ' AND ( `tv2_category` LIKE \''.$c.'\' )';

          // whitelist AND(OR()) > blacklist AND()
          $category = config_xml_by_category ($c);
          $separator = ',';
          $whitelist = '';
          $blacklist = '';
          for ($i = 0; $category->feed[$i]; $i++)
            {
              if (strlen ((string) $category->feed[$i]->whitelist))
                $whitelist .= ($i > 0 ? $separator : '').((string) $category->feed[$i]->whitelist);
              if (strlen ((string) $category->feed[$i]->blacklist))
                $blacklist .= ($i > 0 ? $separator : '').((string) $category->feed[$i]->blacklist);
            }

          $w = explode ($separator, $whitelist);
          $w = array_merge (array_unique ($w)); // remove dupes
          $b = explode ($separator, $blacklist);
          $b = array_merge (array_unique ($b)); // remove dupes

          // category-wise whitelist AND(OR()...)
          if ($w[0])
            {
              $sql_statement .= ' AND (';
              for ($i = 0; $w[$i]; $i++)
                $sql_statement .= ($i > 0 ? ' OR' : '')
                                 .' ( rsstool_title LIKE \'%'.$w[$i].'%\' )'
                                 .' OR ( rsstool_desc LIKE \'%'.$w[$i].'%\' )'
;
              $sql_statement .= ' )';
            }

          // category-wise blacklist ...AND(AND())...
          if ($b[0])
            {
              $sql_statement .= ' AND (';
              for ($i = 0; $b[$i]; $i++)
                $sql_statement .=  ($i > 0 ? ' AND' : '')
                                 .' ( rsstool_title NOT LIKE \'%'.$b[$i].'%\' )'
                                 .' AND ( rsstool_desc NOT LIKE \'%'.$b[$i].'%\' )'
;
              $sql_statement .= ' )';
            }

        }

      // query
      if ($q)
        {
          $query_separator = ' ';
/*
          $sql_statement .= ' AND MATCH ( rsstool_desc'
                           .' ) AGAINST (\''
                           .$db->sql_stresc ($q)
                           .'\''
                           .' IN BOOLEAN MODE'
                           .' )';
*/

          if ($f == 'related')
            {
                  $s = str_replace (' ', '%', trim ($q));
                  $sql_statement .= ' AND ('
                                   .' rsstool_title LIKE \'%'.$s.'%\''
                                   .' )'
;
            }
          else
            {
              $sql_statement .= ' AND (';
              $q_array = explode ($query_separator, $q);
              for ($i = 0; $q_array[$i]; $i++)
                {
                  $s = str_replace (' ', '%', trim ($q_array[$i]));
                  $sql_statement .= ($i > 0 ? ' OR' : '')
                                   .' rsstool_title LIKE \'%'.$s.'%\''              
                                   .($desc ? ' or rsstool_desc LIKE \'%'.$s.'%\'' : '')
;
                }
              $sql_statement .= ' )';
            }
        }

      if ($f == 'related')
        $sql_statement .= ' ORDER BY rsstool_title ASC';
      else
        $sql_statement .= ' ORDER BY rsstool_dl_date DESC';
      $sql_statement .= ' LIMIT '.$start.','.$num;
    }

  $db->sql_write ($sql_statement, $debug);
//  $d = array ();
  $d = $db->sql_read (0 /* $debug */);

  $d = tv2_sql_normalize ($db, $d, $c);

  $db->sql_close ();

  // DEBUG
//  echo '<tt><pre>';
//  print_r ($d);

  return $d;
}


}


?>