<?php
require_once ('config.php');
require_once ('misc/misc.php');
//require_once ('misc/widget.php');
require_once ('misc/sql.php');
//require_once ('qmaps.php');


function
get_num_demos ($game_name)
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

  $sql_statement = 'SELECT COUNT(*) FROM qdemos_demo_table WHERE qdemos_map NOT LIKE \'\''
                  .($game_name ? ' AND qdemos_game_name LIKE \''.$game_name.'\'' : '')
                  .';';

  $db->sql_write ($sql_statement, $debug);
  $r = $db->sql_read ($debug);

  $db->sql_close ();

  return $r[0][0];
}


function
get_num_videos ($game_name)
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

  $sql_statement = 'SELECT COUNT(*) FROM qdemos_demo_table WHERE 1'
                  .($game_name ? ' AND qdemos_game_name LIKE \''.$game_name.'\'' : '')
                  .';';

  $db->sql_write ($sql_statement, $debug);
  $r = $db->sql_read ($debug);

  $db->sql_close ();

  return $r[0][0];
}


function
config_xml_normalize ($config)
{
  for ($i = 0; $config->category[$i]; $i++)
    for ($j = 0; $config->category[$i]->query[$j]; $j++)
//      if ($config->category[$i]->query[$j]->name)
        if ($config->category[$i]->query[$j]->name == 'c')
          {
            $config->category[$i]->demos = get_num_demos ($config->category[$i]->name);
            $config->category[$i]->videos = get_num_videos ($config->category[$i]->name);
          }

  return $config;
}


function
config_xml ()
{
  static $config = NULL;

  if (!$config)
    {
      $config = simplexml_load_file ('config.xml');
      $config = config_xml_normalize ($config);
    }

  return $config;
}


function
config_xml_by_game_name ($game_name)
{
  $config = config_xml ();

  for ($i = 0; $config->category[$i]; $i++)
    if (!strcmp ($config->category[$i]->name, $game_name))
      return $config->category[$i];

  return NULL;
}


// HACK
function
tv2_normalize ($game_name)
{
  $p = strtolower ($game_name);

  if ($p == 'baseq3')
    $game_name = 'quake3';
  else if ($p == 'baseqz')
    $game_name = 'quakelive';

  return $game_name;
}


function
tv2_normalize2 ($dest)
{
  global $tv2_root,
         $tv2_download;

  // get filenames
  for ($i = 0; $dest[$i]; $i++)
    {
      $s = $tv2_download.'/'.$dest[$i]->demo_name;
      if (file_exists ($tv2_root.'/'.$s))
        $dest[$i]->demo_file = $s;
      else
        $dest[$i]->demo_file = NULL;

      $s = $tv2_download.'/'.set_suffix ($dest[$i]->demo_name, '.flv');
      if (file_exists ($tv2_root.'/'.$s))
        $dest[$i]->demo_flv = $s;
      else
        $dest[$i]->demo_flv = NULL;

      $s = $tv2_download.'/'.set_suffix ($dest[$i]->demo_name, '.jpg');
      if (file_exists ($tv2_root.'/'.$s))
        $dest[$i]->demo_jpg = $s;
      else
        $dest[$i]->demo_jpg = NULL;
    }
}


class st_player
{
  var $client;
  var $name_raw;
  var $name;
  var $model;
  var $score;
  var $ping;
}


class st_players
{
  var $player;
}


class st_tv2
{
  var $demo_file;  // set if the demo is downloadable
  var $demo_flv;   // name of the flv (if present)
  var $demo_jpg;   // name of movie thumbnail (if present)

  // extern video resource (youtube, etc.)
  var $other;
  var $other_title;
  var $other_desc;

  // this stuff comes from qdemos (SQL or XML)
  var $demo_name;
  var $demo_name_crc32;
  var $demo_date;
  var $demo_size;
  var $demo_len;
  var $demo_crc32;
  var $demo_parse_date;

  var $sv_name;
  var $sv_version;

  var $game_name;
  var $game_version;
  var $game_type;

  var $match_date_min;
  var $match_date_max;

  var $map;

