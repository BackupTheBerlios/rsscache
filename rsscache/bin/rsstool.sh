#!/bin/bash
#
# wrapper script for running RSStool
#
RSSTOOL_PATH="/usr/bin/torify /usr/local/bin/rsstool"
RSSTOOL_OPTS=""
RSSTOOL_OPTS+=" --hack-google --sbin --shtml"
#RSSTOOL_OPTS+=" --hack-event"
RSSTOOL_OPTS+=" -xml"


random_user_agent ()
{
  echo "Mozilla/5.0 (X11; Linux i686; rv:9.0a1) Gecko/20110919 Firefox/9.0a1 SeaMonkey/2.6a1"
}


# main ()


TMP=$(mktemp)


cd $(dirname ${0})


${RSSTOOL_PATH} -u "$(random_user_agent)" ${RSSTOOL_OPTS} $@ -o ${TMP} 2>&1 >rsstool.log


#cat ${TMP}
/usr/bin/xsltproc /htdocs/rsscache/bin/rsstool_xml2rss.xsl ${TMP}


rm -f ${TMP}


exit
