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

class sm_Installer extends sm_App
{
	function __construct(sm_UIElement $uiView=null) {
	//	session_start();
	//	parent::__construct();
	//	$_SERVER['REQUEST_URI']="config/system";
	//	$this->observer->unregister('sm_UserObserver');
	//	$this->view->unregister('sm_Menu');
		self::$instance=$this;
		$this->module="";
		$this->widgets=array();
		$this->controller = null; //sm_Controller::instance();
		$this->view = sm_View::instance();
		if($uiView)
			$this->view->setMainView($uiView);
		$this->observer = sm_Observer::instance();
		$this->observer->unregister('sm_UserObserver');
		$this->view->unregister('sm_Menu');
		$this->plugins = null; //sm_PluginManager::instance();
		$this->menu=null;//sm_MenuManager::instance();
		$this->messages = null; //new sm_Message();
		$this->redirect="";
		self::$instance=$this;
		
		
		
	}
	
	function setMode($mode)
	{
		$this->op=$mode;
	}
	
	function setModule($module)
	{
		$this->module=$module;
	}
	
	function setAppPath($appPath)
	{
		$this->appPath=$appPath;
	}
	
	function handle(){
		//parent::handle();
		$this->bootstrap();
		if(!$this->isRedirection())
		{
			$this->view->build();
			$this->view->render();
		}
		else 
			$this->redirect();
			
	}
	
	function execute()
	{
		
		$type=!empty($this->op)?$this->op:"";
		if($type=="install")
			$this->do_install();
		if($type=="install_app")
			$this->do_install_app();
		else if($type=="uninstall_app")
			$this->do_uninstall_app();
		else if($type=="install_plugins")
			$this->do_install("plugin");
		else if($type=="uninstall")
			$this->do_uninstall();
		else if($type=="update_plugin")
			$this->do_update("plugin");
		else if($type=="update")
			$this->do_update();
		else if($type=="update_app")
			$this->do_update("apps");
		else if($type=="menu")
			$this->do_install("menu");
		
	}
	
	function do_install($what="system")
	{
		
		include("system/config.inc.php");
		sm_Logger::$usedb=false;
		$db = sm_Database::getInstance();
		$db->initialize($dbHost, $dbUser, $dbPwd);
		$db->setDB($dbName);
		if($what=="system" || $what=="apps")
		{
			$paths=$classPath;
			foreach($paths as $p=>$v)
			{
				if($p!=$what)
					continue;
				sm_Logger::write($v);
				
				//$sys=glob($v."{,controllers/,views/}{*class.php}", GLOB_BRACE); 
				$sys=glob($v."{".implode("/,",$classPathStructure)."}{".$this->module."*class.php}", GLOB_BRACE);
				$args=array($db);
				foreach($sys as $c)
				{
					$class=explode(".",$c);
					//$class=str_replace($v, "", $class[0]);
					$class=substr($class[0],strripos($class[0], "/")+1);
					if($class=="sm_Module")
						continue;
					
					if(method_exists($class, "install"))
					{
						sm_Logger::write($class."::install");
						call_user_func_array(array($class, "install"), $args);
					}
							
				}	
			}	
		}
		if($what=="menu" || $what=="system" || $what=="apps")
		{
			$paths=$classPath;
			foreach($paths as $p=>$v)
			{
				if($p!=$what)
					continue;
				sm_Logger::write($v);
				//$sys=glob($v."{,controllers/,views/}{".$this->module."*class.php}", GLOB_BRACE);
				$sys=glob($v."{".implode("/,",$classPathStructure)."}{*class.php}", GLOB_BRACE);
				$manager=sm_MenuManager::instance();
				$args=array($manager);
				foreach($sys as $c)
				{
					$class=explode(".",$c);
					//$class=str_replace($v, "", $class[0]);
					$class=substr($class[0],strripos($class[0], "/")+1);
					if($class=="sm_Module")
						continue;
					
					if(method_exists($class, "menu"))
					{
						$reflection = new ReflectionClass($class);
						$func1 = $reflection->getMethod('menu');
						if($func1->isStatic())
						{
							sm_Logger::write($class."::menu");
							call_user_func_array(array($class, "menu"), $args);
						}
						
					}
						
				}
			}
		}
		if($what=="plugin")
		{
			$this->plugins = sm_PluginManager::instance();
			$paths=$pluginPath;
			foreach($paths as $p=>$v)
			{
				sm_Logger::write($v);
				$sys=glob($v."*/{*Plugin.php}", GLOB_BRACE);
				$args=array($db);
				foreach($sys as $c)
				{
					$class=explode(".",$c);
					//$class=str_replace($v, "", $class[0]);
					$class=substr($class[0],strripos($class[0], "/")+1);
					if(!class_exists($class))
						include $c;
					$this->plugins->installPlugin($class);
					/*if(method_exists($class, "install"))
					{
						sm_Logger::write($class."::install");
						call_user_func_array(array($class, "install"), $args);
					}*/
			
				}
			}
		}
		sm_Logger::write("End Installation");
		return true;
	}
	
