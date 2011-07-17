<?php
/*
misc.php - miscellaneous functions

Copyright (c) 2006 - 2010 NoisyB


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
*/
if (!defined ('MISC_MISC_PHP'))
{
define ('MISC_MISC_PHP', 1);
//error_reporting(E_ALL | E_STRICT);
if (file_exists ('stemmer.php'))
  include ('stemmer.php');


function
misc_substr2 ($t, $left, $cont, $right = NULL)
{
  // $left='a' $cont='b' $right=NULL
  // --a->]--b->[...

  // $left='a' $cont=NULL $right='b'
  // --a->]   [<-b--

  // $left=NULL $cont='b' $right='a'
  // ...]<-b--[<-a--

  $l = $c = $r = 0;
  if ($left != NULL)
    $l = strpos ($t, $left) + strlen ($left);

  if ($right != NULL)
    $r = strrpos ($t, $right);

  if ($cont != NULL)
    {
      if ($l) // from left
        $c = strpos ($t, $cont, $l);
      else if ($r) // from right
        $c = strrpos ($t, $cont, $r);
    }

  // DEBUG
  echo $l.' '.$c.' '.$r."\n";

  // from left to cont
  if ($right == NULL)
    return substr ($t, $l, $c - $l);
  // from right to cont
  if ($left == NULL)
    return substr ($t, $c, $r - $c);
  // from left to right
  return substr ($t, $l, $r - $l);
}


/*
function
misc_substr2_debug ()
{
  $t = 'a123b123c123d';//123e123f';
  echo $t."\n";
  echo misc_substr2 ($t, '23', '23', NULL)."==b1\n";
  echo misc_substr2 ($t, NULL, '23', '23')."==c1\n";
  echo misc_substr2 ($t, '23', NULL, '23')."==b123c1\n";
}
*/


/*
function
misc_substr2_array ($t, $a)
{
  return misc_substr2 ($t, $a[0], $a[1], $a[2]);
}
*/


function
misc_array_unique_merge ($a)
{
  // should be array_unique2()
//  return array_merge (array_unique ($a));
  $b = array ();
  for ($i = 0; isset ($a[$i]); $i++)
    {
      $t = trim ($a[$i]);

      if ($t == '')
        continue;

      if (isset ($b[0]))
        if (in_array ($t, $b))
          continue;

      $b[] = $t;
    }
  return $b;
}


function
misc_playlist_load_string ($playlist_s)
{
/*
#-----AFTERHOURSDJS.ORG- WWW. AFTERHOURSDJS. ORG---------------------------
#EXTINF:-1, AH Djs 1->
http://205.188.215.229:8020
#EXTINF:-1, AH Djs 2->
http://207.200.96.232:8016
#EXTINF:-1, AH Djs 2->
http://207.200.96.230:8004
#-----TEKLAB.FM- WWW.TEKLAB. FM-----------------------------------
#EXTINF:-1, MIXED 1->
http://91.121.6.21:8002
#-----DRUM & BASS- WWW. DI. FM-----------------------------------
#EXTINF:-1, Drum & Bass 1 ->
http://207.200.96.229:8030
#-----DRUM & BASS- WWW. BASSDRIVE. COM-----------------------------------
#EXTINF:-1, Drum & Bass 1 ->
http://pngusa.streams.bassdrive.com:8000
#EXTINF:-1, Drum & Bass 2 ->
http://shoutdrive.streams.bassdrive.com:8000
#EXTINF:-1, Drum & Bass 3 ->
http://spechal.com.streams.bassdrive.com:8000
#EXTINF:-1, Drum & Bass 4 ->
http://visionshosting.net.streams.bassdrive.com:8004
#EXTINF:-1, Drum & Bass 5 ->
http://aol.streams.bassdrive.com:8012
#EXTINF:-1, Drum & Bass 6 ->
http://us-tx1.streams.bassdrive.com:9000
#EXTINF:-1, Drum & Bass 7 ->
http://uk.streams.bassdrive.com:8000
#EXTINF:-1, Drum & Bass 8 ->
http://pngusa-2.streams.bassdrive.com:8000
#EXTINF:-1, Drum & Bass 9 ->
http://hostdime.streams.bassdrive.com:8000
#-----BREAKS- WWW. DI. FM-----------------------------------
#EXTINF:-1, Breaks 1 ->
http://205.188.215.225:8002
#-----PROGRESSIVE- WWW. ETN. FM-----------------------------------
#EXTINF:-1, Progressive 1 - 192kbps
http://toronto.etn.fm:8210
#EXTINF:-1, Progressive 2 - [BETA] 256k EUROPE
http://etn.luon.net:8200
#-----PROGRESSIVE- WWW. DI. FM-----------------------------------
#EXTINF:-1, Progressive 1 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1026
#EXTINF:-1, Progressive 2 ->
http://scfire-ntc-aa03.stream.aol.com:80/stream/1026
#EXTINF:-1, Progressive 3 ->
http://scfire-ntc-aa04.stream.aol.com:80/stream/1026
#EXTINF:-1, Progressive 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1026
#EXTINF:-1, Progressive 5 ->
http://scfire-ntc-aa02.stream.aol.com:80/stream/1026
#EXTINF:-1, Progressive 6 ->
http://scfire-nyk-aa03.stream.aol.com:80/stream/1026
#EXTINF:-1, Progressive 7 ->
http://scfire-nyk-aa04.stream.aol.com:80/stream/1026
#-----LOUNGE- WWW. DI. FM-----------------------------------
#EXTINF:-1, Lounge 1 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1009
#EXTINF:-1, Lounge 2 ->
http://64.236.98.51:80/stream/1009
#EXTINF:-1, Lounge 3 ->
http://64.236.36.55:80/stream/1009
#EXTINF:-1, Lounge 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1009
#EXTINF:-1, Lounge 5 ->
http://64.236.126.41:80/stream/1009
#EXTINF:-1, Lounge 6 ->
http://64.236.126.42:80/stream/1009
#EXTINF:-1, Lounge 7 ->
http://64.236.34.106:80/stream/1009
#-----SOLO PIANO- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Solo Piano 1 ->
http://160.79.128.40:7794
#-----SALSA- WWW. SALSASTREAM. COM-----------------------------------
#EXTINF:-1, Salsa :D
http://205.188.215.231:8010
#-----ROOTS REGGAE- WWW. DI. FM-----------------------------------
#EXTINF:-1, Roots Reggae 1 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1017
#EXTINF:-1, Roots Reggae 2 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1017
#EXTINF:-1, Roots Reggae 3 ->
http://205.188.215.225:8000
#EXTINF:-1, Roots Reggae 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1017
#EXTINF:-1, Roots Reggae 5 ->
http://64.236.126.41:80/stream/1017
#EXTINF:-1, Roots Reggae 6 ->
http://64.236.126.42:80/stream/1017
#EXTINF:-1, Roots Reggae 7 ->
http://64.236.34.106:80/stream/1017
#EXTINF:-1, Roots Reggae 8 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1017
#-----SOUNDTRACKS- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Simply Soundtracks 1 ->
http://160.79.128.40:7774
#-----CLASSICE TECHNO- WWW. DI. FM-----------------------------------
#EXTINF:-1, Classic Techno & Trance ->
http://205.188.215.225:8004
#-----TECHNO- WWW. DI. FM-----------------------------------
#EXTINF:-1, Techno 1 ->
http://209.247.146.98:8000
#EXTINF:-1, Techno 2 ->
http://66.250.45.118:7204
#-----HARD DANCE- WWW. DI. FM-----------------------------------
#EXTINF:-1, Hard Dance 1 ->
http://scfire-ntc-aa03.stream.aol.com:80/stream/1025
#EXTINF:-1, Hard Dance 2 ->
http://scfire-ntc-aa04.stream.aol.com:80/stream/1025
#EXTINF:-1, Hard Dance 3 ->
http://scfire-nyk-aa03.stream.aol.com:80/stream/1025
#EXTINF:-1, Hard Dance 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1025
#EXTINF:-1, Hard Dance 5 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1025
#EXTINF:-1, Hard Dance 6 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1025
#EXTINF:-1, Hard Dance 7 ->
http://scfire-chi-aa03.stream.aol.com:80/stream/1025
#EXTINF:-1, Hard Dance 8 ->
http://scfire-chi-aa03.stream.aol.com:80/stream/1025
#-----TRANCE- WWW. ETN. FM-----------------------------------
#EXTINF:-1, Trance 1 - 192kbps
http://toronto.etn.fm:8110
#EXTINF:-1, Trance 2 - [BETA] 256k EUROPE
http://etn.luon.net:8100
#-----TRANCE- WWW. DI. FM-----------------------------------
#EXTINF:-1, Trance 1 ->
http://scfire-chi0l-1.stream.aol.com/stream/1003
#EXTINF:-1, Trance 2 ->
http://scfire-chi0l-2.stream.aol.com/stream/1003
#EXTINF:-1, Trance 3 ->
http://scfire-nyk-aa03.stream.aol.com:80/stream/1003
#EXTINF:-1, Trance 4 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1003
#EXTINF:-1, Trance 5 ->
http://scfire-nyk-aa04.stream.aol.com:80/stream/1003
#EXTINF:-1, Trance 6 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1003
#EXTINF:-1, Trance 7 ->
http://scfire-ntc-aa03.stream.aol.com:80/stream/1003
#EXTINF:-1, Trance 8 ->
http://scfire-ntc-aa04.stream.aol.com:80/stream/1003
#EXTINF:-1, Trance 9 ->
http://scfire-nyk-aa02.stream.aol.com:80/stream/1003
#-----VOCAL TRANCE- WWW. DI. FM-----------------------------------
#EXTINF:-1, Vocal Trance 1 ->
http://scfire-ntc-aa03.stream.aol.com:80/stream/1065
#EXTINF:-1, Vocal Trance 2 ->
http://scfire-ntc-aa04.stream.aol.com:80/stream/1065
#EXTINF:-1, Vocal Trance 3 ->
http://scfire-chi-aa03.stream.aol.com:80/stream/1065
#EXTINF:-1, Vocal Trance 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1065
#EXTINF:-1, Vocal Trance 5 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1065
#EXTINF:-1, Vocal Trance 6 ->
http://scfire-nyk-aa03.stream.aol.com:80/stream/1065
#EXTINF:-1, Vocal Trance 7 ->
http://scfire-nyk-aa04.stream.aol.com:80/stream/1065
#-----GOA & PSY TRANCE- WWW. DI. FM-----------------------------------
#EXTINF:-1, Goa & Psychedelic Trance 1 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1008
#EXTINF:-1, Goa & Psychedelic Trance 2 ->
http://64.236.98.51:80/stream/1008
#EXTINF:-1, Goa & Psychedelic Trance 3 ->
http://64.236.36.55:80/stream/1008
#EXTINF:-1, Goa & Psychedelic Trance 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1008
#EXTINF:-1, Goa & Psychedelic Trance 5 ->
http://64.236.126.41:80/stream/1008
#EXTINF:-1, Goa & Psychedelic Trance 6 ->
http://64.236.126.42:80/stream/1008
#EXTINF:-1, Goa & Psychedelic Trance 7 ->
http://64.236.34.106:80/stream/1008
#-----FUTURE SYNTHPOP- WWW. DI. FM-----------------------------------
#EXTINF:-1, Future Synthpop 1 ->
http://160.79.128.40:7234
#-----HOUSE- WWW. DI. FM-----------------------------------
#EXTINF:-1, House 1 ->
http://scfire-nyk-aa03.stream.aol.com:80/stream/1007
#EXTINF:-1, House 2 ->
http://scfire-nyk-aa04.stream.aol.com:80/stream/1007
#EXTINF:-1, House 3 ->
http://scfire-nyk-aa02.stream.aol.com:80/stream/1007
#EXTINF:-1, House 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1007
#EXTINF:-1, House 5 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1007
#EXTINF:-1, House 6 ->
http://scfire-ntc-aa03.stream.aol.com:80/stream/1007
#EXTINF:-1, House 7 ->
http://scfire-ntc-aa04.stream.aol.com:80/stream/1007
#-----SOULFUL HOUSE- WWW. DI. FM-----------------------------------
#EXTINF:-1, Soulful House 1 ->
http://205.188.215.232:8016
#EXTINF:-1, Soulful House 2 ->
http://208.122.59.30:7224
#EXTINF:-1, Soulful House 3 ->
http://160.79.128.40:7224
#-----HOLIDAY / SPECIAL- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Special 1 ->
http://205.188.215.232:8016
#EXTINF:-1, Special 2 ->
http://64.236.34.97:80/stream/1030
#EXTINF:-1, Special 3 ->
http://66.250.45.118:7734
#-----DJ MIXES- WWW. DI. FM-----------------------------------
#EXTINF:-1, DJ Mixes 1 ->
http://209.247.146.100:8000
#-----HAPPY HARDCORE- WWW. DI. FM-----------------------------------
#EXTINF:-1, Happy Hardcore 1 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1004
#EXTINF:-1, Happy Hardcore 2 ->
http://64.236.98.51:80/stream/1004
#EXTINF:-1, Happy Hardcore 3 ->
http://64.236.36.55:80/stream/1004
#EXTINF:-1, Happy Hardcore 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1004
#EXTINF:-1, Happy Hardcore 5 ->
http://64.236.126.41:80/stream/1004
#EXTINF:-1, Happy Hardcore 6 ->
http://64.236.126.42:80/stream/1004
#EXTINF:-1, Happy Hardcore 7 ->
http://64.236.34.106:80/stream/1004
#-----GABBER- WWW. DI. FM-----------------------------------
#EXTINF:-1, Gabber 1 ->
http://205.188.215.226:8006
#-----EURO DANCE- WWW. DI. FM-----------------------------------
#EXTINF:-1, Euro Dance 1 ->
http://scfire-chi0l-1.stream.aol.com/stream/1024
#EXTINF:-1, Euro Dance 2 ->
http://scfire-chi0l-2.stream.aol.com/stream/1024
#EXTINF:-1, Euro Dance 3 ->
http://scfire-nyk-aa04.stream.aol.com:80/stream/1024
#EXTINF:-1, Euro Dance 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1024
#EXTINF:-1, Euro Dance 5 ->
http://scfire-ntc-aa03.stream.aol.com:80/stream/1024
#EXTINF:-1, Euro Dance 6 ->
http://scfire-ntc-aa04.stream.aol.com:80/stream/1024
#EXTINF:-1, Euro Dance 7 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1024
#EXTINF:-1, Euro Dance 8 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1024
#-----CHILLOUT- WWW. DI. FM-----------------------------------
#EXTINF:-1, Chillout 1 ->
http://scfire-chi0l-1.stream.aol.com/stream/1035
#EXTINF:-1, Chillout 2 ->
http://scfire-chi0l-2.stream.aol.com/stream/1035
#EXTINF:-1, Chillout 3 ->
http://scfire-nyk-aa04.stream.aol.com:80/stream/1035
#EXTINF:-1, Chillout 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1035
#EXTINF:-1, Chillout 5 ->
http://scfire-ntc-aa03.stream.aol.com:80/stream/1035
#EXTINF:-1, Chillout 6 ->
http://scfire-ntc-aa04.stream.aol.com:80/stream/1035
#EXTINF:-1, Chillout 7 ->
http://scfire-chi-aa03.stream.aol.com:80/stream/1035
#EXTINF:-1, Chillout 8 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1035
#-----AMBIENT- WWW. DI. FM-----------------------------------
#EXTINF:-1, Ambient 1 ->
http://205.188.215.228:8006
#-----CLASSICAL- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Classical 1 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1006
#EXTINF:-1, Classical 2 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1006
#EXTINF:-1, Classical 3 ->
http://64.236.36.55:80/stream/1006
#EXTINF:-1, Classical 4 ->
http://64.236.36.54:80/stream/1006
#EXTINF:-1, Classical 5 ->
http://64.236.126.41:80/stream/1006
#EXTINF:-1, Classical 6 ->
http://64.236.126.42:80/stream/1006
#EXTINF:-1, Classical 7 ->
http://64.236.34.106:80/stream/1006
#-----PIANO JAZZ- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Piano Jazz 1 ->
http://160.79.128.40:7814
#-----BOSSA NOVA- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Bossa Nova 1 ->
http://160.79.128.40:7804
#-----CHRISTIAN- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Contemporary Christian 1 ->
http://160.79.128.40:7784
#-----NEW AGE- WWW. SKY. FM-----------------------------------
#EXTINF:-1, New Age 1 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1002
#EXTINF:-1, New Age 2 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1002
#EXTINF:-1, New Age 3 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1002
#EXTINF:-1, New Age 4 ->
http://64.236.126.41:80/stream/1002
#EXTINF:-1, New Age 5 ->
http://64.236.126.42:80/stream/1002
#EXTINF:-1, New Age 6 ->
http://64.236.34.106:80/stream/1002
#-----WORLD- WWW. SKY. FM-----------------------------------
#EXTINF:-1, World Music 1 ->
http://160.79.128.61:7674
#-----INDIE- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Indie Rock 1 ->
http://160.79.128.61:7724
#-----IDOBI RADIO- WWW. IDOBI. COM-----------------------------------
#EXTINF:-1, Rock / Punk 1 ->
http://67.159.5.221:80
#EXTINF:-1, Rock / Punk 2 ->
http://208.53.138.106:80
#EXTINF:-1, Rock / Punk 3 ->
http://67.159.5.35:80
#EXTINF:-1, Rock / Punk 4 ->
http://67.159.5.139:80
#-----ALTERNATIVE ROCK- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Alternative Rock 1 ->
http://160.79.128.40:7754
#-----CLASSIC ROCK- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Classic Rock 1 ->
http://72.51.33.149:8000
#EXTINF:-1, Classic Rock 2 ->
http://160.79.128.40:7734
#-----GUITAR- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Classical & Flamenco Guitar 1 ->
http://205.188.215.226:8020
#EXTINF:-1, Classical & Flamenco Guitar 2 ->
http://207.200.96.229:8016
#EXTINF:-1, Classical & Flamenco Guitar 3 ->
http://205.188.215.232:8014
#EXTINF:-1, Classical & Flamenco Guitar 4 ->
http://205.134.236.59:8050
#-----OLDIES- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Oldies 1 ->
http://209.247.146.99:8000
#EXTINF:-1, Oldies 2 ->
http://4.79.190.66:8000
#EXTINF:-1, Oldies 3 ->
http://160.79.128.61:7684
#-----COUNTRY- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Country 1 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1019
#EXTINF:-1, Country 2 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1019
#EXTINF:-1, Country 3 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1019
#EXTINF:-1, Country 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1019
#EXTINF:-1, Country 5 ->
http://64.236.126.41:80/stream/1019
#EXTINF:-1, Country 6 ->
http://64.236.126.42:80/stream/1019
#EXTINF:-1, Country 7 ->
http://64.236.34.106:80/stream/1019
#-----70s HITS- WWW. SKY. FM-----------------------------------
#EXTINF:-1, 70s Hits 1 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1076
#EXTINF:-1, 70s Hits 2 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1076
#EXTINF:-1, 70s Hits 3 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1076
#EXTINF:-1, 70s Hits 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1076
#EXTINF:-1, 70s Hits 5 ->
http://64.236.126.41:80/stream/1076
#EXTINF:-1, 70s Hits 6 ->
http://64.236.126.42:80/stream/1076
#EXTINF:-1, 70s Hits 7 ->
http://64.236.34.106:80/stream/1076
#-----80s HITS- WWW. SKY. FM-----------------------------------
#EXTINF:-1, 80s Hits 1 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1013
#EXTINF:-1, 80s Hits 2 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1013
#EXTINF:-1, 80s Hits 3 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1013
#EXTINF:-1, 80s Hits 4 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1013
#EXTINF:-1, 80s Hits 5 ->
http://64.236.126.41:80/stream/1013
#EXTINF:-1, 80s Hits 6 ->
http://64.236.126.42:80/stream/1013
#EXTINF:-1, 80s Hits 7 ->
http://64.236.34.106:80/stream/1013
#-----TOP HITS- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Top Hits 1 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1014
#EXTINF:-1, Top Hits 2 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1014
#EXTINF:-1, Top Hits 3 ->
http://64.236.36.55:80/stream/1014
#EXTINF:-1, Top Hits 4 ->
http://64.236.36.54:80/stream/1014
#EXTINF:-1, Top Hits 5 ->
http://64.236.126.41:80/stream/1014
#EXTINF:-1, Top Hits 6 ->
http://64.236.126.42:80/stream/1014
#EXTINF:-1, Top Hits 7 ->
http://64.236.34.106:80/stream/1014
#-----DATEMPO LOUNGE- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Datempo Lounge 1 ->
http://38.119.49.140:8000
#EXTINF:-1, Datempo Lounge 2 ->
http://38.119.49.140:8030
#EXTINF:-1, Datempo Lounge 3 ->
http://160.79.128.40:7744
#-----URBAN JAMZ- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Urban Jamz 1 ->
http://66.135.38.32:8000
#EXTINF:-1, Urban Jamz 2 ->
http://160.79.128.61:7704
#-----CLASSIC RAP- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Classic Rap & Hip Hop 1 ->
http://64.34.178.168:8000
#EXTINF:-1, Classic Rap & Hip Hop 2 ->
http://160.79.128.61:7694
#-----SMOOTH JAZZ- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Smooth Jazz 1 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1010
#EXTINF:-1, Smooth Jazz 2 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1010
#EXTINF:-1, Smooth Jazz 3 ->
http://64.236.126.42:80/stream/1010
#EXTINF:-1, Smooth Jazz 4 ->
http://scfire-chi-aa01.stream.aol.com:80/stream/1010
#EXTINF:-1, Smooth Jazz 5 ->
http://scfire-chi-aa02.stream.aol.com:80/stream/1010
#EXTINF:-1, Smooth Jazz 6 ->
http://64.236.126.41:80/stream/1010
#-----JAZZ- WWW. SKY. FM-----------------------------------
#EXTINF:-1,  Modern Jazz 1 ->
http://205.188.215.227:8008
#-----UPTEMPO JAZZ- WWW. SKY. FM-----------------------------------
#EXTINF:-1, Uptempo Smooth Jazz 1 ->
http://82.149.227.171:8000
#EXTINF:-1, Uptempo Smooth Jazz 2 ->
http://81.29.66.64:80
#EXTINF:-1, Uptempo Smooth Jazz 3 ->
http://160.79.128.61:7714
*/
//  $line = file ();
  $line = explode ("\n", $playlist_s);

  // demux
  $demux = 0;
  for ($i = 0; isset ($line[$i]); $i++)
    if (strstr ($p, '#EXTM3U'))
      {
        $demux = 1; // m3u
        break;
      }
  // TODO: support pls, xspf, and wpl too

  // parse
  if ($demux == 1)
    {
      $a = array ();
      for ($i = 0; isset ($line[$i]) && isset ($line[$i + 1]); $i++)
        {
          $p = $line[$i];

          if (substr ($p, 0, 1) != '#')
            continue;

          if (strstr ($p, '#EXTINF:'))
            {
              $p = substr ($p, 8, -3);
              $o = strpos ($p, ',');
              $link = $line[++$i];
              $a[] = array ('title' => trim (substr ($p, $o + 1)),
                            'link' => $link,
                            'media_duration' => trim (substr ($p, 0, $o))
);
            }
          else
            $a[] = array ('title' => trim ($p, ' #-'));
        }

      // DEBUG
//      echo '<pre><tt>';
//      print_r ($a);

      return $a;
    }

//  if ($demux == 0)
//    echo 'unknown playlist format';

  return NULL;
}


function
misc_crc8 ($s, $crc = 0)
{
  $polynomial = (0x1070 << 3);

  $len = strlen ($s);
  for ($i = 0; $i < $len; $i++)
    {
      $crc ^= ord ($s[$i]);
      $crc <<= 8;
      for ($j = 0; $j < 8; $j++)
        {
          if (($crc & 0x8000) != 0)
            $crc ^= $polynomial;
          $crc <<= 1;
        }
      $crc = ($crc >> 8) & 0xff;
    }
  return $crc;
}


function
misc_crc16 ($s, $crc = 0)
{
  $len = strlen ($s);
  for ($i = 0; $i < $len; $i++)
    {
      $crc ^= ord ($s[$i]);
      for ($j = 0; $j < 8; $j++)
        if (($crc & 1) == 1)
          $crc = ($crc >> 1) ^ 0xa001;
        else
          $crc >>= 1;
    }

  return $crc;
}


function
misc_crc24 ($s, $crc = 0xb704ce)
{
  $len = strlen ($s);
  for ($n = 0; $n < $len; $n++)
    {
      $crc ^= (ord($s[$n]) & 0xff) << 0x10;
      for ($i = 0; $i < 8; $i++)
        {
          $crc <<= 1;
          if ($crc & 0x1000000) $crc ^= 0x1864cfb;
        }
    }

  return ((($crc >> 0x10) & 0xff) << 16) | ((($crc >> 0x8) & 0xff) << 8) | ($crc & 0xff);
}


function
misc_related_string_id_sort ($a, $b)
{
  return strlen ($b) - strlen ($a);
}


function
misc_related_string_id ($s)
{
  $t = misc_get_keywords ($s, 0); // isalnum
  $a = explode (' ', $t);
  usort ($a, 'misc_related_string_id_sort');

  // fabricate 32bit id from 4 longest keywords
  $id = 0;
  for ($i = 0; isset ($a[$i]) && $i < 4; $i++)
    {
      $id <<= 8;
      $id |= misc_crc8 ($a[$i]) & 0xff;
    }   
  return $id & 0xffffffff;
}


function
get_ip ($address)
{
  // if it isn't a valid IP assume it is a hostname
  $preg = '#^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}'
         .'(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$#';
  if (!preg_match($preg, $address))
    {
      $result = gethostbyname ($address);

      // not a valid host nor IP
      if ($result === $address)
        $result = false;
    }
  else
    $result = $address;
        
  return $result;
}


function
scandir4 ($path, $sort)
{
  $i = 0;
  $a = array ();

  $dir = opendir ($path);
  while (($a[$i] = readdir ($dir)) !== false)
    $i++;
  closedir ($dir);

  if ($sort)
    array_multisort ($a, SORT_DESC);
  else
    sort ($a);

  return $a;
}


// recursive scandir
function
scandir5 ($directory, $recursive = true)
{
  $result = array ();
  $handle =  opendir ($directory);
  while ($datei = readdir ($handle))
    if (($datei != '.') && ($datei != '..'))
      {
        $file = $directory.$datei;
        if (is_dir ($file))
          {
            if ($recursive)
              $result = array_merge ($result, scandir5 ($file.'/'));
          }
        else
          $result[] = $file;
      }
  closedir ($handle);
  return $result;
}


function
misc_dirmtime ($directory, $recursive = true)
{
//  $a = scandir5 ($directory, $recursive);
  $max = 0;
//  foreach ($a as $val)
//    {
//      $v = filemtime ($val);
//      if ($v > $max) $max = $v;
//    }
  // DEBUG
//  echo date ('misc_dirmtime(): Y-m-d H:i:s'."\n", $max);
  return $max;
}


function
misc_download ($url, $path, $use_tor = 0)
{
  if ($use_tor == 1)
    {
      if (!($img = tor_get_contents ($url)))
        return -1;
    }
  else if (!($img = file_get_contents ($url)))
    return -1;

  if (!($out = fopen ($path, 'wb')))
    return -1;
 
  fwrite ($out, $img);
  fclose ($out);

  // error
  if (!file_exists ($path))
    return -1;
  return 0;
}


function
misc_download_noclobber ($url, $path, $use_tor = 0)
{
  // DEBUG
//  echo $url."\n";

  if (file_exists ($path)) // do not overwrite existing files
    {
      echo 'WARNING: file '.$path.' exists, skipping'."\n";
      return 1;
    }
  // DEBUG
//  echo $path."\n";

  return misc_download ($url, $path, $use_tor);
}


function
time_ms ()
// returns milliseconds since midnight
{
  $tv = gettimeofday ();

  if ($tv)
    {
      $t = $tv['usec'] / 1000;
      $t += ($tv['sec'] % 86400) * 1000;
    }

  return $t;
}


function
islocalhost ()
{
  return $_SERVER['REMOTE_ADDR'] == $_SERVER['SERVER_ADDR'];
}


function
strip_notags ($str)
{
  // strip everything but tags

  // TODO: remove this lame hack
  $first = 1;
  $p = '';
  for ($i = 0; $i < strlen ($str); $i++)
    {
      if (in_tag (substr ($str, $i)))
        {
          if ($first)
            {
              $p .= '<';
              $first = 0;
            }
          $p .= substr ($str, $i, 1);
        }
      else $first = 1;
    }
  return $p;
}



function
strip_tags2 ($s)
{
  // so one text does not get glued to another because of strip_tags()
  return strip_tags (str_replace (array ('>',   '<'), array ('> ', ' <'), $s));
}


function
echo_gzip ($p)
{
  ob_start ('ob_gzhandler');
  echo $p;
  ob_end_flush ();
}


function
get_suffix ($filename)
// get_suffix() never returns NULL
{
  $p = basename ($filename);
  if (!$p)
    $p = $filename;

  $s = strrchr ($p, '.');
  if (!$s)
    $s = strchr ($p, 0);
  if ($s == $p)
    $s = strchr ($p, 0);

  return $s;
}


function
set_suffix ($filename, $suffix)
{
  // always use set_suffix() and NEVER the code below
  return str_replace (get_suffix ($filename), $suffix, $filename);
}


function
str_shorten ($s, $limit)
{
  // Make sure a small or negative limit doesn't cause a negative length for substr().
  if ($limit < 3)
    $limit = 3;

  // Now truncate the string if it is over the limit.
  if (strlen ($s) > $limit)
    return substr($s, 0, $limit - 3).'..';
  else
    return $s;
}


function
in_tag ($s)
{
  // are we inside a tag?
  return strpos ($s, '>') < strpos ($s, '<');
}


function
is_url ($s)
{
  // checks if string is a url
  $is_url = false;

  if (strlen ($s) > 4 &&
      isalpha ($s[0]) &&
      !strstr ($s, '..') &&
      substr_count ($s, '.') == 2 &&
      (substr ($s, -4, 1) == '.' || substr ($s, -3, 1) == '.'))
    $is_url = true;

  if (filter_var ($s, FILTER_VALIDATE_URL) != FALSE)
    $is_url = true;

  return $is_url;
}


if (!function_exists('sys_get_temp_dir'))
{
function
sys_get_temp_dir ()
{
  if ($temp = getenv ('TMP'))
    return $temp;
  if ($temp = getenv ('TEMP'))
    return $temp;
  if ($temp = getenv ('TMPDIR'))
    return $temp;
  $temp = tempnam (__FILE__, '');
  if (file_exists ($temp))
    {
      unlink ($temp);
      return dirname ($temp);
    }
  return null;
}
}


if (!function_exists ('sprint_r'))
{
function 
sprint_r ($var)
{
  ob_start ();

  print_r ($var);

  $ret = ob_get_contents ();

  ob_end_clean ();

  return $ret;
}
}


function
get_cookie ($name)
{
  if (isset ($_COOKIE[$name]))
    return $_COOKIE[$name];

  return NULL;
}


function
get_request_value ($name)
{
//  if (isset ($_POST[$name]))
//    return $_POST[$name];

//  if (isset ($_GET[$name]))
//    return $_GET[$name];

  if (isset ($_REQUEST[$name])) // and cookies
    return $_REQUEST[$name];

  return NULL;
}


function
http_build_query2 ($args = array(), $use_existing_arguments = false)
{
  if ($use_existing_arguments)
    $a = array_merge ($_GET, $args); // $args overwrites $_GET
  else
    $a = $args;

  if (!sizeof ($a))
    return '';

  return http_build_query ($a);
} 


function
random_user_agent ()
{
  $ua = array (
    array ('Mozilla', 3, 6),
    array ('Opera', 4, 6),
    array ('Microsoft Internet Explorer', 6, 9),
    array ('ia_archiver', 0, 9),
);
  $op = array (
    array ('Windows', 4, 4),
    array ('Windows XP', 6, 6),
    array ('Linux', 2, 2),
    array ('Windows NT', 4, 4),
    array ('Windows 2000', 5, 5),
    array ('OSX', 9, 9),
);
  $a =  $ua[rand (0,count ($ua) - 1)];
  $agent  = $a[0].'/'.rand ($a[1],$a[2]).'.0';
  $a = $op[rand (0,count ($op) - 1)];
  $agent .= ' ('.$a[0].' '.rand ($a[1],$a[2]).'.0; en-US;)';
  return $agent;
}


function
encodemail ($my_mail)
{
  // light email protection
  $p = '';
  for ($i = 0; $i < strlen ($my_mail); $i++)
    $p .= "%".dechex (ord ($my_mail[$i])); 
  return $p;
}


function
get_domain ($url)
{
  if (filter_var ($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) === FALSE)
    return false;
  $parts = parse_url ($url);
  return $parts['scheme'].'://'.$parts['host'];
}


function
file_post_contents ($url, $vars, $timeout = 300)
{
  $sock = curl_init ();
  if ($sock == FALSE)
    {
      echo 'CURL support missing';
      return NULL;
    }

  curl_setopt ($sock, CURLOPT_URL, $url);
  $agent = random_user_agent ();
  curl_setopt ($sock, CURLOPT_USERAGENT, $agent);
  curl_setopt ($sock, CURLOPT_RETURNTRANSFER, 1); // return to string instead of spewing to output
  curl_setopt ($sock, CURLOPT_CONNECTTIMEOUT, $timeout);
  curl_setopt ($sock, CURLOPT_FOLLOWLOCATION, 1);  // follow location header, not sure if this is needed.

  // allow ssl always
  curl_setopt ($sock, CURLOPT_SSL_VERIFYPEER, false);

  // cookies
  $parts = parse_url ($url); 
  curl_setopt ($sock, CURLOPT_COOKIEJAR, $parts['host'].'.txt');
  curl_setopt ($sock, CURLOPT_COOKIEFILE, $parts['host'].'.txt');

  // Expect: 100-continue doesn't work properly with lightTPD
  // This fix by zorro http://groups.google.com/group/php.general/msg/aaea439233ac709b
  curl_setopt ($sock, CURLOPT_HTTPHEADER, array ('Expect:')); 

  // DEBUG
//  curl_setopt ($sock, CURLOPT_VERBOSE, 1);

  // set method POST
  curl_setopt ($sock, CURLOPT_POST, 1);
  curl_setopt ($sock, CURLOPT_POSTFIELDS, $vars);

  $response = curl_exec ($sock);
  if ($response == FALSE)
    {
      echo 'file_post_contents() failed';
      return NULL;
    }

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($response);
 
  // DEBUG
//  echo '<pre><tt>';
//  print_r (curl_getinfo ($sock));
//  print_r (curl_getinfo ($sock, CURLINFO_HTTP_CODE));

  curl_close ($sock);

  return $response;
}


// use torify instead
function
tor_get_contents ($url, $tor_proxy_host = '127.0.0.1', $tor_proxy_port = 9050, $timeout = 300)
{
  $sock = curl_init ();
  if ($sock == FALSE)
    {
      echo 'CURL support missing';
      return NULL;
    }

  curl_setopt ($sock, CURLOPT_PROXY, $tor_proxy_host.':'.$tor_proxy_port);
  curl_setopt ($sock, CURLOPT_URL, $url);
//  curl_setopt ($sock, CURLOPT_HEADER, 1);
  $agent = random_user_agent ();
  curl_setopt ($sock, CURLOPT_USERAGENT, $agent);
  curl_setopt ($sock, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($sock, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt ($sock, CURLOPT_TIMEOUT, $timeout);
  curl_setopt ($sock, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

  // DEBUG
//  curl_setopt ($sock, CURLOPT_VERBOSE, 1);

  $response = curl_exec ($sock);
  if ($response == FALSE)
    {
      echo 'TOR not running';
      return NULL;
    }

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($response);

  // DEBUG
//  echo '<pre><tt>';
//  print_r (curl_getinfo ($sock));

  curl_close ($sock);

  return $response;
}


function
misc_read_cache ($cachefile, $cache_time)
{
  if (file_exists($cachefile))
    if (time() - $cache_time < filemtime ($cachefile))
      {
        $data = file_get_contents ($cachefile);
        return unserialize ($data);
      }
  return NULL;
}


function
misc_write_cache ($cachefile, $data)
{
  $cache = serialize ($data);
  $fh = fopen ($cachefile, 'wb');
  if ($fh)
    {
      fwrite (fh, $cache, sizeof ($cache));
      fclose (fh);
      touch ($cachefile, time()); // make sure it has time()
    }
}


// replace custom tags
//$template_replace = array (
//   array ('login_button' => login_button ()),
//   array ('content' => content ()),
//   array ('register_button' => register_button ()),
//);
function
misc_template ($src, $template_replace)
{
  // $template_replace = array (array ('bla', 'hello'))
  //   replaces <!-- bla --> with hello
  $p = $src;
  $a = array_keys ($template_replace);
  for ($i = 0; isset ($a[$i]); $i++)
    {
      $func = $template_replace[$a[$i]];
//      if (function_exists ($func))
//        $s = $func ();
//      else
        $s = $func;
//      $p = str_replace ('<!-- '.$a[$i].' -->', $s, $p);
      $p = str_replace ($a[$i], $s, $p);
    }

  return $p;
}


function
misc_whois ($query)
{
  $server = 'whois.verisign-grs.com';

  if (!($fp = fsockopen ($server, 43, $errno, $errstr, 15)))
    return false;

  fwrite ($fp, $query.'\r\n');

  $p = '';
  while (!feof($fp))
    $p .= fgets($fp, 1024);

  fclose($fp);

  return $p;
}


function
misc_url_exists ($url)
{
  if (!strncasecmp ($url, 'udp://'))  // check UDP
    {
      $a = explode (':', $url);

      if (!($fp = fsockopen("udp://".$a[0], $a[1], $errno, $errstr, 1)))
        return false;

      socket_set_timeout ($fp, 2);
      if (!fwrite ($fp,"\x00"))
        return false;

      $t1 = time();
      $res = fread($fp, 1);
      $t2 = time();
      fclose ($fp);

      if ($t2 - $t1 > 1)
        return false;

      if (!($res))
        return false;

      return true;
    }

  if (file_get_contents ($url, FALSE, NULL, 0, 0) === false)
    return false;
  return true;
}


function
check_udp ($address, $port)
{
  return misc_url_exists ('udp://'.$address.':'.$port);
}


/*
  My notation of variables:
  i_ = integer, ex: i_count
  a_ = array, a_html
  b_ = boolean,
  s_ = string
 
  What it does:
  - parses a html string and get the tags
  - exceptions: html tags like <br> <hr> </a>, etc
  - At the end, the array will look like this:
  ["IMG"][0]["SRC"] = "xxx"
  ["IMG"][1]["SRC"] = "xxx"
  ["IMG"][1]["ALT"] = "xxx"
  ["A"][0]["HREF"] = "xxx"
*/
function
misc_parse_html ($s)
{
/*
function
misc_explode_tag ($html_tag)
{
  // returns 2d array with tag name and attributes and their values
  $s = strpos ($html_tag, '<') + 1;
  $l = strrpos ($html_tag, '>') - $s;
  $p = substr ($html_tag, $s, $l);
  $p = trim ($p);
  // '=      "' to '="'
  $p = str_replace (array ('= "','=  "'), '="', $p);
  $p = str_replace (array ('= "','=  "'), '="', $p);
  $p = str_replace (array ('= "','=  "'), '="', $p);
  // '      ="' to '="'
  $p = str_replace (array (' ="','  ="'), '="', $p);
  $p = str_replace (array (' ="','  ="'), '="', $p);
  $p = str_replace (array (' ="','  ="'), '="', $p);

  // DEBUG
//  echo $p;

  $a = explode (' ', $p);
  $a = misc_array_unique_merge ($a);

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($a);

  $tag = array ();
  $count = 0;
  for ($i = 0; isset ($a[$i]); $i++)
    if (strpos ($a[$i], '=')) // is attribute
      {
        $aa = explode ('=', $a[$i], 2);

        $tag[strtolower (trim ($aa[0]))] = '"'.trim (trim ($aa[1]), '"').'"'; // trim first spaces then quotes
      }
    else // attribute without value (e.g. tag name)
      $tag[strtolower (trim ($a[$i]))] = NULL;

  // DEBUG
//  echo '<pre><tt>';
//  print_r ($tag);

  return $tag;
}


function
misc_gettag ($html_tag, $attr = array(), $use_existing_attr = false)
{
  $tag = misc_explode_tag ($html_tag);

  if (!$use_existing_attr)
    {
      // BUT keep the attributes without value (e.g. tag name)
      $attr_keys = array_keys ($tag);
      for ($i = 0; $attr_keys[$i]; $i++)
        if ($tag[$attr_keys[$i]] == NULL)
          $a[$attr_keys[$i]] = $tag[$attr_keys[$i]];
      $tag = $a;
    }

  if (!$attr)
    return misc_implode_tag ($tag);

//  $tag = array_replace ($tag, $attr);
  $attr_keys = array_keys ($attr);
  for ($i = 0; $attr_keys[$i]; $i++)
    if ($attr[$attr_keys[$i]] != NULL)
      $tag[$attr_keys[$i]] = '"'.trim ($attr[$attr_keys[$i]], '"').'"';

  return misc_implode_tag ($tag);
}
*/
  $i_indicatorL = 0;
  $i_indicatorR = 0;
  $s_tagOption = "";
  $i_arrayCounter = 0;
  $a_html = array();

  // search for a tag in string
  while(is_int (($i_indicatorL = strpos ($s, '<', $i_indicatorR))))
    {
      // get everything into tag...
      $i_indicatorL++;
      $i_indicatorR = strpos ($s, '>', $i_indicatorL);
      $s_temp = substr ($s, $i_indicatorL, ($i_indicatorR - $i_indicatorL));
      $a_tag = explode (' ', $s_temp);
      // here we get the tag's name
      list(, $s_tagName, , ) = each ($a_tag);
      $s_tagName = strtoupper ($s_tagName);
      // well, I am not interesting in <br>, </font> or anything else like that...
      $b_boolOptions = is_array (($s_tagOption = each ($a_tag))) && $s_tagOption[1];
      if ($b_boolOptions)
        {
          // without this, we will mess up the array
          $i_arrayCounter = (int) count ($a_html[$s_tagName]);
          // get the tag options, like src="htt://". Here, s_tagTokOption is 'src'
          // and s_tagTokValue is '"http://"'

          do
            {
              $s_tagTokOption = strtoupper(strtok($s_tagOption[1], "="));
              $s_tagTokValue = trim(strtok("="));
              $a_html[$s_tagName][$i_arrayCounter][$s_tagTokOption] =
              $s_tagTokValue;
              $b_boolOptions = is_array(($s_tagOption=each($a_tag))) &&
              $s_tagOption[1];
            } while($b_boolOptions);
        }
    }
  return $a_html;
}




$ctype__ = array(32,32,32,32,32,32,32,32,32,40,40,40,40,40,32,32,32,32,32,32,32,32,32,32,32,32,32,32,32,32,32,32,
  -120,16,16,16,16,16,16,16,16,16,16,16,16,16,16,16,4,4,4,4,4,4,4,4,4,4,16,16,16,16,16,16,
  16,65,65,65,65,65,65,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,16,16,16,16,16,
  16,66,66,66,66,66,66,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,16,16,16,16,32,
  0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
  0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
  0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
  0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
function isalnum ($c){ global $ctype__; return ((($ctype__[( ord($c) )]&(01 | 02 | 04 )) != 0)?1:0);}
function isalpha ($c){ global $ctype__; return ((($ctype__[( ord($c) )]&(01 | 02 )) != 0)?1:0);}
function isascii ($c){ global $ctype__; return (((( ord($c) )<=0177) != 0)?1:0);}
function iscntrl ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 040 ) != 0)?1:0);}
function isdigit ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 04 ) != 0)?1:0);}
function isgraph ($c){ global $ctype__; return ((($ctype__[( ord($c) )]&(020 | 01 | 02 | 04 )) != 0)?1:0);}
function islower ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 02 ) != 0)?1:0);}
function isprint ($c){ global $ctype__; return ((($ctype__[( ord($c) )]&(020 | 01 | 02 | 04 | 0200 )) != 0)?1:0);}
function ispunct ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 020 ) != 0)?1:0);}
function isspace ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 010 ) != 0)?1:0);}
function isupper ($c){ global $ctype__; return ((($ctype__[( ord($c) )]& 01 ) != 0)?1:0);}
function isxdigit ($c){ global $ctype__; return ((($ctype__[( ord($c) )]&(0100 | 04 )) != 0)?1:0);}


