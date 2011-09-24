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
# http://www.user-agents.org/allagents.xml
#
# The script searches for a type “B” in the xml document, which stands for
# Browser, if you want to include other types, write the letter in place of,
# or with one space seperated from “B”.  It’s possible to specify more
# than 2 types.
#
# B = Browser
# C = Link-, bookmark-, server- checking D = Downloading tool
# P = Proxy server, web filtering
# R = Robot, crawler, spider
# S = Spam or bad bot
user_agents=$(cat ./allagents.xml | grep -i -B 5 '<type>B</type>' | grep -i '<string>' | cut -c 9- | sed 's/..........$//')
#echo $user_agents
lines=$(echo "$user_agents" | wc -l | tr -d ' ')
user_agent=$(echo "$user_agents" | sed -n $[ ( $RANDOM % ( $[ $lines - 1 ] + 1 ) ) + 1 ]p)
 
echo $user_agent
#  echo "Mozilla/5.0 (X11; Linux i686; rv:9.0a1) Gecko/20110919 Firefox/9.0a1 SeaMonkey/2.6a1"
}


# main ()
cd $(dirname ${0})
#/usr/local/bin/rsstool --hack-google --sbin --shtml -xml 2>/dev/null
echo ${RSSTOOL_PATH} -u "$(random_user_agent)" ${RSSTOOL_OPTS} $@ 2>/dev/null


exit