	function do_uninstall_app()
	{
	
		include("system/config.inc.php");
		sm_Logger::$usedb=false;
		$db = sm_Database::getInstance();
		$db->initialize($dbHost, $dbUser, $dbPwd);
		$db->setDB($dbName);
		$paths=$classPath;
		foreach($paths as $p=>$v)
		{
			if($p!="apps")
				continue;
			
			$sys=glob($v."{".implode("/,",$classPathStructure)."}{*class.php}", GLOB_BRACE);

			sm_Logger::write($this->module);
			foreach($sys as $c)
			{
				if(!empty($this->appPath) && strpos(realpath($c), $this->appPath)===false)
					continue;
					
				$class=explode(".",$c);
				//$class=str_replace($v, "", $class[0]);
				$class=substr($class[0],strripos($class[0], "/")+1);
				if($class=="sm_Module")
					continue;
					
				if(method_exists($class, "uninstall"))
				{
					$args=array($db);
					sm_Logger::write($class."::uninstall");
					call_user_func_array(array($class, "uninstall"), $args);
				}
				if(method_exists($class, "uninstallMenu"))
				{
					$manager=sm_MenuManager::instance();
					$args=array($manager);
					sm_Logger::write($class."::uninstallMenu");
					call_user_func_array(array($class, "uninstallMenu"), $args);
				}
				
			}
		}
		
		
		
	}
	
	function do_install_app()
	{
	
		include("system/config.inc.php");
		sm_Logger::$usedb=false;
		$db = sm_Database::getInstance();
		$db->initialize($dbHost, $dbUser, $dbPwd);
		$db->setDB($dbName);
		$paths=$classPath;
		foreach($paths as $p=>$v)
		{
			if($p!="apps")
				continue;
			sm_Logger::write($v);
			$sys=glob($v."{".implode("/,",$classPathStructure)."}{*class.php}", GLOB_BRACE);
			foreach($sys as $c)
			{
				if(!empty($this->appPath) && strpos(realpath($c), $this->appPath)===false)
					continue;
					
				$class=explode(".",$c);
				//$class=str_replace($v, "", $class[0]);
				$class=substr($class[0],strripos($class[0], "/")+1);
				if($class=="sm_Module")
					continue;
					
				if(method_exists($class, "install"))
				{
					$args=array($db);
					sm_Logger::write($class."::install");
					call_user_func_array(array($class, "install"), $args);
				}
				if(method_exists($class, "menu"))
				{
					$manager=sm_MenuManager::instance();
					$args=array($manager);
					sm_Logger::write($class."::menu");
					call_user_func_array(array($class, "menu"), $args);
				}
			}
		}
	
	
	
	}
	
	
	function do_update($what="system")
	{
	
		include("system/config.inc.php");
		sm_Logger::$usedb=false;
		$db = sm_Database::getInstance();
		$db->initialize($dbHost, $dbUser, $dbPwd);
		$db->setDB($dbName);
		if($what=="system" || $what=="apps")
		{
			$paths=$classPath; 
			foreach($paths as $p=>$v)
			{
				if($p!=$what)
					continue;
				sm_Logger::write($v);
				$sys=glob($v."{,".implode(",",$classPathStructure)."}{".$this->module."*class.php}", GLOB_BRACE);
				
				foreach($sys as $c)
				{
					$class=explode(".",$c);
					//$class=str_replace($v, "", $class[0]);
					$class=substr($class[0],strripos($class[0], "/")+1);
					if($class=="sm_Module")
						continue;
						
					if(method_exists($class, "install"))
					{
						$args=array($db);
						sm_Logger::write($class."::install");
						call_user_func_array(array($class, "install"), $args);
					}
					if(method_exists($class, "menu"))
					{
						$manager=sm_MenuManager::instance();
						$args=array($manager);
						sm_Logger::write($class."::menu");
						call_user_func_array(array($class, "menu"), $args);
					}
						
				}
			}
		}
		if($what=="plugin")
		{
			$paths=$pluginPath;
			foreach($paths as $p=>$v)
			{
				sm_Logger::write($v);
				$sys=glob($v."*/{".$this->module."Plugin.php}", GLOB_BRACE);
				$args=array($db);
				foreach($sys as $c)
				{
					$class=explode(".",$c);
					//$class=str_replace($v, "", $class[0]);
					$class=substr($class[0],strripos($class[0], "/")+1);
					
					if(!class_exists($class))
						include $c;
					$this->plugins->installPlugin($class);
					/*if(method_exists($class, "install"))
					 {
					sm_Logger::write($class."::install");
					call_user_func_array(array($class, "install"), $args);
					}*/
						
				}
			}
		}
	}
	
