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

class SiiMobilitySettingsController extends sm_ControllerElement
{
	protected $model;
	protected $view;
	
	function __construct()
	{
		$this->model = sm_Config::instance();
	}
	
	/**
	 * @des Gets the SiiMobility settings
	 *
	 * @url GET /IndexGenerator/settings
	 * 
	 */
	function index()
	{
		$conf=$this->model->conf;
		$data=array();
		foreach($conf as $c=>$p)
		{
			if(strpos($p['module'],"SiiMobilityApp")!==FALSE)
			{
				$p['name']=$c;
				$data[$p['module']][]=$p;
			}
		}
		
		$this->view = new SiiMobilitySettingsView($data);
		$this->view->setData($data);
	}	
	
	/**
	 * @desc Write the SiiMobility settings data
	 * 
	 * @url POST /IndexGenerator/settings
	 */
	function writeSettings($data)
	{
		unset($data['form']);
		if($this->model->save($data))
			sm_set_message("SiiMobility IndexManager Settings successfully saved!");
		else
			sm_set_error("");
		if(isset($_SERVER['HTTP_REFERER']))
			sm_app_redirect($_SERVER['HTTP_REFERER']);
	}
}
