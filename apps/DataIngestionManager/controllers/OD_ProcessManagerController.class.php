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

class OD_ProcessManagerController extends sm_ControllerElement
{
	protected $model;
	protected $view;
	
	function __construct()
	{
		
	}
	
	/**
	 * @desc Gets the ProcessManager View
	 *
	 * @url GET /ProcessManager
	 * 
	 */
	function index()
	{
		$this->view = new OD_ProcessManagerView();	
		$this->view->setOp("index");
	}
	
	/**
	 * @desc Gets the Panel in ProcessManager View
	 *
	 * @url GET /ProcessManager/View/:panel
	 *
	 * @callback
	 *
	 */
	function panel($panel)
	{
		$this->view = new OD_ProcessManagerView();
		$this->view->setOp($panel);
	}
	
	/**
	 * @desc Gets the Open Data (:id) properties
	 *
	 * @url GET /ProcessManager/Edit/:id
	 *
	 * @callback
	 *
	 */
	function edit($id=null)
	{
		$data=array();
		if($id)
		{
			$request['draw']=0;
			$request['columns'][0]["data"]="process";		
			$request['columns'][0]["searchable"]=true;
			$request['columns'][0]["search"]["value"]=$id;
			$request['columns'][0]["search"]["regex"]=false;
			$OD = new OD_ProcessManager();
			$result = $OD->getMultiTables($request);		
			if($result['recordsFiltered']==1)
			{
				$data=$result['data'][0];
				$s = new OD_SchedulerManager();
				$data['tasks']=$s->getTasksProcess($data['process']);
				
			}
		}
		$this->view = new OD_ProcessManagerJSONView($data);
		//$this->view->setOp("edit");
		
	}
	
