<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms">
<xsl:output method="xml" indent="yes"/>


<xsl:template match="/"><mediawiki xmlns="http://www.mediawiki.org/xml/export-0.4/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
 xsi:schemaLocation="http://www.mediawiki.org/xml/export-0.4/ http://www.mediawiki.org/xml/export-0.4.xsd"
 version="0.4" xml:lang="en">
  <siteinfo>
    <sitename><xsl:value-of disable-output-escaping="yes" select="rss/channel/title"/></sitename>
    <!-- base>http://localhost/index.php/Main_Page</base -->
    <generator>RSScache</generator>
    <!-- case>first-letter</case -->
    <!-- namespaces>
      <namespace key="-2" case="first-letter">Media</namespace>
      <namespace key="-1" case="first-letter">Special</namespace>
      <namespace key="0" case="first-letter" />
      <namespace key="1" case="first-letter">Talk</namespace>
      <namespace key="2" case="first-letter">User</namespace>
      <namespace key="3" case="first-letter">User talk</namespace>
      <namespace key="4" case="first-letter">Hiddenwiki</namespace>
      <namespace key="5" case="first-letter">Hiddenwiki talk</namespace>
      <namespace key="6" case="first-letter">File</namespace>
      <namespace key="7" case="first-letter">File talk</namespace>
      <namespace key="8" case="first-letter">MediaWiki</namespace>
      <namespace key="9" case="first-letter">MediaWiki talk</namespace>
      <namespace key="10" case="first-letter">Template</namespace>
      <namespace key="11" case="first-letter">Template talk</namespace>
      <namespace key="12" case="first-letter">Help</namespace>
      <namespace key="13" case="first-letter">Help talk</namespace>
      <namespace key="14" case="first-letter">Category</namespace>
      <namespace key="15" case="first-letter">Category talk</namespace>
    </namespaces -->
  </siteinfo>

<xsl:for-each select="rss/channel/item">
  <page>
    <title><xsl:value-of disable-output-escaping="yes" select="title"/></title>
    <id><xsl:value-of disable-output-escaping="yes" select="pubDate"/></id>
    <revision>
      <id>($rsscache_time + $i + 1)</id>
      <!-- timestamp>'.strftime ("%Y-%m-%dT%H:%M:%SZ", $item[$i]['pubDate']).'</timestamp -->
      <!-- timestamp>'.strftime ("%Y-%m-%dT%H:%M:%SZ", $rsscache_time).'</timestamp -->
      <contributor>
        <ip>127.0.0.1</ip>
      </contributor>
      <text xml:space="preserve">__NOTOC__
<xsl:value-of disable-output-escaping="yes" select="link"/>
<xsl:value-of disable-output-escaping="yes" select="title"/><br/>
=<xsl:value-of disable-output-escaping="yes" select="title"/>=<br/>
{{#mw_media:<xsl:value-of disable-output-escaping="yes" select="link"/>|640}}
<xsl:value-of disable-output-escaping="yes" select="description"/>
[[:Category:<xsl:value-of disable-output-escaping="yes" select="category"/>|<xsl:value-of disable-output-escaping="yes" select="category"/>]]

==Keywords==
[[Category:<xsl:value-of disable-output-escaping="yes" select="media:keywords"/>]]

</text>
    </revision>
  </page>
</xsl:for-each>

</mediawiki>

</xsl:template>


</xsl:stylesheet>
