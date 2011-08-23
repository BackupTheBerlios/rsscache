#!/bin/bash
#
#


cd "$(dirname "${0}")"

./rsscache_cronjob.php ../htdocs/videos_config.xml                             
./rsscache_cronjob.php ../htdocs/emulive_config.xml


exit
