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

class sm_Application
{
	protected $appFolder;
	protected $appName;
	protected $appDescription;
	protected $appVersion;
	protected $baseDir;
	
	function __construct()
	{
		$this->appFolder = dirname(__FILE__);
		$this->appName=get_class($this);
		$this->appDescription="";
		$this->appVersion="";
		$this->baseDir=sm_relativeURL(dirname(__FILE__))."/"; //.DIRECTORY_SEPARATOR;
		spl_autoload_register(array($this,'app_autoloader'));
		
	}
	
	function bootstrap(){
		
		
	}
	
	
	function app_autoloader($class)
	{
		if(file_exists($this->appFolder."/". $class . '.class.php'))
		{
			include_once $this->appFolder."/" . $class . '.class.php';
			return;
		}
		else if(file_exists($this->appFolder."/". $class . '.php'))
		{
			include_once $this->appFolder."/" . $class . '.php';
			return;
		}
		$dirs=glob($this->appFolder."/{*/}", GLOB_BRACE | GLOB_ONLYDIR | GLOB_MARK );
		if(is_array($dirs))
		{
			foreach($dirs as $d=>$path)
			{
				if(file_exists($path.$class.".class.php"))
				{
					include_once $path.$class.".class.php";
					return;
				}
				else if(file_exists($path . $class . '.php'))
				{
					include_once $path . $class . '.php';
					return;
				}
	
			}
		}
	}	
	function getAppPath(){
		return $this->appFolder;
	}
	
	function getControllers(){
		$path="";
		$results=array();
		$controllers=array();
		
		$file_names = glob($this->appFolder."/controllers/*Controller.class.php",GLOB_BRACE);
		if( FALSE === $file_names || count($file_names)==0) //FALSE === file_exists($file_name) )
			return $controllers;
		else
			$results=$file_names;
		
		foreach($results as $r=>$p)
		{
			$m=explode("/",$p);
			preg_match('/(\w+).class/i', $p,$m);
			$controllers[]=$m[1];
		}
		
		return $controllers;
	}
	
	function getViews(){
		$path="";
		$results=array();
		$views=array();
	
		$file_names = glob($this->appFolder."/views/*View.class.php",GLOB_BRACE);
		if( FALSE === $file_names || count($file_names)==0) //FALSE === file_exists($file_name) )
			return $views;
		else
			$results=$file_names;
	
		foreach($results as $r=>$p)
		{
			$m=explode("/",$p);
			preg_match('/(\w+).class/i', $p,$m);
			$views[]=$m[1];
		}
	
		return $views;
	}
	
	
	function getObservers()
	{
		$path="";
		$results=array();
		$observers=array();
		
		$file_names = glob($this->appFolder."/observers/*Observer.class.php",GLOB_BRACE);
		if( FALSE === $file_names || count($file_names)==0) //FALSE === file_exists($file_name) )
				return $observers;
		else
			$results=$file_names;
		
		
		foreach($results as $r=>$p)
		{
			$m=explode("/",$p);
			preg_match('/(\w+).class/i', $p,$m);
			$observers[]= new $m[1];
		}
		return $observers;
	}
	
	function getAppRoot()
	{
		return $this->appFolder;
	}
	
	function getAppName(){
		return $this->appName;
	}
	
	function getAppDescription(){
		return $this->appDescription;
	}
	
	function getAppVersion(){
		return $this->appVersion;
	}
	
	static function getFolder($name)
	{
		return $this->appFolder.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR;
	}
	
	
	static function getFolderUrl($name)
	{
		return $this->appFolder.$name."/";
	}
	
	function installApp($db)
	{
		$res=true;
		$dirs=glob($this->appFolder."/{,*/}", GLOB_BRACE | GLOB_ONLYDIR | GLOB_MARK );
		$args=array($db);
		if(is_array($dirs))
		{
			foreach($dirs as $d=>$path)
			{
				$files=glob($path."{*class.php}", GLOB_BRACE);
				
				foreach($files as $c)
				{
					$class=explode(".",$c);
					//$class=str_replace($v, "", $class[0]);
					$class=substr($class[0],strripos($class[0], "/")+1);					
					if(method_exists($class, "install"))
					{
						sm_Logger::write($class."::install");
						if(call_user_func_array(array($class, "install"), array($db)))
						{
							sm_set_message("app ".$this->appName.": ".$class." installed");
						}
						else
						{
							sm_set_error("app ".$this->appName.": ".$class." not installed");
						}
					}
					if(method_exists($class, "menu"))
					{
						sm_Logger::write($class."::menu");
						if(call_user_func_array(array($class, "menu"), array(sm_MenuManager::instance())))
						{
							sm_set_message("app ".$this->appName.": ".$class." installed");
						}
						else
						{
							sm_set_error("app ".$this->appName.": ".$class." not installed");
						}
					}
				}
			}
		}
		return true;
	}
	
	function uninstallApp($db)
	{
		$dirs=glob($this->appFolder."/{,*/}", GLOB_BRACE | GLOB_ONLYDIR | GLOB_MARK );
		$args=array($db);
		if(is_array($dirs))
		{
			foreach($dirs as $d=>$path)
			{
				$files=glob($path."{*class.php}", GLOB_BRACE);
		
				foreach($files as $c)
				{
					$class=explode(".",$c);
					//$class=str_replace($v, "", $class[0]);
					$class=substr($class[0],strripos($class[0], "/")+1);
					if(method_exists($class, "uninstall"))
					{
						sm_Logger::write($class."::uninstall");
						call_user_func_array(array($class, "uninstall"), $args);
					}
				}
			}
		}
		return true;
	}
		
}