function
time_count ($t_date)
{
  static $t_now = 0;
  static $t_count = 0;

  if ($t_now == 0)
    $t_now = $t_count = time ();

  $p = NULL;

  while ($t_count > $t_date)
    {
      $t_calc = $t_now - $t_count;

      if ($t_calc < 3600)
        {
          $t_count -= 300;
          if ($t_calc)
            if ($t_count < $t_date)
            $p .= sprintf ('%s', $t_calc / 60 .' minutes');
        }
      else if ($t_calc < 86400)
        {
          $t_count -= 3600;
          if ($t_count < $t_date)
            $p .= sprintf ('%s', $t_calc / 3600 .' hours');
        }
      else
        {
          $t_count -= 86400;
          if ($t_count < $t_date)
            $p .= sprintf ('%s', $t_calc / 86400 .' days');
        }
    }

  return $p;
}


function
misc_get_keywords ($s, $flag = 0) // default: isalnum()
{
  $keyword_size = 3;

  // normalize
  $s = str_replace (array ('. ', ',', ';', '!', '?', '"'), ' ', strtolower ($s));

  // remove punctuation
  for ($i = 0; $i < strlen ($s); $i++)
    if (ispunct ($s[$i]) && $s[$i] != '_' && $s[$i] != '.')
      $s[$i] = ' ';

  // remove eventual html tags
  $s = strip_tags2 ($s);

  // strtolower()
//  $s = strtolower ($s);

  // explode and trim
  $a = explode (' ', $s);
  for ($i = 0; isset ($a[$i]); $i++)
    $a[$i] = trim ($a[$i], ' .');

  // unify
  $a = misc_array_unique_merge ($a);

  // stemmer.php (english only)
//  if (class_exists (stemmer))
//    {
//      $s = new stemmer;
//
//      for ($i = 0; isset ($a[$i]); $i++)
//        $a[$i] = $s->stem ($a[$i]);
//    }

  $p = '';
  $func = $flag ? 'isalpha' : 'isalnum';
  for ($i = 0; isset ($a[$i]); $i++)
    {
      $s = $a[$i];

      if (strlen ($s) < $keyword_size)
        continue;

      $found = 0;
      for ($j = 0; $j < strlen ($s); $j++)
        if (!($func ($s[$j])) && $s[$j] != '_' && $s[$j] != '.')
          {
            $found = 1;
            break;
          }
      if ($found == 1)
        continue;

      $p .= trim ($s).' ';
    }

  // DEBUG
//  echo $p;

  return trim ($p);
}