	/**
	 * @desc Get the Data License Text
	 *
	 * @url GET  ProcessManager/License/:id
	 *
	 * @callback
	 */
	function license($id=null)
	{
		if(isset($id) && is_string($id))
		{
			$OD = new OpenData();
			if ($OD->select($id))
			{
				$data['data']=$OD;
				$this->view = new OD_ProcessManagerView($data);
				$this->view->setOp("license");
				return;
			}
			else
				sm_set_error(sm_Database::getInstance()->getError());
	
		}
		else
			sm_set_error("Invalid data");
	
		sm_app_redirect($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * @desc Gets MultiTables
	 *
	 * @url POST /ProcessManager/MultiTables
	 *
	 * @callback
	 */
	function MultiTables()
	{
		$ProcessManager = new OD_ProcessManager();
		$this->view = new OD_ProcessManagerJSONView($ProcessManager->getMultiTables($_POST));
	}
	
	/**
	 * @desc AjaxCommand
	 *
	 * @url POST /ProcessManager/AjaxCommand/:cmd
	 *
	 * @callback
	 */
	function AjaxCommand($cmd=null)
	{
		if($cmd!=null)
		{
			$ProcessManager = new OD_ProcessManager();
			$this->view = new OD_ProcessManagerJSONView($ProcessManager->ajaxCommand($cmd,$_POST));
		}
	}
	
	/**
	 * @desc Write the ProcessManager settings data
	 *
	 * @url POST /ProcessManager/settings
	 * 
	 * @callback
	 */
	function writeSettings($data)
	{
		unset($data['form']);
		$settings = sm_Config::instance();
		if($settings->save($data))
			$response = array(
					"message"=>array("Settings successfully saved!")
			);
		else
			$response = array(
					"errors"=>array("An error occurred when saving settings!")
			);
		$this->view = new OD_ProcessManagerJSONView($response);
	}
	

	/**
	 * @desc Get the Triple File Browser
	 *
	 * @url GET  /ProcessManager/Repository
	 * @url GET  /ProcessManager/Repository/:id
	 * @callback
	 */
	function files_repository_browser($id=null)
	{
		$dirLister = new OD_DirectoryLister();
		$UPLOAD_DIR=sm_Config::get('STATICDATAPATH',"/media/Triples");
		if(isset($id) && is_string($id))
		{
			$oData= new OpenData();
			if ($oData->select($id))
			{
				if($oData->getReal_time()=="yes")
					$UPLOAD_DIR=sm_Config::get('REALTIMEDATAPATH',"/media/Triples");
				$UPLOAD_DIR=str_replace("\\", "/", $UPLOAD_DIR);
				if(substr($UPLOAD_DIR, -1)=="/")
					$UPLOAD_DIR.=$oData->getCategory()."/".$oData->getprocess();
				else
					$UPLOAD_DIR.="/".$oData->getCategory()."/".$oData->getprocess();
			}
			// Initialize the directory array
			$dirLister->setAppUrl("/ProcessManager/Repository/".$id);
		}
		else 
			$dirLister->setAppUrl("ProcessManager/Repository");
		$dirLister->setRelativeHome($UPLOAD_DIR);
		if (isset($_GET['dir'])) {
			$data['dirArray'] = $dirLister->listDirectory($_GET['dir']);
		} 
		else if (isset($_GET['get']))
		{
			if(!$dirLister->download($_GET['get']))
			{
				sm_send_error("204");
				exit();
			}
			
		}
		else {
			$data['dirArray'] = $dirLister->listDirectory($UPLOAD_DIR."/");
		}
		$data['breadcrumbs'] = $dirLister->listBreadcrumbs();
		$message = $dirLister->getSystemMessages();
		$data['message'] = $message?$message:array();
		$data['home']=explode("/",$UPLOAD_DIR);
		$this->view = new OD_ProcessManagerView($data);
		$this->view->setOp("file_browser");
		
	}
	
	/**
	 * @desc Get the Triple File list in Archive for a data set
	 *
	 * @url GET  /ProcessManager/Files/:id
	 *
	 * @callback
	 */
	function triples_archive_files($id=null)
	{
		if(isset($id) && is_string($id))
		{
			$oData= new OpenData();
		
			if ($oData->select($id))
			{
				$data['data']=$oData;
				$data['title']='Triples Files for '.$oData->getprocess();
				if($oData->getReal_time()=="no")
					$UPLOAD_DIR=sm_Config::get('STATICDATAPATH',"/media/Triples");
				else
					$UPLOAD_DIR=sm_Config::get('REALTIMEDATAPATH',"/media/Triples");
				$UPLOAD_DIR=str_replace("\\", "/", $UPLOAD_DIR);
				if(substr($UPLOAD_DIR, -1)=="/")
					$UPLOAD_DIR.=$oData->getCategory()."/".$oData->getprocess();
				else
					$UPLOAD_DIR.="/".$oData->getCategory()."/".$oData->getprocess();
				$data['repository']=$UPLOAD_DIR;
			}
			$data['url']="ProcessManager/Repository/".$id;
			$this->view = new OD_ProcessManagerView($data);
			$this->view->setOp("files");
		}
		else
			sm_set_error("Invalid data");
	}
/*	function triples_archive_files($id=null)
	{
		if(isset($id) && is_string($id))
		{
			$procMan = new OD_ProcessManager();
			$oData= new OpenData();
			if ($oData->select($id))
			{
				if($oData->getReal_time()=="no")
					$UPLOAD_DIR=sm_Config::get('STATICDATAPATH',"/media/Triples");
				else 
					$UPLOAD_DIR=sm_Config::get('REALTIMEDATAPATH',"/media/Triples");
				$UPLOAD_DIR=str_replace("\\", "/", $UPLOAD_DIR);
				if(substr($UPLOAD_DIR, -1)=="/")
					$UPLOAD_DIR.=$oData->getCategory()."/".$oData->getprocess();
				else
					$UPLOAD_DIR.="/".$oData->getCategory()."/".$oData->getprocess();
				$data['data']=$oData;
				$data['title']='Files for '.$oData->getprocess();
				$data['repository']=$UPLOAD_DIR;
				$data['files']=$procMan->getTriplesFiles($oData);
				$this->view = new OD_ProcessManagerView($data);
				$this->view->setOp("files");
				return;
			}
			else
				sm_set_error(sm_Database::getInstance()->getError());
	
		}
		else
			sm_set_error("Invalid data");
	
		sm_app_redirect($_SERVER['HTTP_REFERER']);
	}*/
	
}