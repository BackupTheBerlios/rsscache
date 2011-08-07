<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
  <head>
    <title><xsl:value-of select="rss/channel/title"/></title>
    <!-- style type="text/css">
    @import url(rsscache.css);
    </style -->
  </head>
  <body>
      <h1><xsl:value-of select="rss/channel/title"/></h1>
      <xsl:value-of disable-output-escaping="yes" select="rss/channel/description"/>

      <xsl:for-each select="rss/channel/item">
      <div class="article">
        <h2><a href="{link}" rel="bookmark"><xsl:value-of select="title"/></a></h2>
        <xsl:value-of disable-output-escaping="yes" select="description"/>
      </div>
      </xsl:for-each>

  </body>
</html>
</xsl:template>
</xsl:stylesheet>