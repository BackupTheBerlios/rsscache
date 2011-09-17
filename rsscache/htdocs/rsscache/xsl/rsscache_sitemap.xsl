<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms">
<xsl:output method="xml" indent="yes"/>
<xsl:template match="/"><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
 xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
<xsl:for-each select="rss/channel/item">
<!--    if (trim ($config_xml['item'][$i]['category']) != '') -->
<url>
  <loc>http://rsscache.a1.25u.com/?c=<xsl:value-of disable-output-escaping="yes" select="category"/></loc>
<!--
The formats are as follows. Exactly the components shown here must be present, with exactly this punctuation. Note that the "T" appears literally in the string, to indicate the beginning of the time element, as specified in ISO 8601.

   Year:
      YYYY (eg 1997)
   Year and month:
      YYYY-MM (eg 1997-07)
   Complete date:
      YYYY-MM-DD (eg 1997-07-16)
   Complete date plus hours and minutes:
      YYYY-MM-DDThh:mmTZD (eg 1997-07-16T19:20+01:00)
   Complete date plus hours, minutes and seconds:
      YYYY-MM-DDThh:mm:ssTZD (eg 1997-07-16T19:20:30+01:00)
   Complete date plus hours, minutes, seconds and a decimal fraction of a second
      YYYY-MM-DDThh:mm:ss.sTZD (eg 1997-07-16T19:20:30.45+01:00)
-->
<!-- lastmod>strftime ('%F' /* 'T%T%Z' */)</lastmod>
<changefreq>always</changefreq -->
<video:video>
<video:thumbnail_loc><xsl:value-of disable-output-escaping="yes" select="image"/></video:thumbnail_loc>
<video:title><xsl:value-of disable-output-escaping="yes" select="title"/></video:title>
<video:description><xsl:value-of disable-output-escaping="yes" select="description"/></video:description>
<video:duration><xsl:value-of disable-output-escaping="yes" select="media:duration"/></video:duration>
</video:video>
</url>
</xsl:for-each>
</urlset>
</xsl:template>
</xsl:stylesheet>