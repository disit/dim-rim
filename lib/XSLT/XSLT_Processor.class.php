<?php
/* Data Ingestion Manager and RDF Indexing Manager (DIM-RIM).
   Copyright (C) 2015 DISIT Lab http://www.disit.org - University of Florence

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public License
   as published by the Free Software Foundation; either version 2
   of the License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. */

DEFINE("SAXON_JAR","/saxon/saxon9he.jar");

class XSLT_Processor
{
	protected $schemaDir;
	
	function XSLT_Processor(){
		$this->schemaDir=realpath("./").DIRECTORY_SEPARATOR."schema/";
	}
	
	function setSchemaDir($path)
	{
		$this->schemaDir=$path;
	}
	
	function mapFile($xmlFile,$xsltFile)
	{
		$filename=$this->schemaDir."output_".round(microtime(true) * 1000).".xml";
		$xsltFilePath=$xsltFile;
		$saxon = str_replace("/", DIRECTORY_SEPARATOR, SAXON_JAR);
		$cmd ="java -jar \"".__DIR__.$saxon."\" -xsl:\"$xsltFilePath\" -s:\"$xmlFile\" -o:\"$filename\"";
		sm_Logger::error($cmd);
		$output=array();
		$xmlString="";
		exec($cmd,$output);
		if(!file_exists($filename))
			sm_Logger::error($output);
		else 
		{
			$xmlString = file_get_contents($filename);
			unlink($filename);
		}
		
		return $xmlString;
	}
	
	function mapString($xmlStr,$xsltFile)
	{
		$filename=$this->schemaDir."tmp_".round(microtime(true) * 1000).".xml";
		$f=fopen($filename,"wt");
		fwrite($f,$xmlStr);
		fclose($f);
		$res = $this->mapFile($filename,$xsltFile);
		unlink($filename);
		return $res; 
	}
}
