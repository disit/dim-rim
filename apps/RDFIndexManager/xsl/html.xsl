<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
  <html>
  <body>
  <h1><xsl:value-of select="SiiMobilityIndex/RepositoryID"/></h1>
  <p><b>Date:</b> <xsl:value-of select="SiiMobilityIndex/GenerationEnd"/><br></br>
  <b>Version:</b> <xsl:value-of select="SiiMobilityIndex/Version"/><br></br>
  <b>Database Type:</b> <xsl:value-of select="SiiMobilityIndex/Type"/><br></br>
  <b>Security Level:</b> <xsl:value-of select="SiiMobilityIndex/SecurityLevel"/><br></br>
  <b>Description:</b> <xsl:value-of select="SiiMobilityIndex/Description"/></p>
  <h2>Ontologie</h2>
    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
      <tr style="background-color: rgb(255, 153, 0);">
        <th style="text-align:left">Nome</th>
        <th style="text-align:left">Versione</th>
      </tr>
      <xsl:for-each select="SiiMobilityIndex/ontologies/ontology">
      <tr>
        <td><xsl:value-of select="ID_Ontology"/></td>
        <td><xsl:value-of select="TripleDate"/></td>
      </tr>
      </xsl:for-each>
    </table>
	
	<h2>Dati Statici</h2>
    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
      <tr style="background-color: rgb(255, 153, 0);">
		<th style="text-align:left">Categoria</th>
        <th style="text-align:left">Dataset</th>
        <th style="text-align:left">Versione</th>
      </tr>
      <xsl:for-each select="SiiMobilityIndex/staticdata/opendata">
      <tr>
		<td><xsl:value-of select="Category"/></td>
        <td><xsl:value-of select="ID_OpenData"/></td>
        <td><xsl:value-of select="TripleStart"/></td>
      </tr>
      </xsl:for-each>
    </table>
	
	<h2>Riconciliazioni</h2>
    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
      <tr style="background-color: rgb(255, 153, 0);">
        <th style="text-align:left">Nome</th>
        <th style="text-align:left">Versione</th>
      </tr>
      <xsl:for-each select="SiiMobilityIndex/reconciliations/reconciliation">
      <tr>
        <td><xsl:value-of select="ID_Reconciliation"/></td>
        <td><xsl:value-of select="TripleDate"/></td>
      </tr>
      </xsl:for-each>
    </table>
	
	<h2>Dati Realtime</h2>
    <table border="1" cellpadding="0" cellspacing="0" style="width: 100%;">
      <tr style="background-color: rgb(255, 153, 0);">
        <th style="text-align:left">Dataset</th>
        <th style="text-align:left">Data Inizio</th>
      </tr>
      <xsl:for-each select="SiiMobilityIndex/realtimedata/opendatart">
      <tr>
        <td><xsl:value-of select="ID_OpenData"/></td>
        <td><xsl:value-of select="TripleStart"/></td>
      </tr>
      </xsl:for-each>
    </table>
  </body>
  </html>
</xsl:template>
</xsl:stylesheet>