function
str_similar ($str1, $str2)
{
/*
    $count = 0;
   
    $str1 = ereg_replace("[^a-z]", ' ', strtolower($str1));
    while(strstr($str1, '  ')) {
        $str1 = str_replace('  ', ' ', $str1);
    }
    $str1 = explode(' ', $str1);
   
    $str2 = ereg_replace("[^a-z]", ' ', strtolower($str2));
    while(strstr($str2, '  ')) {
        $str2 = str_replace('  ', ' ', $str2);
    }
    $str2 = explode(' ', $str2);
   
    if(count($str1)<count($str2)) {
        $tmp = $str1;
        $str1 = $str2;
        $str2 = $tmp;
        unset($tmp);
    }
   
    for($i=0; $i<count($str1); $i++) {
        if(in_array($str1[$i], $str2)) {
            $count++;
        }
    }
   
    return $count/count($str2)*100;
*/
//      if (similar_text ($last, $category->title, $match) < 50)
//      if (levenshtein ($last, $category->title) > 100)
//      if (str_compare ($last, $category->title) < 50)
  $t = array ();
  $t[] = explode (' ', $str1);
  $t[] = explode (' ', $str2);
  return !strncmp (soundex ($t[0][0]), soundex ($t[1][0]), 3);
}


function
parse_links ($s, $cached = 1)
{
  // turn plain text urls into links
//  return preg_replace ('/\\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i', "<a href=\"#\" onclick=\"open_url('\\0')\";return false;>\\0</a>", $s);
//  return ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\">\\0</a>", $s);
//  $s = eregi_replace("((([ftp://])|(http(s?)://))((:alnum:|[-\%\.\?\=\#\_\:\&\/\~\+\@\,\;])*))","<a href = '\\0' target='_blank'>\\0</a>", $s);
//  $s = eregi_replace("(([^/])www\.|(^www\.))((:alnum:|[-\%\.\?\=\#\_\:\&\/\~\+\@\,\;])*)", "\\2<a href = 'http://www.\\4'>www.\\4</a>", $s);

  $a = explode (' ', strip_tags2 ($s));
  $a = misc_array_unique_merge ($a); // remove dupes

  // find eventual urls
  for ($i = 0; isset ($a[$i]); $i++)
    if (is_url ($a[$i]))
      {
        $url = $a[$i];
// DEBUG
//echo $url;
        if (!stristr ($url, 'http://'))
          $url = 'http://'.$url;

// cached: http://web.archive.org/web/*/http://ucon64.sourceforge.net
        $link = '<a href="'.$url.'">'.$url.'</a>';
        if ($cached == 1)
          $link = $link.'[<a href="http://web.archive.org/web/*/'.$url.'">Cached</a>]';

        $s = str_replace ($a[$i], $link, $s);
      }

  return $s;
}


