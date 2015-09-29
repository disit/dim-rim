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

class SiiMobilityApp extends sm_Application implements sm_Module
{
	protected $baseDir;
	//protected $appFolder;
	static $instance;
	function __construct()
	{
		parent::__construct();
		$this->appDescription="RDF Index Manager";
		$this->appVersion="1.0";
		$this->appName="RDF Index Manager";
		
		$this->appFolder = dirname(__FILE__);
		$this->baseDir=sm_relativeURL(dirname(__FILE__))."/"; //.DIRECTORY_SEPARATOR;
	}
	
	static function getFolder($name)
	{
		return self::instance()->appFolder.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
	}
	
	static function getFolderUrl($name)
	{
		return self::instance()->baseDir.$name."/";
	}
	
	static function instance()
	{
		if(self::$instance ==null)
		{
			$c=__CLASS__;
			self::$instance=new self();
		}
		return self::$instance;
	}
	
	static public function install($db)
	{
		include 'php/dbDetails.php';
		sm_Config::set("SIIMOBILITYDB",array('value'=>$sql_details['db'],"description"=>"Set the RIM Data Source Database Name"));
		sm_Config::set("SIIMOBILITYDBUSER",array('value'=>$sql_details['user'],"description"=>"Set the RIM Data Source Database User"));
		sm_Config::set("SIIMOBILITYDBPWD",array('value'=>$sql_details['pass'],"description"=>"Set the RIM Data Source Database Password"));
		sm_Config::set("SIIMOBILITYDBURL",array('value'=>$sql_details['host'],"description"=>"Set theRIM Data Source Database URL"));
		sm_Logger::write("Installed RIM Data Source Database Settings");
		
		sm_Config::set('ONTOLOGIESPATH',array('value'=>"/media/rim/Ontologie","description"=>"Set the Data Source File Path for Ontologies Triples"));
		sm_Config::set('STATICDATAPATH',array('value'=>"/media/rim/Triples","description"=>"Set the Data Source File Path for Static Data Triples"));
		sm_Config::set('REALTIMEDATAPATH',array('value'=>"/media/rim/Triples","description"=>"Set the Data Source File Path for Real Time Data Triples"));
		sm_Config::set('RECONCILIATIONSPATH',array('value'=>"/media/rim/Triples","description"=>"Set the Data Source File Path for Reconciliations Data Triples"));
		sm_Logger::write("Installed RIM Data Source File Path Settings");
		
	}
	
	static public function uninstall($db)
	{
		sm_Config::delete("SIIMOBILITYDB");
		sm_Config::delete("SIIMOBILITYDBUSER");
		sm_Config::delete("SIIMOBILITYDBPWD");
		sm_Config::delete("SIIMOBILITYDBURL");
		sm_Config::delete('ONTOLOGIESPATH');
		sm_Config::delete('STATICDATAPATH');
		sm_Config::delete('REALTIMEDATAPATH');
		sm_Config::delete('RECONCILIATIONSPATH');
	}
}
