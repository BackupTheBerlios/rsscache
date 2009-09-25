<?php
require_once ('config.php');
require_once ('misc/misc.php');
//require_once ('misc/widget.php');
require_once ('misc/sql.php');
//require_once ('qmaps.php');


function
config_xml_normalize ($config)
{
  for ($i = 0; $config->category[$i]; $i++)
    for ($j = 0; $config->category[$i]->query[$j]; $j++)
//      if ($config->category[$i]->query[$j]->name)
        if ($config->category[$i]->query[$j]->name == 'c')
          {
            // add a new variable
            $config->category[$i]->videos = tv2_get_num_videos ($config->category[$i]->name);
          }

  return $config;
}


function
config_xml ($use_memcache = 0)
{
  static $config = NULL;

  if ($config)
    return $config;

  if ($use_memcache == 1) 
    {
      $memcache = new Memcache;  
      $memcache->connect ('localhost', 11211) or die ("memcache: could not connect");

      // data from the cache
      $config = unserialize ($memcache->get (md5 ('config.xml')));
   
      if ($config)
        {
        // DEBUG
//        echo 'cached';
          return $config;
        } 
    }

  $config = simplexml_load_file ('config.xml');
  $config = config_xml_normalize ($config);

  // use memcache
  if ($use_memcache == 1)
    {
      $memcache->set (md5 ('config.xml'), serialize ($config))
        or die ("memcache: failed to save data at the server");
    }   

  return $config;
}


function
config_xml_by_category ($category)
{
  $config = config_xml ();

  for ($i = 0; $config->category[$i]; $i++)
    if (!strcmp ($config->category[$i]->name, $category))
      return $config->category[$i];

  return NULL;
}


function
tv2_get_num_videos_func ($db, $category)
{
  $debug = 0;    

  $sql_statement = 'SELECT COUNT(*) FROM rsstool_'.$category.'_table WHERE 1'
                  .';';

  $db->sql_write ($sql_statement, $debug);
  $r = $db->sql_read ($debug);

  return $r[0][0];
}


function
tv2_get_num_videos ($category)
{
  global $tv2_dbhost,
         $tv2_dbuser,
         $tv2_dbpass,
         $tv2_dbname;
  $num = 0;

  $db = new misc_sql;  
  $db->sql_open ($tv2_dbhost,
                 $tv2_dbuser,
                 $tv2_dbpass,
                 $tv2_dbname);

  if ($category)
    {
      $num = tv2_get_num_videos_func ($db, $category);
    }
  else
    {
      $config = config_xml ();
      for ($i = 0; $config->category[$i]; $i++)
        if ($config->category[$i]->name)
          if ($config->category[$i]->name[0])
            $num += tv2_get_num_videos_func ($db, $config->category[$i]->name);
    }

  $db->sql_close ();

  return $num;
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
tv2_normalize2 (&$dest, $c)
{
  for ($i = 0; $dest[$i]; $i++)
    {
      $dest[$i]['category'] = $c;
      $dest[$i]['duration'] = 0;

      if (strstr ($dest[$i]['rsstool_url'], 'www.google.com'))
        $dest[$i]['rsstool_desc'] = substr ($dest[$i]['rsstool_desc'], 0, strrpos ($dest[$i]['rsstool_desc'], '<div '));
      $dest[$i]['rsstool_desc'] = strip_tags ($dest[$i]['rsstool_desc'], '<img>');

      // remove eventual google redirect
      if (strstr ($dest[$i]['rsstool_url'], 'www.google.com'))
        {
          $offset = strpos ($dest[$i]['rsstool_url'], '?q=') + 3;
          $len = strpos ($dest[$i]['rsstool_url'], '&source=') - $offset;
          $dest[$i]['rsstool_url'] = substr ($dest[$i]['rsstool_url'], $offset, $len);
        }
    }
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
         $tv2_download;
  $debug = 0;

  $db = new misc_sql;
  $db->sql_open ($tv2_dbhost,
                 $tv2_dbuser,
                 $tv2_dbpass,
                 $tv2_dbname);

  if ($c)
    if ($c == '')
      $c = NULL;

//  if (!($c))
    $c = $db->sql_stresc ($c);
  $q = $db->sql_stresc ($q);
  $v = $db->sql_stresc ($v);
  $start = $db->sql_stresc ($start);
  $num = $db->sql_stresc ($num);

//  if ($c)
    $rsstool_table = 'rsstool_'.$c.'_table';
/*
  else
    {
      $config = config_xml ();
      $count = 0;
      $rsstool_table = '';
      for ($i = 0; $config->category[$i]; $i++)
        if ($config->category[$i]->name)
          if ($config->category[$i]->name[0])
          {
            if ($count)
              $rsstool_table .= ', ';
            $rsstool_table .= 'rsstool_'.$config->category[$i]->name.'_table';
            $count++;
          }
    }
*/
  $sql_statement = 'SELECT * FROM ( '.$rsstool_table.' ) WHERE 1';

  if ($v) // direct
    $sql_statement .= ' AND ( rsstool_title_crc32 = '.$v.' )';
  else
    {
      // functions
      if ($f == 'new')
        $sql_statement .= ' AND ( rsstool_dl_date > '.(time () - $tv2_isnew).' )';
/*
      else if ($f == '0_5min')
        $sql_statement .= ' AND ( `tv2_len` > 0 && `tv2_len` < 301 )';
      else if ($f == '5_10min')
        $sql_statement .= ' AND ( `tv2_len` > 300 && `tv2_len` < 601 )';
      else if ($f == '10_min')
        $sql_statement .= ' AND ( `tv2_len` > 600 )';
*/
      else if ($f == '1v1')
//        $sql_statement .= ' AND MATCH ( `rsstool_title`, `rsstool_desc` ) AGAINST (\' vs vs. deathmatch \' IN BOOLEAN MODE)';
        $sql_statement .= ' AND MATCH ( `rsstool_title`, `rsstool_desc` ) AGAINST (\'+vs\' IN BOOLEAN MODE)';
      else if ($f == 'ffa')
        $sql_statement .= ' AND MATCH ( `rsstool_title`, `rsstool_desc` ) AGAINST (\'+vs\' IN BOOLEAN MODE)';
      else if ($f == 'tdm')
        $sql_statement .= ' AND MATCH ( `rsstool_title`, `rsstool_desc` ) AGAINST (\'+vs\' IN BOOLEAN MODE)';
      else if ($f == 'ctf')
        $sql_statement .= ' AND MATCH ( `rsstool_title`, `rsstool_desc` ) AGAINST (\'+vs\' IN BOOLEAN MODE)';

      // query
      if ($q)
        {
          $sql_statement .= ' AND MATCH ( rsstool_title, rsstool_desc'
                           .' ) AGAINST (\''
                           .$db->sql_stresc ($q)
                           .'\''
                           .' IN BOOLEAN MODE'
                           .')';
        }

      $sql_statement .= ' ORDER BY `rsstool_dl_date` DESC';
//      $sql_statement .= ' ORDER BY `rsstool_title` ASC';
      $sql_statement .= ' LIMIT '.$start.','.$num;
    }

  $db->sql_write ($sql_statement, $debug);
//  $d = array ();
  $d = $db->sql_read (0 /* $debug */);

  $db->sql_close ();

  tv2_normalize2 ($d, $c);

  // DEBUG
//  echo '<tt><pre>';
//  print_r ($d);

  return $d;
}


?>