function
detect_links ($s, $cached = 1)
{
  // find urls in text and turn them into links
  return parse_links ($s, $cached);
}


function
misc_search ($search)
{
  $a = array ();

  // ftp
  $search = str_replace (' ', '+', $search);
//  $query = 'intitle:("Index.of.*"|"Index.von.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:in
  $query = 'intitle:("Index.of.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:index';
  $a[] = 'http://www.google.com/search?q='.urlencode ($query);
  // video
  $search = str_replace (' ', '+', $search);
//  $query = 'intitle:("Index.of.*"|"Index.von.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:in
  $query = 'intitle:("Index.of.*") +("'.$search.'") -inurl:(htm|php|html|py|asp|pdf) -inurl:inurl -inurl:index';
  $a[] = 'http://www.google.com/search?q='.urlencode ($query);
// youtube
//    <feed>http://video.google.com/videosearch?q=quakeworld&amp;so=1&amp;output=rss&amp;num=1000</feed>
//    <feed>http://gdata.youtube.com/feeds/api/videos?vq=quake1&amp;max-results=50</feed>
  $a[] = 'http://en.wikipedia.org/w/index.php?title=Special%3ASearch&redirs=0&search='.$search.'&fulltext=Search&ns0=1'; // wikipedia
  $a[] = 'http://www.google.com/search?ie=UTF-8&oe=utf-8&q='.$search; // lyrics
  $a[] = 'http://images.google.com/search?ie=UTF-8&oe=utf-8&q='.$search;             // images
  $a[] = 'http://images.google.com/search?ie=UTF-8&oe=utf-8&q=walkthrough+'.$search; // walkthrough
  $a[] = 'http://images.google.com/search?ie=UTF-8&oe=utf-8&q=cheat+'.$search; // cheat

  return $a;
}


