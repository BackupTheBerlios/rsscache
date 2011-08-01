#!/bin/bash
#
# rsscache_upload.sh - upload/mirror database contents TO different server
#
# Copyright (c) 2009 - 2011 NoisyB
#
#
# Usage: rsscache_upload.sh SUBDOMAIN
#
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or   
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of 
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.  


SQLPASSWD=nb
SQLREMOTEPW=pwn44553
SUBDOMAIN="${1}"
#export SUBDOMAIN
HOSTNAME=$(hostname)
#export HOSTNAME
FILEDATE=$(date +"%Y%m%d_%H%M%S")
#export FILEDATE
LOCAL_SQLFILE=pwnoogle_${SUBDOMAIN}_upload_${HOSTNAME}.sql
#SQLFILE=pwnoogle_${SUBDOMAIN}_upload_${HOSTNAME}_${FILEDATE}.sql
SQLFILE=pwnoogle_${SUBDOMAIN}_upload_${HOSTNAME}.sql


cd $(dirname $0)


# mysqldump
MYSQL_OPTS=""
#MYSQL_OPTS=" --default-character-set=latin1"
MYSQL_OPTS+=" --skip-add-drop-table" 
MYSQL_OPTS+=" --no-create-info" 
MYSQL_OPTS+=" --insert-ignore"  
MYSQL_OPTS+=" --delayed-insert"
#case ${SUBDOMAIN} in
#  "live")
# replace
#MYSQL_OPTS+=" --replace"
#    ;;
#
#  *)
# append
#MYSQL_OPTS+=" --insert-ignore"  
#    ;;
#esac
#MYSQL_OPTS+=" -P" 
echo "$(date): dumping ${LOCAL_SQLFILE}"
mysqldump ${MYSQL_OPTS} -p${SQLPASSWD} pwnoogle_${SUBDOMAIN} >${LOCAL_SQLFILE}
echo "$(date): done"


# rsync
RSYNC_OPTS=""
if [ ${HOSTNAME} == "debian2" ]; then
RSYNC_OPTS+=" --bwlimit=50" 
fi
RSYNC_OPTS+=" --timeout=300"
RSYNC_OPTS+=" --compress-level=9"
RSYNC_OPTS+=" --times"
#export RSYNC_OPTS


# thumbnails
LAST_EXIT_CODE=1

while :
do
echo "$(date): uploading thumbnails"
rsync ${RSYNC_OPTS} -rav --numeric-ids ../htdocs/thumbnails/rsscache pwnoogle@pwnoogle.com:/home/pwnoogle/htdocs/${SUBDOMAIN}/htdocs/thumbnails/

  LAST_EXIT_CODE=$?
  if [ $LAST_EXIT_CODE -eq 0 ]; then
# delete thumbnails
#rm -f ../htdocs/thumbnails/rsscache/*
    break
  fi
done


# sql
LAST_EXIT_CODE=1

while :
do
echo "$(date): uploading SQL"
rsync ${RSYNC_OPTS} -rav --numeric-ids ./${LOCAL_SQLFILE} pwnoogle@pwnoogle.com:/home/pwnoogle/${SQLFILE}

  LAST_EXIT_CODE=$?
  if [ $LAST_EXIT_CODE -eq 0 ]; then
# empty tables
if [ $(hostname) != "debian2" ]; then
echo

for T in $(mysql -p${SQLPASSWD} pwnoogle_${SUBDOMAIN} -e 'show tables' | awk '{ print $1}' | grep -v '^Tables'); do
  echo "truncate ${T}" | mysql -p${SQLPASSWD} pwnoogle_${SUBDOMAIN}
done

fi
    break
  fi
done


echo "$(date): running SQL"
#expect -f rsscache_upload.expect
expect << EOF
set timeout  -1

spawn ssh pwnoogle@pwnoogle.com

#expect "*?assword:*"
#send "password\r"

expect "*jailshell*"
send "date\r"

expect "*jailshell*"
send "echo >/home/pwnoogle/htdocs/${SUBDOMAIN}/htdocs/maintenance_${SUBDOMAIN}.tmp\r"

expect "*jailshell*"
#send "cat ${SQLFILE} | sed 's|/*!40101 SET NAMES latin1 */;|/*!40101 SET NAMES utf8 */;|g' | mysql -u pwnoogle_db -p pwnoogle_${SUBDOMAIN}\r"
send "cat ${SQLFILE} | nice -n 19 mysql -u pwnoogle_db -p pwnoogle_${SUBDOMAIN}\r"

expect "*?assword:*"
send "${SQLREMOTEPW}\r"

expect "*jailshell*"
send "rm /home/pwnoogle/htdocs/${SUBDOMAIN}/htdocs/maintenance_${SUBDOMAIN}.tmp\r"

expect "*jailshell*"
send "echo done\r"

expect "*jailshell*"
send "exit\r"
EOF


echo




exit
