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

class VIRTUOSOController extends sm_ControllerElement
{
		
	/**
	 * Gets the VIRTUOSO tool page
	 *
	 * @url GET virtuoso/workbench
	 *
	 */
	function openrdf_workbench()
	{
		
		$url=sm_Config::get('VIRTUOSOWORKBENCH',"");
		
		if($url!="")
		{
			$data['url']=$url;	
			$this->view = new VIRTUOSOView($data);
			$this->view->setOp("workbench");
		}
		
	}
	
	/**
	 * @desc Write the VIRTUOSO settings data
	 *
	 * @url POST virtuoso/settings
	 */
	function writeSettings($data)
	{
		$this->model = sm_Config::instance();
		unset($data['form']);
		if($this->model->save($data))
			sm_set_message("VIRTUOSO Settings successfully saved!");
		else
			sm_set_error("");
		if(isset($_SERVER['HTTP_REFERER']))
			sm_app_redirect($_SERVER['HTTP_REFERER']);
	}
	
	/***** EVENTS ***************/
	
	function onGenerateScriptEvent(sm_Event &$event)
	{
		
		$obj = $event->getData();
		if($obj && $obj->getType()=="VIRTUOSO")
		{
			$event->stopPropagation();
			$virtuoso = new VIRTUOSO();
			$event->setResult($virtuoso->doScript($obj));
		}
		return true;
	}
	
	function onDeleteRepository(sm_Event &$event)
	{
	
		$obj = $event->getData();
		if($obj && is_a($obj,"SiiMobilityRepository") && $obj->getType()=="VIRTUOSO")
		{
			$event->stopPropagation();
			$virtuoso = new VIRTUOSO();
			$res=$virtuoso->deleteRepository($obj->getRepositoryID());
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
				if(strpos($p['module'],"VIRTUOSO")===FALSE)
					continue;
				$p['name']=$c;
				$data[$p['module']][]=$p;
			}
			$objdata = $curView->getModel() + $data;
			$curView->setModel($objdata);
		}
	}
	
	
	
	

} 