/*
function
vname (&$var, $scope=false, $prefix='unique', $suffix='value')
{
  if ($scope)
    $vals = $scope;
  else
    $vals = $GLOBALS;

  $old = $var;
  $var = $new = $prefix.rand().$suffix;
  $vname = FALSE;

  foreach ($vals as $key => $val)
    if($val === $new)
      $vname = $key;

  $var = $old;

  return $vname;
}
*/


function
misc_exec ($cmdline, $debug = 0)
{
  if ($debug)
    echo 'cmdline: '.$cmdline."\n"
        .'escaped: '.escapeshellcmd ($cmdline).' (not used)'."\n"
;
  if ($debug == 2)
    return '';

  $a = array();

  exec ($cmdline, $a, $res);

  $p = '';
  if ($debug)
    $p = $res."\n";

  $p .= implode ("\n", $a);

  return $p;
}


function
misc_exec_wget ($url, $output_path = '.', $noclobber = 0, $wget_path = '/usr/local/bin/wget', $wget_opts = '')
{
//  global $wget_path;
//  global $wget_opts;
  $debug = 0;

  // DEBUG
//  echo $url."\n";

  if ($noclobber == 1)
    {
      if (file_exists ($output_path)) // do not overwrite existing files
        {
          echo 'WARNING: file '.$output_path.' exists, skipping'."\n";
          return 1;
        }
      // DEBUG
//      echo $output_path."\n";
    }

  // download
  $p = $wget_path.' '.$wget_opts.' -U "'.random_user_agent ().'" "'.$url.'" -O "'.$output_path.'"';
  // DEBUG
  echo $p."\n";

  echo misc_exec ($p, $debug);
  return 0;
}


