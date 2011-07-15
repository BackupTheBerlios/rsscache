<?php
/*
widget_media.php - new HTML widgets for media

Copyright (c) 2009 - 2010 NoisyB


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
if (!defined ('MISC_WIDGET_MEDIA_PHP'))
{
define ('MISC_WIDGET_MEDIA_PHP', 1);  
//error_reporting(E_ALL | E_STRICT);
include_once ('misc.php');
include_once ('widget.php');
include_once ('youtube.php');


function
has_webm() 
{
  // client does play webm
  if (stristr ($_SERVER['HTTP_USER_AGENT'], 'Firefox/4.'))
               // Mozilla/5.0 (X11; Linux i686; rv:2.0b8pre) Gecko/20101106 Firefox/4.0b8pre
    return true;
  return false;
}


function
playlist_parser ($playlist_s)
{
  // parse m3u, pls, xspf, wpl and return url array
  $a = array ();
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



  $fh = fopen ($filename, 'r');

  if (!$fh)
    return '';

  $demux = 0;
  $suffix = strtolower (get_suffix ($filename));
  if ($suffix == '.m3u')
    $demux = 1;
  if ($suffix == '.pls')
    $demux = 2;
  if ($suffix == '.xspf')
    $demux = 3;
  if ($suffix == '.wpl')
    $demux = 4;

  if ($demux == 1)
    {
  $count = 0;
  $a = array (array ());
  while (($p = fgets ($fh)))
    {
      $p = str_replace ("\n", '', $p);

      if ($p[0] != '#')
        continue;

      if (strstr ($p, '#EXTM3U'))
        $demux = 1;
      else if (strstr ($p, '#EXTINF:'))
        {
          $p = substr ($p, 8, -3);
          $a[$count]['title'] = trim (substr ($p, strpos ($p, ',') + 1));
          $a[$count]['link'] = str_replace ("\n", '', fgets ($fh));
          $a[$count++]['duration'] = trim (substr ($p, 0, strpos ($p, ',')));
        }
      else $a[$count++]['title'] = trim ($p, ' #-');
    }

  fclose ($fh);
    }
*/
  return $a;
}


function
widget_media_object_func ($object, $param, $embed)
{
  $p = '';

  if ($object)
    {
      $p .= '<object';
      for ($i = 0; isset ($object[$i]); $i++)
        $p .= ' '.$object[$i][0].'=\''.$object[$i][1].'\'';
//        $p .= ' '.$object[$i][0].'="'.$object[$i][1].'"';
      $p .= '>';
    }

  if ($param)
    {
      $param[] = array ('allowFullScreen', 'true');
      $param[] = array ('allowScriptAccess', 'always');
//      $param[] = array ('wmode', 'transparent');
      for ($i = 0; isset ($param[$i]); $i++)
        $p .= '<param name="'
             .$param[$i][0].'" value=\''.$param[$i][1].'\''
//             .$param[$i][0].'" value="'.$param[$i][1].'"'
             .'></param>';
    }
  if ($embed)
    {
      $embed[] = array ('allowFullScreen', 'true');      
      $embed[] = array ('allowScriptAccess', 'always');      
//      $embed[] = array ('wmode', 'transparent');      
      $p .= '<embed';
      for ($i = 0; isset ($embed[$i]); $i++)
        $p .= ' '.$embed[$i][0].'=\''.$embed[$i][1].'\'';
//        $p .= ' '.$embed[$i][0].'="'.$embed[$i][1].'"';
      $p .= '></embed>';
    }

  if ($object)
    $p .= '</object>';

  return $p;
}


function
widget_image_html4 ($image_url, $width = NULL, $height = NULL, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';

  $p .= '<img name="widget_image_html4_playlist" src="'.$image_url.'"'
       .($width ? ' width="'.$width.'"' : '')
       .($height ? ' height="'.$height.'"' : '')
       .($hq ? ' style="image-rendering:otimizeQuality"' : ' style="image-rendering:optimizeSpeed"')
       .' alt=""'
       .' border="0"'
//       .' style="background-color:#000;"'
            .' onerror="this.parentNode.removeChild(this);"'
       .'>';
  return $p;
//  return '';
}


/*
function
widget_audio_html4 ($audio_url, $width, $height, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';
  return $p;
}
*/


function
widget_audio_html5 ($audio_url, $width, $height, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';
  $p .= '<audio src="'.$audio_url.'"'
       .' controls="controls"'
       .($autoplay ? ' autoplay="autoplay"' : '')
       .($loop ? ' loop="loop"' : '')
       .' preload="meta">'
       // fallback to html4 (flash)
//       .widget_audio_html4 ($video_url, $width, $height, $autoplay, $hq, $loop)
       .'</audio>';
  return $p;
}


/*
function
widget_audio_youtube ($video_url, $width, $height, $autoplay = 0, $hq = 0, $loop = 0)
{
// show only the controls
  $p = '';
  return $p;
}
*/