  var $fraglimit;
  var $capturelimit;
  var $timelimit;

  var $scores;

  var $team_scores_red;
  var $team_scores_blue;

  var $max_players;

  var $players;
}


function
tv2_sql ($other, $c, $q, $f, $v, $start, $num)
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

  $other = $db->sql_stresc ($other);
  $c = $db->sql_stresc ($c);
  $q = $db->sql_stresc ($q);
  $v = $db->sql_stresc ($v);
  $start = $db->sql_stresc ($start);
  $num = $db->sql_stresc ($num);

  $sql_statement = 'SELECT * FROM `qdemos_demo_table` WHERE 1';

  if ($v) // direct
    $sql_statement .= ' AND ( `qdemos_demo_table`.`qdemos_demo_name_crc32` = '.$v.' )';
  else
    {
      // functions
      if ($f == 'new')
        $sql_statement .= ' AND ( `qdemos_demo_parse_date` > '.(time () - $tv2_isnew).' )';
      else if ($f == '0_5min')
        $sql_statement .= ' AND ( `qdemos_demo_len` > 0 && `qdemos_demo_len` < 301 )';
      else if ($f == '5_10min')
        $sql_statement .= ' AND ( `qdemos_demo_len` > 300 && `qdemos_demo_len` < 601 )';
      else if ($f == '10_min')
        $sql_statement .= ' AND ( `qdemos_demo_len` > 600 )';
      else if ($f == '1v1')
//        $sql_statement .= ' AND MATCH ( qdemos_demo_name, qdemos_sv_name, qdemos_sv_version, qdemos_game_name, qdemos_game_version, qdemos_map) AGAINST (\' vs vs. deathmatch \' IN BOOLEAN MODE)';
        $sql_statement .= ' AND MATCH ( `qdemos_demo_name` ) AGAINST (\'+vs\' IN BOOLEAN MODE)';
      else if ($f == 'ffa')
        $sql_statement .= ' AND ( `qdemos_demo_len` > 600 )';
      else if ($f == 'tdm')
        $sql_statement .= ' AND ( `qdemos_demo_len` > 600 )';
      else if ($f == 'ctf')
        $sql_statement .= ' AND ( `qdemos_demo_len` > 600 )';

      // game name
      if ($c)
        $sql_statement .= ' AND ( `qdemos_demo_table`.`qdemos_game_name` LIKE \''.$c.'\' )';

      // query
      if ($q)
        {
/*
          $sql_statement .= ' WHERE MATCH (`qdemos_demo_name`'
                           .' ,`qdemos_sv_name`'
                           .' ,`qdemos_sv_version`'
                           .' ,`qdemos_game_name`'
                           .' ,`qdemos_game_version`'
                           .' ,`qdemos_map`'
                           .') AGAINST (\''
                           .$db->sql_prep_query ($rsstool->q, 0)
                           .'\''
                           .' IN BOOLEAN MODE'
                           .')';
*/
          $q_array = explode (' ', $q);
          $q_size = sizeof ($q_array);
          for ($i = 0; $i < $q_size; $i++)
            {
              $sql_statement .= ' AND ( `qdemos_demo_name` LIKE \'%'.$q_array[$i].'%\''
                               .' OR `qdemos_sv_name` LIKE \'%'.$q_array[$i].'%\''
                               .' OR `qdemos_sv_version` LIKE \'%'.$q_array[$i].'%\''
                               .' OR `qdemos_game_name` LIKE \'%'.$q_array[$i].'%\''
                               .' OR `qdemos_game_version` LIKE \'%'.$q_array[$i].'%\''
                               .' OR `qdemos_map` LIKE \'%'.$q_array[$i].'%\''
;
              // players
              $sql_statement .= ' OR `qdemos_demo_table`.`qdemos_demo_name_crc32`'
                               .' IN ('
                               .' SELECT `qdemos_demo_name_crc32`'
                               .' FROM `qdemos_player_table`'
                               .' WHERE `qdemos_player_name` LIKE \'%'.$q_array[$i].'%\''
                               .' )'
;
              if ($other)
                $sql_statement .= ' OR `qdemos_demo_table`.`qdemos_demo_name_crc32`'
                                 .' IN ('
                                 .' SELECT `qdemos_demo_name_crc32`'
                                 .' FROM `qdemos_other`'
                                 .' WHERE `qdemos_demo_title` LIKE \'%'.$q_array[$i].'%\''
                                 .' OR `qdemos_demo_desc` LIKE \'%'.$q_array[$i].'%\''
                                 .' )'
;
              $sql_statement .= ' )';
            }
        }

      // NO other videos
      if ($other)
        {
        }
      else
        $sql_statement .= ' AND ( `qdemos_demo_table`.`qdemos_demo_size` != 0 )';

      $sql_statement .= ' ORDER BY `qdemos_demo_parse_date` DESC';
//      $sql_statement .= ' ORDER BY `qdemos_demo_name` ASC';
//      $sql_statement .= ' ORDER BY `qdemos_demo_date` DESC';
      $sql_statement .= ' LIMIT '.$start.','.$num;
    }

  $db->sql_write ($sql_statement, $debug);