function
misc_seo_description ($html_body)
{
  // generate meta tag from the body
  $p = strip_tags2 ($html_body);
  $p = str_replace (array ('&nbsp;', '&gt;', '&lt;', "\n"), ' ', $p);
  $p = misc_get_keywords ($p, 1);
  return '<meta name="Description" content="'.$p.'">'
        .'<meta name="keywords" content="'.$p.'">';
}


function
misc_head_tags ($icon, $refresh = 0, $charset = 'UTF-8')
{
  $p = '';

  $p .= '<meta http-equiv="Content-Type" content="text/html;charset='.$charset.'">';
//  $p .= '<meta name="Content-Type" content="text/html; charset='.$charset.'">';

  if ($refresh > 0)
//    header ('location:'.$_SERVER['REQUEST_URI']);
    header ('refresh: '.$refresh.'; url='.$_SERVER['REQUEST_URI']);
//    $p .= '<meta http-equiv="refresh" content="'.$refresh.'; URL='.$_SERVER['REQUEST_URI'].'">';

/*
    <meta http-equiv="imagetoolbar" content="no">
    <meta http-equiv="reply-to" content="editor@NOSPAM.sniptools.com">
    <meta http-equiv="MSThemeCompatible" content="Yes">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Expires" content="Mon, 24 Sep 1976 12:43:30 IST">
*/

  if ($icon)
    $p .= '<link rel="icon" href="'.$icon.'" type="image/png">';

  return $p;
}


