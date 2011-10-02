<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:media="http://search.yahoo.com/mrss/" xmlns:rsscache="data:,rsscache" xmlns:cms="data:,cms">
<xsl:output method="xml" indent="yes"/>


<xsl:template match="/"><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
<xsl:for-each select="rss/channel/item">
  <!-- xsl:if test="category != ''" -->
  <url>
    <loc>http://rsscache.a1.25u.com/?c=<xsl:value-of disable-output-escaping="yes" select="category"/></loc>
    <lastmod><xsl:value-of disable-output-escaping="yes" select="/rss/channel/lastBuildDate" /></lastmod>
    <changefreq>always</changefreq>
    <video:video>
      <video:thumbnail_loc><xsl:value-of disable-output-escaping="yes" select="enclosure/@url"/></video:thumbnail_loc>
      <video:title><xsl:value-of disable-output-escaping="yes" select="title"/></video:title>
      <video:description><xsl:value-of disable-output-escaping="yes" select="description"/></video:description>
      <video:duration><xsl:value-of select="media:duration"/></video:duration>
    </video:video>
  </url>
  <!-- /xsl:if -->
</xsl:for-each>
</urlset>
</xsl:template>


</xsl:stylesheet>