//  $d = array ();
  $d = $db->sql_read ($debug);

  // NORMALIZE: clone an qdemos XML array from the SQL result
  $dest = array ();
  for ($i = 0; $d[$i]; $i++)
    {
      // unset integer indices
      for ($j = 0; $j < sizeof ($d[$i]); $j++)
        unset ($d[$i][$j]);

      $dest[$i] = new st_tv2;
      $keys = array_keys ((array) $dest[$i]);

      for ($j = 0; $j < sizeof ($keys); $j++)
        eval ("\$dest[\$i]->".$keys[$j]." = \$d[\$i][\"qdemos_".$keys[$j]."\"];");

      $sql_statement = 'SELECT * FROM `qdemos_player_table`'
                      .' WHERE ( `qdemos_demo_name_crc32` = '.$dest[$i]->demo_name_crc32.' )'
                      .' ORDER BY `qdemos_player_score` DESC';

      $db->sql_write ($sql_statement, 0);

//      $p = array ();
      $p = $db->sql_read (0);

      $dest[$i]->players = new st_players;
      $dest[$i]->players->player = array ();

      for ($j = 0; $j < $db->sql_get_rows (); $j++)
        {
          $dest[$i]->players->player[$j] = new st_player;
          $keys = array_keys ((array) $dest[$i]->players->player[$j]);

          for ($k = 0; $k < sizeof ($keys); $k++)
            eval ("\$dest[\$i]->players->player[\$j]->".$keys[$k]." = \$p[\$j][\"qdemos_player_".$keys[$k]."\"];");
        }

      // add title and desc from other videos from RSS
      $dest[$i]->other = 0;
//      if ($other)
        {
          // get title and desc from qdemos_other
          $sql_statement = 'SELECT * FROM `qdemos_other` WHERE `qdemos_other`.`qdemos_demo_name_crc32` = '
                          .$dest[$i]->demo_name_crc32;

          $db->sql_write ($sql_statement, $debug);

//          $p = array ();
          $p = $db->sql_read ($debug);
          if ($p)
            {
              $dest[$i]->other = 1;
              $dest[$i]->other_title = $p[0]['qdemos_demo_title'];
              $s = $p[0]['qdemos_demo_desc'];
              if (strstr ($dest[$i]->demo_name, 'www.google.com'))
                $s = substr ($s, 0, strrpos ($s, '<div '));
              $dest[$i]->other_desc = strip_tags ($s, '<img><br>');

              // remove eventual google redirect
              if (strstr ($dest[$i]->demo_name, 'www.google.com'))
                {
                  $offset = strpos ($dest[$i]->demo_name, '?q=') + 3;
                  $len = strpos ($dest[$i]->demo_name, '&source=') - $offset;
                  $dest[$i]->demo_name = substr ($dest[$i]->demo_name, $offset, $len);
                }
            }
        }
   }

  $db->sql_close ();

  tv2_normalize2 ($dest);

  // DEBUG
//  print_r ($dest);

  return $dest;
}


?>