function
widget_video_html4 ($video_url, $width = 400, $height = 300, $autoplay = 0, $hq = 0, $loop = 0)
{
  $url = $video_url;
//  if ($preview_image)
//    $url .= '&image='.$preview_image;

  // using flowplayer or jwplayer
  $o = array (
    array ('type', 'application/x-shockwave-flash'),
//    array ('data', 'misc/flowplayer.swf'),  // flowplayer
    array ('data', 'misc/jwplayer.swf'),   // jwplayer
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
//    array ('movie', 'misc/flowplayer.swf'), // flowplayer
    array ('movie', 'misc/jwplayer.swf'), // jwplayer
    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
//    array ('flashvars', 'config={"clip":"'.$url.'"}'), // flowplayer
    array ('flashvars', 'file='.$url),   // jwplayer
    array ('autostart', $autoplay ? 'true' : 'false'), // jwplayer
  );

  return widget_media_object_func ($o, $p, NULL);
}


function
widget_video_html5 ($video_url, $width = 400, $height = 300, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';
  $p .= '<video src="'.$video_url.'" width="'.$width.'" height="'.$height.'"'
       .' controls="controls"'
       .($autoplay ? ' autoplay="autoplay"' : '')
       .($loop ? ' loop="loop"' : '')
       .' preload="meta">'
       // fallback to html4 (flash)
//       .widget_video_html4 ($video_url, $width, $height, $autoplay, $hq, $loop)
       .'</video>'
//       .'<br>'
//       .'Video codec: <a href="http://www.webmproject.org/users/">WebM</a>'
;

  return $p;
}


function
widget_video_youtube ($video_url, $width = 425, $height = 344, $autoplay = 0, $hq = 0, $loop = 0)
{
  $video_url = youtube_get_videoid ($video_url);

//    'youtube' => array(
//        'url'=>'http://www.youtube.com/v/$1',
//        'default_width'=>425,
//        'default_ratio'=>425/350
//			# [youtube] By JockeTF.
//			# 16:9 By jack thompson (was 425x350)  new width = ((425*3/4)*16/9)
//                        $replacements[] = '<object width="565" height="350">
//<param name="movie" value="http://www.youtube.com/v/\\1&fmt=18">
//</param>
//<param name="wmode" value="transparent">
//</param>
//<embed src="http://www.youtube.com/v/\\1&fmt=18&fs=1" type="application/x-shockwave-flash" wmode="transparent" width="564" height="350">
//</embed>
//</object>';

//http://code.google.com/apis/youtube/player_parameters.html      
  $url = 'http://www.youtube.com/v/'
        .$video_url
       .'&fs=1'             // allow fullscreen
//       .'&rel=1'            // related
       .($autoplay ? '&autoplay=1' : '')
       .($loop ? '&loop=1' : '')
//       .'&color1=0x000000'
//       .'&color2=0x000000'
//       .'&start=30'         // skip to
//       .($hq ? '&hd=1' : '')  // high quality?
       .'&showinfo=0'
       .'&showsearch=0' // search
       .'&border=0'
;

/*
1. View high quality videos

Youtube gives you the option to switch to high quality videos for some of
the videos, however you can check if a video is available in high quality
format by appending &fmt=18#(stereo, 480 x 270 resolution) or
&fmt=22#(stereo, 1280 x 720 resolution) for even higher quality.

2. Embed Higher Quality Videos

While the above trick works for playback, if however you want to embed hig
quality videos you need to append "&ap=%2526fmt%3D18# and
"&ap=%2526fmt%3D22# to the embed url.

3. Cut the chase and link to the interesting part

Linking to a video where the real action starts at 3 minutes 22 seconds,
wondered if you could make it start at 03:22?  You are in luck.  All you
have to do is add #t=03m22s (#t=XXmYYs for XX mins and YY seconds) to the
end of the URL.

4. Hide the search box

youtube url start time

The search box appears when you hover over an embedded video. To hide the
search box add &showsearch=0# to the embed url.

5. Embed only a part of Video

youtube url to mp3

Just append &start=30# to skip first 30s of the video. In general you can
modify the value after start= to the number of seconds you want to skip the
video for.

6. Autoplay an embedded video

Normally when you embed a Youtube video and load the page, the player is
loaded and it sits there waiting for you to hit the play button.  You can
make the video play automatically by adding &autoplay=1# to the url part of
the embed code.

7. Loop an embedded video

Append &loop=1# to make the video start again without user intervention
after it reaches the end.

8. Disable Related Videos

youtube url downloader

Publishing your content in the form of Youtube video? Don't want people to
see other people's content that may be related but may as well be in
competition to you?  Just add &rel=0# to the end of the url part of the
embed code and you just turned off the related video suggestions!

9. Bypass Youtube Regional Filtering

Some videos are only available in certain parts of the world. Your IP
Address is used to determine your location and then allow or deny access to
the video.  Change the url from http://www.youtube.com/watch?v=<somecode> to
http://www.youtube.com/v/<somecode>
*/

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', $url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('autoplay', $autoplay ? 'true' : 'false'),
//    array ('wmode', 'transparent'),   
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('autoplay', $autoplay ? 'true' : 'false'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );
 
  return widget_media_object_func ($o, $p, $e);

  // iframe
//  return '<iframe class="youtube-player" type="text/html" width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$video_url
//        .($autoplay ? '&autoplay=1' : '')
//        .($loop ? '&loop=1' : '')
//        .'" frameborder="0"></iframe>';
}


