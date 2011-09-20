#!/bin/bash
#
# RSScache wrapper script for running RSStool
#
TMP=$(mktemp)


random_user_agent ()
{
  echo "Mozilla/5.0 (X11; Linux i686; rv:9.0a1) Gecko/20110919 Firefox/9.0a1 SeaMonkey/2.6a1"
}


cd $(dirname ${0})


/usr/local/bin/rsstool -u "$(random_user_agent)" -xml $@ -o ${TMP} 2>&1 >rsstool.log


#cat ${TMP}
/usr/bin/xsltproc rsstool_xml2rss.xsl ${TMP}


rm -f ${TMP}


exit