function
misc_head_rss ($title, $url)
{
  $p = '';
  $p .= '<link rel="alternate" type="application/rss+xml"'
       .' title="'.$title.'"'
       .' href="'.$url.'">';
  return $p;
}


/*
  split html into an array with content separated by tags
    returns an array

  $a['start']      // [...<head>]
  $a['head']       // <head>[...]</head>
  $a['between']    // [</head>...<body>]
  $a['body']       // <body>[...]</body>
  $a['end']        // [</body>...]EOF
*/
function
split_html_content ($html)
{
  $p = strtolower ($html);

  // split in head, body (and the rest)
  if (strpos ($p, '<head'))
    {
      $head_start = strpos ($p, '<head');
      $head_start += strpos (substr ($p, $head_start), '>') + 1;
      $head_len = strpos ($p, '</head>') - $head_start;
    }
  else
    {
      $head_start = 0;
      $head_len = 0;
    }

  if (strpos ($p, '<body'))
    {
      $body_start = strpos ($p, '<body');
      $body_start += strpos (substr ($p, $body_start), '>') + 1;
      $body_len = strpos ($p, '</body>') - $body_start;
    }
  else
    {
      $body_start = 0;
      $body_len = strlen ($html);
    }

  $start = substr ($html, 0, $head_start);
  $head = substr ($html, $head_start, $head_len);
  $between = substr ($html, $head_start + $head_len, $body_start - ($head_start + $head_len));

  $body = substr ($html, $body_start, $body_len);

  $end = substr ($html, $body_start + $body_len);

  $a = array ();

  $a['start'] = $start;
  $a['head'] = $head;
  $a['between'] = $between;
  $a['body'] = $body;
  $a['end'] = $end;

  // DEBUG
//print_r ($a);

  return $a;
}