function
widget_video_justintv ($video_url, $width=400, $height=300, $autoplay = 0, $hq = 0, $loop = 0)
{
/*
<object type="application/x-shockwave-flash" height="300" width="400" id="live_embed_player_flash" data="http://www.justin.tv/widgets/live_embed_player.swf?channel=cybersportsnetwork" bgcolor="#000000">
<param name="allowFullScreen" value="true" />
<param name="allowScriptAccess" value="always" />
<param name="allowNetworking" value="all" />
<param name="movie" value="http://www.justin.tv/widgets/live_embed_player.swf" />
<param name="flashvars" value="channel=cybersportsnetwork&auto_play=false&start_volume=25" />
</object>
*/
  $channel = 'cybersportsnetwork';
  $o = array (
    array ('type', 'application/x-shockwave-flash'),  
    array ('width', $width),  
    array ('height', $height),
    array ('id', 'live_embed_player_flash'),
    array ('data', 'http://www.justin.tv/widgets/live_embed_player.swf?channel='.$channel),
  );
  $p = array (
    array ('movie', $url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
      array ('allowNetworking', 'all'),
      array ('movie', 'http://www.justin.tv/widgets/live_embed_player.swf'),
      array ('flashvars', 'channel='.$channel.'&auto_play=false&start_volume=25'),
  );

  return widget_media_object_func ($o, $p, NULL);
}


function
widget_video_dailymotion ($video_url, $width=420, $height=336, $autoplay = 0, $hq = 0, $loop = 0)
{
  $video_url = substr ($video_url, strpos ($video_url, '/video/') + 7);
  $video_url = substr ($video_url, 0, strpos ($video_url, 'from') - 3);
  // $video_url = 'k4H0eU9uhV7waa1XXp';
  $url = 'http://www.dailymotion.com/swf/'.$video_url.'&related=1';

//    'dailymotion' => array(
//        'url' => 'http://www.dailymotion.com/swf/$1',
//        'default_width'=>425,
//        'default_ratio'=>425/350
  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', $url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );

  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_xvideos ($video_url, $width=510, $height=400, $autoplay = 0, $hq = 0, $loop = 0)
{
  // http://www.xvideos.com/video266837/dia_zerva_jordan_and_kenzi_marie
  $video_url = substr ($video_url, strpos ($video_url, '/video') + 6);
  $video_url = substr ($video_url, 0, strpos ($video_url, '/'));        

  $o = array (
    array ('width', $width),
    array ('height', $height),
    array ('classid', 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000'),
    array ('codebase', 'http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0'),
  );
  $p = array (
    array ('movie', 'http://static.xvideos.com/swf/flv_player_site_v4.swf'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('quality', 'high'),
    array ('bgcolor', '#000000'),
    array ('flashvars', 'id_video='.$video_url),
  );
  $e = array (
    array ('src', 'http://static.xvideos.com/swf/flv_player_site_v4.swf'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('menu', 'false'),
    array ('quality', 'high'),
    array ('bgcolor', '#000000'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('pluginspage', 'http://www.macromedia.com/go/getflashplayer'),
    array ('flashvars', 'id_video='.$video_url),
  );

  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_xxxbunker ($video_url, $width=550, $height=400, $autoplay = 0, $hq = 0, $loop = 0)
{
  //http://xxxbunker.com/1209498
  $video_url = substr ($video_url, strpos ($video_url, 'xxxbunker.com/') + 14);
  $url = 'http://xxxbunker.com/playerConfig.php?videoid='.$video_url.'&autoplay=false';
  $url = urlencode ($url);

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', 'http://xxxbunker.com/flash/player.swf'),
//    array ('wmode', 'transparent'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('flashvars', 'config='.$url),
  );
  $e = array (
    array ('src', 'http://xxxbunker.com/flash/player.swf'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('flashvars', 'config='.$url),  
//    array ('wmode', 'transparent'),
//    array ('allowFullScreen', 'true'),  
//    array ('allowScriptAccess', 'always'),
  );
 
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_tnaflix ($video_url, $width=650, $height=515, $autoplay = 0, $hq = 0, $loop = 0)
{
  // http://www.tnaflix.com/view_video.php?viewkey=e6f818fd95b6313e2c28
  $video_url = substr ($video_url, strpos ($video_url, 'viewkey=') + 8);
  $url = 'config=embedding_feed.php?viewkey='.$video_url;

  $o = array (
    array ('width', $width),
    array ('height', $height),
    array ('type', 'application/x-shockwave-flash'),
    array ('data', 'http://www.tnaflix.com/embedding_player/player_v0.2.1.swf')
  );
  $p = array (
    array ('movie', 'http://www.tnaflix.com//embedding_player/player_v0.2.1.swf'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('FlashVars', 'value='.$url),  
  );
    
  return widget_media_object_func ($o, $p, NULL);
}


function
widget_video_xfire ($video_url, $width=425, $height=279, $autoplay = 0, $hq = 0, $loop = 0)
{
  $video_url = substr ($video_url, strpos ($video_url, '/video/') + 7, -1);
  $url = 'http://media.xfire.com/swf/embedplayer.swf';

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('flashvars', 'videoid='.$video_url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );

  return widget_media_object_func ($o, NULL, $e);
}


function
widget_video_myspace ($video_url, $width=425, $height=360, $autoplay = 0, $hq = 0, $loop = 0)
{
//  $video_url = 'k4H0eU9uhV7waa1XXp';
  $video_url = '6773592';
  $url = 'http://mediaservices.myspace.com/services/media/embed.aspx/m='.$video_url.',t=1,mt=video';

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', $url),
//    array ('wmode', 'transparent'),   
//    array ('allowFullScreen', 'true'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('wmode', 'transparent'),
//    array ('allowFullScreen', 'true'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_veoh ($video_url, $width=410, $height=341, $autoplay = 0, $hq = 0, $loop = 0)
{
  $url = substr ($video_url, strrpos ($video_url, '/') + 1);
  $url = 'http://www.veoh.com/static/swf/webplayer/WebPlayer.swf?version=AFrontend.5.5.3.1011&permalinkId='
         .$url
         .'&player=videodetailsembedded&videoAutoPlay=0&id=anonymous';

  $o = array (
    array ('width', $width),
    array ('height', $height),
    array ('id', 'veohFlashPlayer'),
    array ('name', 'veohFlashPlayer'),
  );
  $p = array (
    array ('movie', $url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('id', 'veohFlashPlayerEmbed'),
    array ('name', 'veohFlashPlayerEmbed'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_google ($video_url, $width=400, $height=326, $autoplay = 0, $hq = 0, $loop = 0)
{
  $url = 'http://video.google.com/googleplayer.swf?docid='.$video_url.'&fs=true';

//    'googlevideo' => array(
//        'id_pattern'=>'%[^0-9\\-]%',
//        'url' => 'http://video.google.com/googleplayer.swf?docId=$1',
//        'default_width'=>425,
//        'default_ratio'=>425/350
//                        # [googlevideo] By JockeTF.
//<embed style="width:400px; height:326px;" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId=\\1&hl=en" flashvars=""> </embed>';

  // original: 400x326
  $e = array (
    array ('id', 'VideoPlayback'),
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('style', 'width:'.$width.'px;height:'.$height.'px;'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );
 
  return widget_media_object_func (NULL, NULL, $e);
}


function
widget_video_yahoo ($video_url, $width=512, $height=322, $autoplay = 0, $hq = 0, $loop = 0)
{
// vid id
//http://espanol.video.yahoo.com/watch/5410123/14251443
//  $video_url = 'k4H0eU9uhV7waa1XXp';
  $video_url = '6773592';
  $video_vid = '6773592';
  $url = 'http://mediaservices.myspace.com/services/media/embed.aspx/m='.$video_url.',t=1,mt=video';

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', 'http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('bgcolor', '#000000'),
    array ('flashvars', 'id='.$id.'&vid='.$vid.'&lang=es-mx&intl=e1&thumbUrl=http%3A//l.yimg.com/a/p/i/bcst/videosearch/9707/88446579.jpeg&embed=1'),
  );
  $e = array (
    array ('src', 'http://d.yimg.com/static.video.yahoo.com/yep/YV_YEP.swf?ver=2.2.46'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('bgcolor', '#000000'),
    array ('flashvars', 'id='.$id.'&vid='.$vid.'&lang=es-mx&intl=e1&thumbUrl=http%3A//l.yimg.com/a/p/i/bcst/videosearch/9707/88446579.jpeg&embed=1'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_own3d ($video_url, $width=640, $height=360, $autoplay = 0, $hq = 0, $loop = 0)
{
  //http://www.own3d.tv/video/25617
  //http://www.own3d.tv/stream/25617
  $url = str_replace ('watch', 'stream', $video_url);

  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
    array ('movie', $url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
//    array ('wmode', 'transparent'),
  );
  $e = array (
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
//    array ('wmode', 'transparent'),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_archive ($video_url, $width=640, $height=506, $autoplay = 0, $hq = 0, $loop = 0)
{
  $o = array (
    array ('width', $width),
    array ('height', $height),
//    array ('classid', 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'),
  );
  $p = array (
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('quality', 'high'),           
    array ('cachebusting', 'true'),
//    array ('bgcolor', '#000000'),
    array ('movie', 'http://www.archive.org/flow/flowplayer.commercial-3.2.1.swf'),
    array ('flashvars', "config={'key':'#$aa4baff94a9bdcafce8','playlist':['format=Thumbnail?.jpg',{'autoPlay':false,'url':'Consolevania-03x25677-3-The_Black_Episode_512kb.mp4'}],'clip':{'autoPlay':true,'baseUrl':'http://www.archive.org/download/Consolevania-03x25677-3-The_Black_Episode/','scaling':'fit','provider':'h264streaming'},'canvas':{'backgroundColor':'#000000','backgroundGradient':'none'},'plugins':{'controls':{'playlist':false,'fullscreen':true,'height':26,'backgroundColor':'#000000','autoHide':{'fullscreenOnly':true}},'h264streaming':{'url':'http://www.archive.org/flow/flowplayer.pseudostreaming-3.2.1.swf'}},'contextMenu':[{},'-','Flowplayer v3.2.1']}"),
  );
  $e = array (
    array ('src', 'http://www.archive.org/flow/flowplayer.commercial-3.2.1.swf'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('quality', 'high'),
    array ('cachebusting', 'true'),
//    array ('bgcolor', '#000000'),
    array ('flashvars', "config={'key':'#$aa4baff94a9bdcafce8','playlist':['format=Thumbnail?.jpg',{'autoPlay':false,'url':'Consolevania-03x25677-3-The_Black_Episode_512kb.mp4'}],'clip':{'autoPlay':true,'baseUrl':'http://www.archive.org/download/Consolevania-03x25677-3-The_Black_Episode/','scaling':'fit','provider':'h264streaming'},'canvas':{'backgroundColor':'#000000','backgroundGradient':'none'},'plugins':{'controls':{'playlist':false,'fullscreen':true,'height':26,'backgroundColor':'#000000','autoHide':{'fullscreenOnly':true}},'h264streaming':{'url':'http://www.archive.org/flow/flowplayer.pseudostreaming-3.2.1.swf'}},'contextMenu':[{},'-','Flowplayer v3.2.1']}"),
  );
  return widget_media_object_func ($o, $p, $e);
}


function
widget_video_liveleak ($video_url, $width=450, $height=370, $autoplay = 0, $hq = 0, $loop = 0)
{
  $o = array (
    array ('width', $width),
    array ('height', $height),
  );
  $p = array (
//    array ('wmode', 'transparent'),
//    array ('allowScriptAccess', 'always'),  
    array ('movie', 'http://www.liveleak.com/e/06c_1287260062'), 
  );
  $e = array (
    array ('src', 'http://www.liveleak.com/e/06c_1287260062'),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
//    array ('allowScriptAccess', 'always'),
//    array ('wmode', 'transparent'),
  );
  return widget_media_object_func ($o, $p, $e);
}

/*

// metacafe
<embed src="http://www.metacafe.com/fplayer/\\1.swf"
width="400"
height="345"
wmode="transparent"
pluginspage="http://www.macromedia.com/go/getflashplayer"
type="application/x-shockwave-flash"> </embed>';

// metacafe 2
<embed flashVars="playerVars=showStats=yes|autoPlay=no|videoTitle=Study: Indoor Marijuana Cultivation Bad for the Environment"
src="http://www.metacafe.com/fplayer/6274472/study_indoor_marijuana_cultivation_bad_for_the_environment.swf"
width="440"
height="272"
wmode="transparent"
allowFullScreen="true"
allowScriptAccess="always"
name="Metacafe_6274472"
pluginspage="http://www.macromedia.com/go/getflashplayer"
type="application/x-shockwave-flash">
</embed>

// divshare
    'divshare' => array(
        'url' => 'http://www.divshare.com/flash/video2?myId=$1',
        'default_width'=>425,
        'default_ratio'=>425/350

// revver
    'revver' => array(
        'url' => 'http://flash.revver.com/player/1.0/player.swf?mediaId=$1',
        'default_width'=>425,
        'default_ratio'=>425/350

// MLG
http://tv.majorleaguegaming.com/video/community-video/830444301001-halo-3-montage-finals-2nd
http://tv.majorleaguegaming.com/gameplay/community-video.rss?q=&tags[game-title]=Halo%3A+Combat+Evolved
http://tv.majorleaguegaming.com/gameplay/mlg-video.rss?q=&tags[game-title]=Halo+3


<object id="flashObj" width="486" height="412" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,47,0">
<param name="movie" value="http://c.brightcove.com/services/viewer/federated_f9?isVid=1" />
<param name="bgcolor" value="#FFFFFF" />
<param name="flashVars" value="videoId=830444301001&linkBaseURL=http%3A%2F%2Ftv.majorleaguegaming.com%2Fvideo%2Fcommunity-video%2F830444301001-halo-3-montage-finals-2nd&playerID=63791254001&playerKey=AQ~~,AAAADtnNG8E~,Ya7HVG73U426MUvIr82Cx3rrc1nz-6TJ&domain=embed&dynamicStreaming=true" />
<param name="base" value="http://admin.brightcove.com" />
<param name="seamlesstabbing" value="false" />
<param name="allowFullScreen" value="true" />
<param name="swLiveConnect" value="true" />
<param name="allowScriptAccess" value="always" />
<embed src="http://c.brightcove.com/services/viewer/federated_f9?isVid=1" bgcolor="#FFFFFF" flashVars="videoId=830444301001&linkBaseURL=http%3A%2F%2Ftv.majorleaguegaming.com%2Fvideo%2Fcommunity-video%2F830444301001-halo-3-montage-finals-2nd&playerID=63791254001&playerKey=AQ~~,AAAADtnNG8E~,Ya7HVG73U426MUvIr82Cx3rrc1nz-6TJ&domain=embed&dynamicStreaming=true" base="http://admin.brightcove.com" name="flashObj" width="486" height="412" seamlesstabbing="false" type="application/x-shockwave-flash" allowFullScreen="true" swLiveConnect="true" allowScriptAccess="always" pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
</embed>
</object>


// revver 2 
<embed type="application/x-shockwave-flash"
src="http://flash.revver.com/player/1.0/player.swf"
pluginspage="http://www.macromedia.com/go/getflashplayer"
scale="noScale"
salign="TL"
bgcolor="#000000"
flashvars="mediaId=\\1&affiliateId=0&allowFullScreen=true"
allowfullscreen="true"
height="392"
width="480"></embed>

// sevenload
    'sevenload' => array(
        'url' => 'http://page.sevenload.com/swf/en_GB/player.swf?id=$1',
        'default_width'=>425,
        'default_ratio'=>425/350

// youtubehd
    'youtubehd' => array(
        'url' => 'http://www.youtube.com/v/$1&ap=%2526fmt%3D22',
        'default_width' => 720,
        'default_ratio' => 16/9

// vimeo
    'vimeo' => array(
        'url'=>'http://vimeo.com/moogaloop.swf?clip_id=$1&;server=vimeo.com&fullscreen=0&show_title=1&show_byline=1&show_portrait=0'


// ustream 
<object width="480" height="386" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">
  <param name="flashvars" value="cid=4738711&autoplay=false"/>
  <param name="allowfullscreen" value="true"/>
  <param name="allowscriptaccess" value="always"/>
  <param name="src" value="http://www.ustream.tv/flash/viewer.swf"/>
  <embed flashvars="cid=4738711&autoplay=false" width="480" height="386" allowfullscreen="true" allowscriptaccess="always" src="http://www.ustream.tv/flash/viewer.swf" type="application/x-shockwave-flash">
</embed>
</object>

// blip tv
<embed src="http://blip.tv/play/AYLB6yAC"
type="application/x-shockwave-flash"
width="480"
height="300"
wmode="transparent"
allowscriptaccess="always"
allowfullscreen="true" >
</embed>

*/



function
widget_media_demux ($media_url)
{
  $s = strtolower ($media_url);

  if (strstr ($s, '.youtube.com'))
    return 1;
  else if (strstr ($s, 'dailymotion.'))
    return 2;
  else if (strstr ($s, 'xfire.com'))
    return 3;
  else if (in_array (get_suffix ($s), array ('.flv', '.mp4', '.mp3')))
    return 4; // jwplayer or flowplayer
  else if (in_array (get_suffix ($s), array ('.webm', '.ogg')))
    return 5; // <video>
  else if (in_array (get_suffix ($s), array ('.weba', '.wav')))
    return 6; // <audio>
  else if (strstr ($s, 'veoh.com'))
    return 7;
  else if (strstr ($s, 'xvideos.com'))
    return 8;
  else if (strstr ($s, 'xxxbunker.com'))
    return 9;
  else if (strstr ($s, 'video.google'))
    return 10;
  else if (strstr ($s, 'tnaflix.com'))
    return 11;
  else if (strstr ($s, 'own3d.tv'))
    return 12;
  else if (strstr ($s, 'archive.org'))
    return 13;
  else if (in_array (get_suffix ($s), array ('.jpg', '.png', '.webp', '.gif')))
    return 14; // <img>
  else if (strstr ($s, 'liveleak.com'))
    return 15;
  else if (strstr ($s, 'justin.tv'))
    return 16;
/*
  else if (strstr ($s, 'metacafe.com'))
    return 17;
  else if (strstr ($s, 'divshare.com'))
    return 18;
  else if (strstr ($s, 'revver.com'))
    return 19;
  else if (strstr ($s, 'sevenload.com'))
    return 20;
  else if (strstr ($s, 'vimeo.com'))
    return 21;
  else if (strstr ($s, 'ustream.tv'))
    return 22;
  else if (strstr ($s, 'blip.tv'))
    return 23;
*/
  return 0; // not supported
}


function
widget_media_demux_func ($media_url)
{
  $demux = widget_media_demux ($media_url);
  $a = array (
         'widget_video_youtube',
         'widget_video_dailymotion',
         'widget_video_xfire',
         'widget_video_html4',  
         'widget_video_html5',    

         'widget_audio_html5',    
         'widget_video_veoh',     
         'widget_video_xvideos',  
         'widget_video_xxxbunker',
         'widget_video_google', 

         'widget_video_tnaflix',
         'widget_video_own3d',  
         'widget_video_archive',
         'widget_image_html4',
         'widget_video_liveleak',

         'widget_video_justintv',
/*
         'widget_video_metacafe',
         'widget_video_divshare',  
         'widget_video_revver',
         'widget_video_sevenload',

         'widget_video_vimeo',
         'widget_video_ustreamtv',  
         'widget_video_bliptv',
*/
);
  if ($demux > 0)
    if (isset ($a[$demux - 1])) 
      return $a[$demux - 1];
  return NULL;
}


function
widget_media_embed_code ($media_url)
{
  $func = widget_media_demux_func ($media_url);
  $p = '';

  if ($func)
    {
      $c = $func ($media_url);
      $p .= '<input style="background-color:#fff;" type="text" readonly="readonly" value="'.htmlentities ($c).'">';
    }
  return $p;
}


function
widget_video_youtube_download ($media_url, $tor_enabled)
{
  $p = '';

  $yt_array = youtube_download ($media_url, $tor_enabled, 0);

  // DEBUG
//  echo '<pre><tt>';
//  echo $media_url."\n";
//  print_r ($a); 

  $yt = $yt_array[0];

  if ($yt['status'] == 'fail') // youtube fail
    {
      $p .= $yt['errorcode'].': '.$yt['reason'];
 
      switch ($yt['errorcode'])
        {
          case 150: // copyright
            $p .= '<br>'
                 .' Probably Naziwalled against access from your country<br>'
                 .'Try a proxy or service that is located in the country of the possible license owner'
;
            break;

          case 100: // removed by user
          default:
            break;
        }
      return $p;
    }

//  [fmt_list] => 35/854x480/9/0/115,34/640x360/9/0/115,18/640x360/9/0/115,5/320x240/7/0/0
  $a = explode (',', $yt['fmt_list']);

//  $p .= '<br>';

  // download
//  if (islocalhost ())
    {
  $p .= 'Download: <a href="'.$yt['video_url'].'">Best</a>';

  for ($q = 0; isset ($yt[$q]); $q++)
    {
      $b = explode ('/', $a[$q]);
      $fmt = substr ($yt[$q], 0, strpos ($yt[$q], '|'));
      $t = substr ($yt[$q], strpos ($yt[$q], '|') + 1);
      $p .= ' <a href="'.$t.'" title="&fmt='.$fmt.'">'.$b[1].'</a>';
    }

  // direct link
//  $p .= ' <a href="'.$yt['ad_eurl'].'">Direct</a>';

  $p .= '<br>';
    }

  $p .= ''
       .'Name: <input type="text"'
       .' value="'
       .$yt['title']
       .'" readonly="readonly"'
//       .' style="width:'.($rescue_w - 50).'px;"'
       .'>'
;
  $p .= '<br>';

  // details
  $p .= widget_collapse ('Details', '<!-- div style="width:200px;height:100px;overflow:auto;" --><pre><tt>'.sprint_r ($yt).'</tt></pre><!-- /div -->', 1);

  return $p;
}


function
widget_media_download ($media_url, $tor_enabled = 0)
{
  $func = widget_media_demux_func ($media_url).'_download';

  $p = '';
  if (function_exists ($func))
    $p .= $func ($media_url, $tor_enabled);

  return $p;
}


function
widget_video_youtubereloaded_playlist ($media_urls, $width = 470, $height = 470, $ratio = NULL, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';
/*
<script type="text/javascript" src="http://www.youtubereloaded.com/embed/swfobject.js"></script>
<div id="YouTubeReloadedPlayer">This div will be replaced</div>

<script type="text/javascript">
var s1 = new SWFObject('http://www.youtubereloaded.com/embed/player.swf','ply','470','470','9','#');
s1.addParam('allowfullscreen','true');
s1.addParam('allowscriptaccess','always');
s1.addParam('wmode','opaque');
s1.addParam('flashvars','file=http%3A%2F%2Fgdata.youtube.com%2Ffeeds%2Fapi%2Fvideos%3Fvq%3Dcaptain+future%26max-results%3D10&playlist=bottom&frontcolor=cccccc&lightcolor=66cc00&skin=http://www.youtubereloaded.com/embed/skin1.swf&backcolor=111111&playlistsize=200');
s1.write('YouTubeReloadedPlayer');
</script>
*/
  return $p;
}


function
widget_video_youtube_playlist ($video_urls, $width = 425, $height = 344, $ratio = NULL, $autoplay = 0, $hq = 0, $loop = 0)
{
  $p = '';

//<script src="http://www.google.com/jsapi"></script>
$p .= '
<script type="text/javascript">


function widget_video_youtube_next ()
{
  var e = document.getElementById (\'widget_video_youtube\');
//  var e = document.getElementById (\'ytplayer\');
//  var e = ytplayer;

  var debug = document.getElementById (\'debug\');
  debug.innerHTML = e.getPlayerState ()+\'\';

  if (e.getPlayerState () == 1) // video is still playing
    return;

  if (typeof this.pos == \'undefined\')
    this.pos = 0;

  var a = new Array (';

  for ($i = 0; isset ($video_urls[$i]); $i++)
    {
      if ($i > 0)
        $p .= ', ';
      $p .= '\''.youtube_get_videoid ($video_urls[$i]).'\'';
    }

  $p .= '
);

  // restart
//  if (this.pos == a.length)
//    this.pos = 0;

  e.loadVideoById (a[this.pos++], 0);
  e.playVideo ();
//  e.loadVideoById (\'Nh6siIPTN3o\', 0);
//  e.playVideo ();
//  e.loadVideoById (\'R1Z7plEWGgQ\', 0);
//  e.playVideo ();
}


window.onload = function ()
//function widget_video_youtube_start()
{
  setInterval (\'widget_video_youtube_next()\', 500);
}

</script>
<div id="debug"></div>
';
$script = $p;

//http://code.google.com/apis/youtube/player_parameters.html      
//  $url = 'http://www.youtube.com/v/'
//        .$video_url
  $url = 'http://www.youtube.com/apiplayer?enablejsapi=1'
       .'&fs=1'             // allow fullscreen
//       .'&rel=1'            // related
//       .($autoplay ? '&autoplay=1' : '')
//       .($loop ? '&loop=1' : '')
//       .'&color1=0x000000'
//       .'&color2=0x000000'
//       .'&start=30'         // skip to
//       .($hq ? '&hd=1' : '')  // high quality?
       .'&showinfo=0'
       .'&showsearch=0' // search
       .'&border=0'
;

  $o = array (
    array ('width', $width),
    array ('height', $height),
//    array ('onload', 'widget_video_youtube_start()'), // start playlist
  );
  $p = array (
    array ('movie', $url),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
    array ('autoplay', $autoplay ? 'true' : 'false'),
//    array ('wmode', 'transparent'),   
  );
  $e = array (
    array ('id', 'widget_video_youtube'),
    array ('src', $url),
    array ('type', 'application/x-shockwave-flash'),
    array ('width', $width),
    array ('height', $height),
    array ('autoplay', $autoplay ? 'true' : 'false'),
//    array ('allowFullScreen', 'true'),
//    array ('allowScriptAccess', 'always'),
  );
 
  return $script.widget_media_object_func ($o, $p, $e);
}


function
widget_media_playlist ($media_urls, $width = NULL, $height = NULL, $ratio = NULL, $autoplay = 0, $hq = 0, $loop = 0, $blackbg = 0)
{
  $p = '';
  $a = array ();
  for ($i = 0; isset ($media_urls[$i]) && $i < 10; $i++)
    {
      $demux = widget_media_demux ($media_urls[$i]);
      if ($demux == 1)
        $a[] = $media_urls[$i];
    }

//print_r ($a);

  if (count ($a) > 0)
    $p .= widget_video_youtube_playlist ($a, $width, $height, $ratio, $autoplay, $hq, $loop);

  return $p;
}


function
widget_media ($media_url, $width = NULL, $height = NULL, $ratio = NULL, $autoplay = 0, $hq = 0, $loop = 0, $blackbg = 0)
{
  // array
  if (gettype ($media_url) == 'array')
    return widget_media_playlist ($media_url, $width, $height, $ratio, $autoplay, $hq, $loop, $blackbg);

  $func = widget_media_demux_func ($media_url);
  $p = '';

  $scale = 0;

  if ($width == -1 || $height == -1)
    $scale = 1;

  if ($scale)
    {
//      $bg_width = '\'+Math.floor (misc_getwh ()[0])+\''; // width of black background
//      if ($ratio)
//        $width = '\'+Math.floor (misc_getwh ()[1] * '.$ratio.')+\'';
//      else
//        $width = $bg_width;
//      $height = '\'+Math.floor (misc_getwh ()[1])+\'';
      $bg_width = '100%';
      $width = '100%';
      $height = '100%';
//      $p .= ''
//           .'<script type="text/javascript">'."\n"
//           .'document.write (\'';
    } 
  else
    {
      $bg_width = floor ($width); // width of black background
      if ($ratio)
        $width = floor ($height * $ratio);
      else
        $width = $bg_width;
      $height = floor ($height);
    }

  if ($func)
      {
        $c = $func ($media_url, $width, $height, $autoplay, $hq, $loop);
//        if ($blackbg == 1)
          $p .= ''
               .'<div width="'.$bg_width.'" height="'.$height.'" style="background-color:#'.($blackbg ? '000' : 'fff').';text-align:center;">'
               .$c
               .'</div>'
;
//        else
//          $p .= $c;
      }

  if ($scale)
    {   
//      $p .= '\');'."\n\n"
//           .'</script>'
//;
    }

  return $p;
}


}

?>