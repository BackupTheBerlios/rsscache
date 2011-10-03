<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output cdata-section-elements="summary title" method="xml" indent="yes"/>


<xsl:template match="/"><feed xmlns="http://www.w3.org/2005/Atom">
  <title><xsl:value-of disable-output-escaping="yes" select="/rss/channel/title"/></title>
  <subtitle></subtitle>
  <link rel="self"><xsl:attribute name="href"><xsl:value-of disable-output-escaping="yes" select="/rss/channel/link"/></xsl:attribute></link>
  <link><xsl:attribute name="href"><xsl:value-of disable-output-escaping="yes" select="/rss/channel/link"/></xsl:attribute></link>
  <id></id>
  <updated><xsl:value-of disable-output-escaping="yes" select="/rss/channel/lastBuildDate"/></updated>
  <author>
    <name><xsl:value-of disable-output-escaping="yes" select="/rss/channel/author"/></name>
    <email></email>
  </author>
  <xsl:for-each select="rss/channel/item">
  <entry>
    <title><xsl:value-of disable-output-escaping="yes" select="title"/></title>
    <link><xsl:attribute name="href"><xsl:value-of disable-output-escaping="yes" select="link"/></xsl:attribute></link>
    <!-- link rel="alternate" type="text/html" href="http://example.org/2003/12/13/atom03.html"/ -->
    <!-- link rel="edit" href="http://example.org/2003/12/13/atom03/edit"/ -->
    <id></id>
    <updated><xsl:value-of disable-output-escaping="yes" select="pubDate"/></updated>
    <summary><xsl:value-of disable-output-escaping="yes" select="description"/></summary>
  </entry>
  </xsl:for-each>
</feed>
</xsl:template>


</xsl:stylesheet>