function
simplexml_load_file2 ($xml_file)
{
  // wrapper that normalizes XML
  $p = file_get_contents ($xml_file);

  // HACK: remove unknown special chars
  $a = array ();
  for ($i = 0; $i < 30; $i++)
    $a[] = '&#'.$i.';';
  $p = str_replace ($a, '', $p);

  // re-read as XML
  $xml = simplexml_load_string ($p);

// DEBUG
//print_r ($xml);
//exit;
  return $xml;
}


// XML serializer
/*
function
array2xml_func (&$xml, $a)
{
  foreach ($a as $name=>$value)
    {
//      $name = preg_replace ("^[0-9]{1,}^", 'data', $name);
      $name = str_replace ('.', '_', $name);
 
      $xml .= '<'.$name.'>';

      if (is_array ($value))
        array2xml_func ($xml, $value);
      else
        {
//          if ($name == 'gq_name' || $name == 'nick' || $name == 'NGU')
//            $xml .= base64_encode ($value);
//          else
            $xml .= htmlspecialchars ($value, ENT_NOQUOTES, 'UTF-8');
        }

      $xml .= '</'.$name.'>'."\n";
    }
}


function 
array2xml ($a, $root_name = 'root')
{
  $xml = '';

//  if (is_array ($a))
//    if (count ($a) > 0)
      {
        array2xml_func ($xml, $a);
 
        return '<?xml version="1.0" encoding="utf-8"?>'."\n"
              .'<'.$root_name.'>'."\n"
              .$xml
              .'</'.$root_name.'>'."\n"
;
      }

  return '';
}
*/
 
}


?>