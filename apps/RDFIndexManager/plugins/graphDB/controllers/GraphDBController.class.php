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

class GraphDBController extends sm_ControllerElement
{
	/**
	 * @desc Create a GraphDB repository
	 *
	 * @url GET GraphDB/create/:name
	 *
	 * @callback
	 */
	function create_repository($name=null){
		$GraphDB = new GraphDB();
		$ret=false;
		if($GraphDB->exist($name))
			$ret = $GraphDB->create($name);
		$this->view = new SiiMobilityJSONView($ret);
	}
	
	/**
	 * @desc Create a GraphDB repository
	 *
	 * @url GET GraphDB/flush/:name
	 *
	 * @callback
	 */
	function flush_repository($name=null){
		$GraphDB = new GraphDB();
		$ret=false;
		if($GraphDB->exist($name))
			$ret = $GraphDB->flush($name);
		$this->view = new SiiMobilityJSONView($ret);
	}
	
	/**
	 * @desc Check if an GraphDB repository exits
	 *
	 * @url GET GraphDB/exist/:name
	 *
	 * @callback
	 */
	function exist_repository($name=null){
		$GraphDB = new GraphDB();
		$ret=false;
		if($GraphDB->exist($name))
			$ret = $GraphDB->exist($name);
		$this->view = new SiiMobilityJSONView($ret);
	}
	
	/**
	 * Gets the monitor tool page
	 *
	 * @url GET GraphDB/openrdf-workbench
	 *
	 */
	function openrdf_workbench()
	{
		
		$url=sm_Config::get('GraphDBOPENRDFWORKBENCH',"");
		
		if($url!="")
		{
			$data['url']=$url;	
			$this->view = new GraphDBView($data);
			$this->view->setOp("workbench");
		}
		
	}
	
	/**
	 * @desc Write the SiiMobility settings data
	 *
	 * @url POST GraphDB/settings
	 */
	function writeSettings($data)
	{
		$this->model = sm_Config::instance();
		unset($data['form']);
		if($this->model->save($data))
			sm_set_message("GraphDB Settings successfully saved!");
		else
			sm_set_error("");
		if(isset($_SERVER['HTTP_REFERER']))
			sm_app_redirect($_SERVER['HTTP_REFERER']);
	}
	
	/***** EVENTS ***************/
	
	function onGenerateScriptEvent(sm_Event &$event)
	{
		
		$obj = $event->getData(); 
		if($obj && $obj->getType()=="GraphDB")
		{
			$event->stopPropagation();
			$GraphDB = new GraphDB();
			
			$res=$GraphDB->doScript($obj);
			$event->setResult($res);
		}
		return true;
	}
	
	function onDeleteRepository(sm_Event &$event)
	{		
		$obj = $event->getData();
		if($obj && is_a($obj,"SiiMobilityRepository") && $obj->getType()=="GraphDB")
		{
			$event->stopPropagation();
			$GraphDB = new GraphDB();
			$res=$GraphDB->deleteRepository($obj->getRepositoryID());
			$event->setResult($res);
		}
		return true;
		
	}
	
	function onExtendController(sm_Event &$event)
	{
		$obj = $event->getData();
		if(get_class($obj)=="SiiMobilitySettingsController")
		{
			$this->extendConfigurator($obj);
		}
	}
	
	function extendConfigurator($obj)
	{
		$curView=$obj->getView();
		if($curView)
		{
			$data=array();
		
			$conf= sm_Config::instance()->conf;
			
			foreach($conf as $c=>$p)
			{
				if(strpos($p['module'],"GraphDB")===FALSE)
					continue;
				$p['name']=$c;
				$data[$p['module']][]=$p;
			}
			$objdata = $curView->getModel() + $data;
			$curView->setModel($objdata);
		}
	}
	
	
	
	

} 