<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9">

<xsl:output method="html" encoding="UTF-8" indent="yes"/>

<xsl:template match="/">
<html>
<head>
    <title>XML Sitemap</title>
    <style>
        body { font-family: Arial, sans-serif; margin:20px; }
        h1 { font-size:22px; margin-bottom:10px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border:1px solid #ddd; padding:8px; }
        th { background:#f4f4f4; }
        tr:nth-child(even){ background:#f9f9f9; }
        a { color:#0073aa; text-decoration:none; }
    </style>
</head>
<body>
    <h1>XML Sitemap</h1>
    <p>This sitemap contains <xsl:value-of select="count(//sitemap:url)"/> URLs.</p>
    <table>
        <tr>
            <th>URL</th>
            <th>Last Modified</th>
        </tr>
        <xsl:for-each select="//sitemap:url">
            <tr>
                <td><a href="{sitemap:loc}"><xsl:value-of select="sitemap:loc"/></a></td>
                <td><xsl:value-of select="sitemap:lastmod"/></td>
            </tr>
        </xsl:for-each>
    </table>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