	function do_uninstall()
	{
		
		include("system/config.inc.php");
		sm_Logger::$usedb=false;
		$db = sm_Database::getInstance();
		$db->initialize($dbHost, $dbUser, $dbPwd);
		$db->setDB($dbName);
		
		$paths=$pluginPath;
		foreach($paths as $p=>$v)
		{
			sm_Logger::write($v);
			$sys=glob($v."*/{*Plugin.php}", GLOB_BRACE);
			$args=array($db);
			foreach($sys as $c)
			{
				$class=explode(".",$c);
				//$class=str_replace($v, "", $class[0]);
				$class=substr($class[0],strripos($class[0], "/")+1);
				include $c;
				$this->plugins->uninstallPlugin($class);
				/*if(method_exists($class, "install"))
				 {
				sm_Logger::write($class."::install");
				call_user_func_array(array($class, "install"), $args);
				}*/
					
			}
		}
		
		foreach($classPath as $p=>$v)
		{
			$sys=glob($v."{*class.php}", GLOB_BRACE); 
			$args=array($db);
			foreach($sys as $c)
			{
				$class=explode(".",$c);
				$class=str_replace($v, "", $class[0]);
				if($class=="sm_Module")
					continue;
				sm_Logger::write($class."::uninstall");
				if(method_exists($class, "uninstall"))
					call_user_func_array(array($class, "uninstall"), $args);
						
			}	
		}	
	}
	
	function do_config()
	{
		$db = new sm_Database($_POST["DB_HOST"], $_POST["DB_USER"], $_POST["DB_PASS"]);
		if(!$db->query("CREATE SCHEMA IF NOT EXISTS ".$_POST["DB_NAME"]))
		{
			$e = $db->getError();
			if(!empty($e))
			{
				sm_set_error($e);
			}
			return false;
		}
		$line=array();
		$line[]='<?php';
		$line[]='include "structure.inc.php";';
		$line[]='/******* MySQL Settings  *******/';
		$line[]='//MySQL Host';
		$line[]='$dbHost="DB_HOST";';
		$line[]='//MySQL User';
		$line[]='$dbUser="DB_USER";';
		$line[]='//MySQL Pwd';
		$line[]='$dbPwd="DB_PASS";';
		$line[]='//MySQL Db Name';
		$line[]='$dbName="DB_NAME";';
		
		
		$dbconfig =implode("\n",$line);
		$dbconfig = str_replace("DB_HOST", $_POST["DB_HOST"], $dbconfig);
		$dbconfig = str_replace("DB_USER",  $_POST["DB_USER"], $dbconfig);
		$dbconfig = str_replace("DB_PASS", $_POST["DB_PASS"], $dbconfig);
		$dbconfig = str_replace("DB_NAME", $_POST["DB_NAME"], $dbconfig);
		$f=fopen("system/config.inc.php", "wt");
		fwrite($f,$dbconfig);
		fclose($f);
		return true;
	}
	
	function do_registerUserAdmin(){
		$data = $_POST;
		unset($data['step']);
		unset($data['nextstep']);
		if(isset($data['username']) && isset($data['password']))
		{
			$user = new sm_User();
		
			$data['active']=1;
			$id = $user->insertUser($data);
			$acl = new sm_ACL();
			$role=array(
				'userID'=>$id,
				'role_'.ACL_USER_ADMINISTRATOR=>1		
			);
			$acl->saveUserRole($role);
		}
		return true;
	}
	
	function do_step($step=null,$next=null)
	{
		$ret = false;
		switch($step)
		{
			case 1:
				$ret = isset($_POST['accept']) && $_POST['accept']==1;
				if(!$ret)
					sm_set_error("Please click Accept to continue!");	
				$_SESSION['accept']=$ret;
				break;
			case 2:
				if(isset($_SESSION['accept']) && $_SESSION['accept'])
					$ret = $this->do_config();
				break;
			case 3:
				if(isset($_SESSION['accept']) && $_SESSION['accept'])
					$ret = $this->do_install();
				break;
			case 4:
				if(isset($_SESSION['accept']) && $_SESSION['accept'])
					$ret = $this->do_registerUserAdmin();
			break;
				
		}
		if(!isset($_SESSION['accept']))
				$next=null;
		if($next)
			$this->setRedirection("?step=".$next);
		return $ret;
	}
	
	function termsAccepted()
	{
		return isset($_SESSION['accept']) && $_SESSION['accept'];
	}
/*	static function form_extend(sm_Form $f)
	{
		if(self::$instance instanceof sm_Installer)
		{
			if($f->getId()=="system_config")
			{
				$form_element=$f->getElement("Save");
				if($form_element instanceof Element_Button)
					$form_element->setAttribute("value", "Install");
				//$f->setSubmitMethod("installer_form_submit");
				
			}
		}
	}*/
	
}
