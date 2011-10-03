<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="text" omit-xml-declaration="yes" indent="no"/>


<xsl:template match="/">{
{
  "title": "<xsl:value-of disable-output-escaping="yes" select="/rss/channel/title"/>",
  "link": "<xsl:value-of disable-output-escaping="yes" select="/rss/channel/link"/>",
  "description": "<xsl:value-of disable-output-escaping="yes" select="/rss/channel/description"/>",
  "lastBuildDate": "<xsl:value-of disable-output-escaping="yes" select="/rss/channel/lastBuildDate"/>",
  <xsl:for-each select="rss/channel/item">
{
    "title": "<xsl:value-of disable-output-escaping="yes" select="title"/>",
    "link": "<xsl:value-of disable-output-escaping="yes" select="link"/>",
    "pubDate": "<xsl:value-of disable-output-escaping="yes" select="pubDate"/>",
    "description": "<xsl:value-of disable-output-escaping="yes" select="description"/>",
    "category": "<xsl:value-of disable-output-escaping="yes" select="category"/>",
}
  </xsl:for-each>
}}
</xsl:template>


</xsl:stylesheet>