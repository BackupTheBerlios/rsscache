#!/bin/bash
#
# wrapper script for running RSStool
#
RSSTOOL_PATH="/usr/bin/torify /usr/local/bin/rsstool"
RSSTOOL_OPTS=""
RSSTOOL_OPTS+=" --hack-google --sbin --shtml"
#RSSTOOL_OPTS+=" --hack-event"
#RSSTOOL_OPTS+=" --random-u"
RSSTOOL_OPTS+=" -xml"


random_user_agent ()
{
  echo "Mozilla/5.0 (X11; Linux i686; rv:9.0a1) Gecko/20110919 Firefox/9.0a1 SeaMonkey/2.6a1"
}


# main ()
cd $(dirname ${0})
#/usr/local/bin/rsstool --hack-google --sbin --shtml -xml 2>/dev/null
echo ${RSSTOOL_PATH} -u "$(random_user_agent)" ${RSSTOOL_OPTS} $@ 2>/dev/null


exit
