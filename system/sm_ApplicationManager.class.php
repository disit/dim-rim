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

define("APPLICATION_FOLDER",'apps/*/');
define("APPLICATIONMANAGERTABLE","applications");
define("APPLICATION_INSTALLED",1);
define("APPLICATION_DISABLED",0);

class sm_ApplicationManager implements sm_Module
{
	static protected $instance;
	protected $applications;
	protected $applicationsPaths;
	protected $db;
	function __construct()
	{
		$this->applicationsPaths=array();
		global $classPath;
		if(isset($classPath))
			$this->applicationsPaths['apps']=$classPath['apps'];
		else 
			$this->applicationsPaths['apps']=APPLICATION_FOLDER;
		$this->applications=array();
		$this->db=sm_Database::getInstance();
	}
	
	
	static function instance()
	{
		if(self::$instance ==null)
		{
			$c=__CLASS__;
			self::$instance=new $c();
		}
		return self::$instance;
	}
	
	static public function install($db)
	{
		$sql="CREATE TABLE IF NOT EXISTS `".APPLICATIONMANAGERTABLE."` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(128) NOT NULL,
			`path` varchar(1024) NOT NULL,
			`description` TEXT NOT NULL,
	  		`version` varchar(128) NOT NULL,
			`status` int(11) DEFAULT '1',
			`class` varchar(128) NOT NULL DEFAULT '',
	  		PRIMARY KEY (`id`),
	  		KEY `name` (`name`)
			)
			ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
	
			$result=$db->query($sql);
			if($result)
			{
				sm_Logger::write("Installed ".APPLICATIONMANAGERTABLE." table");
				return true;
			}
			return false;
	}
	
	static public function uninstall($db)
	{
		$sql="DROP TABLE `".APPLICATIONMANAGERTABLE."`;";
		$result=$db->query($sql);
		if($result)
			return true;
		return false;
	}
	
	
	static function getApplication($name)
	{
		if(isset($this->applications[$name]))
			return $this->applications[$name];
		return null;
	}
	

	function getApplications()
	{
		return $this->applications;
	}
	
	function installApplication($appName){
		$res =false;
		if(class_exists($appName))
		{
			$app = new $appName();
			if($app)
			{
				if(!$this->existsApplication($appName))
				{
					
					$installer = new sm_Installer();
					sm_Logger::write("Installing App ".$appName);
					$installer->setMode('install_app');
					$installer->setAppPath($app->getAppRoot());
					$installer->execute();
					$this->registerApplication($app);
					sm_EventManager::handle(new sm_Event("InstallApplication",$appName));
					$res =true;
				}
			}
		}
		return $res;
		
	}
	

	function uninstallApplication($appName){
		$res =false;
		if($this->existsApplication($appName))
		{
			$app = new $appName();
			$installer = new sm_Installer();
			sm_Logger::write("Uninstalling App ".$appName);
			$installer->setMode('uninstall_app');
			$installer->setAppPath($app->getAppRoot());
			$installer->execute();
			$this->deleteApplication($appName);
			sm_EventManager::handle(new sm_Event("UninstallApplication",$appName));
			$res =true;
		}
		return $res;
	}
	
	protected function registerApplication(sm_Application $app)
	{
		$app_data['name']=$app->getAppName();
		$app_data['version']=$app->getAppVersion();
		$app_data['description']=$app->getAppDescription();
		$app_data['path']=$app->getAppPath();
		$app_data['class']=get_class($app);
		$this->db->save(APPLICATIONMANAGERTABLE,$app_data);
	}
	
	public function detectApplications()
	{
		$results=array();
		
		foreach($this->applicationsPaths as $path=>$p)
		{
			$file_names = glob($p."*App.class.php",GLOB_BRACE);
			if( FALSE === $file_names || count($file_names)==0) //FALSE === file_exists($file_name) )
			 	continue;
			else
			{
				foreach($file_names as $c)
				{
					$class=explode(".",$c);
					//$class=str_replace($v, "", $class[0]);
					$class=substr($class[0],strripos($class[0], "/")+1);					
					if(!$this->existsApplication($class))
					{
						include_once $c;
						$results[]=new $class;
					}
				}
			}
		}
		return $results;
	}
	
	
	public function listApplications()
	{
		$results=$this->db->select(APPLICATIONMANAGERTABLE);
		return $results;
	}
	
	protected function _loadInstalledApplications()
	{
		$results=$this->db->select(APPLICATIONMANAGERTABLE,array("status"=>1));
		return $results;
	}
	
	protected function existsApplication($class)
	{
		$results=$this->db->select(APPLICATIONMANAGERTABLE,array("class"=>$class));
		return count($results)>0;
	}
	
	protected function deleteApplication($appName)
	{
		$this->db->delete(APPLICATIONMANAGERTABLE,array("name"=>$appName));
	}
}