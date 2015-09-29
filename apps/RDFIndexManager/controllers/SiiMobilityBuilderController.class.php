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

class SiiMobilityBuilderController extends sm_ControllerElement
{

	/**
	 * @desc Execute a Builder Command
	 *
	 * @url GET IndexGenerator/Builder/Command/:command/:id
	 * 
	 * @callback
	 * 
	 * @access
	 * 
	 */
	function builder_command($command=null,$value=null)
	{
		$data=array();
		$data=SiiMobilityBuilder::command($command,$value);
		
		$this->view=new SiiMobilityBuilderView($data);
		$this->view->setOp("command");
	}

	/**
	 * @desc Gets the Builder page
	 *
	 * @url GET IndexGenerator/Editor/Builder/:id
	 *
	 * @access
	 * 
	 * @callback
	 */
	function builder($id=null)
	{
		if($id)
		{
			$repos = new SiiMobilityRepository();
			$repos->load($id);
			$log="progress.out";
			$file= $repos->getScriptPath();
			if(empty($file))
				$file="No File Available";
			else 
			{
				$path=pathinfo($file);
				$log=$path['dirname'].DIRECTORY_SEPARATOR.$log;
				$log = ltrim ($log, '/');
			}
			
			$data=array();
			$data['id']=$id;
			$data['file']=$log;
			$data['refreshUrl']=empty($log)?"":"IndexGenerator/Builder/Refresh/Log/?file=/".$log;
			$data['title']="Monitor";
			$data['plotRefreshUrl']="IndexGenerator/Builder/Refresh/Plot/".$id;
			$data['builderRefreshUrl']="IndexGenerator/Builder/Refresh/Progress/".$id;
			//$data['performance']=SiiMobilityBuilder::getPerformance($file);
			$time = strtotime($repos->getGenerationEnd())-strtotime($repos->getGenerationStart());
			if($time>0)
				$data['Time']=$time;
			$data['committed']=SiiMobilityBuilder::getCommittedData($repos); sm_Logger::write("getCommittedData for ".$id);
			$data['queue']=SiiMobilityBuilder::getData2Process($repos); sm_Logger::write("getData2Process for ".$id);
			$n=SiiMobilityBuilder::countItems2Process($repos); sm_Logger::write("countItems2Process for ".$id);
			$data['total']=isset($n)?$n:count($data['queue']); sm_Logger::write("Data for ".$id);
			$data['progress']=0;
			if($data['total']>0)
				$data['progress']=sprintf("%01.2f%%",100*($data['total']-count($data['queue']))/$data['total']);
			//sm_Logger::write("View Build controller for ".$id);
			$this->view=new SiiMobilityBuilderView($data);
			$this->view->setOp("view");
			
		}
	}
	
	
	/**
	 * @desc Gets the Builder page refresh
	 *
	 * @url GET IndexGenerator/Builder/Refresh/Log
	 *
	 * @callback
	 *
	 */
	function builder_refresh_Log()
	{
		$file=$_GET['file'];
		$tail = new sm_Tail();
		$data=$tail->refresh($file);
		$this->view=new sm_TailView($data);
		$this->view->setOp("refresh");
	}
	
	/**
	 * @desc Gets the Builder Performance Plot refresh
	 *
	 * @url GET IndexGenerator/Builder/Refresh/Plot/:id
	 *
	 * @callback
	 *
	 */
	function builder_plot_refresh($id=null)
	{
		$repos = new SiiMobilityRepository();
		$repos->load($id);
		$file=$repos->getScriptPath();
		$data=SiiMobilityBuilder::getPerformance($file);
		$this->view=new SiiMobilityBuilderView($data);
		$this->view->setOp("refresh::plot");
	}
	
	/**
	 * @desc Gets the Builder Performance Plot refresh
	 *
	 * @url GET IndexGenerator/Builder/Refresh/Progress/:id
	 *
	 * @callback
	 *
	 */
	function builder_refresh_progress($id=null)
	{
		$repos = new SiiMobilityRepository();
		$repos->load($id);
		$file=$repos->getScriptPath();
		$time = strtotime($repos->getGenerationEnd())-strtotime($repos->getGenerationStart());
		if($time>0)
			$data['Time']=$time;
		$data['queue']=SiiMobilityBuilder::getData2Process($repos);
		$data['committed']=SiiMobilityBuilder::getCommittedData($repos);
		$n=SiiMobilityBuilder::countItems2Process($repos);
		$data['total']=isset($n)?$n:count($data['queue']);
		$data['progress']=0;
		$committed = !empty($data['committed'])?count($data['committed']):0;
		if($data['total']>0)
			$data['progress']=sprintf("%01.2f",100*$committed/$data['total']);
		$this->view=new SiiMobilityBuilderView($data);
		$this->view->setOp("refresh::progress");
	}
	
	
	/**
	 * @desc Set the Builder Settings 
	 *
	 * @url POST IndexGenerator/Builder/Settings
	 *
	 * @callback
	 *
	 */
	function builder_settings_submit($data)
	{
		/*$data=SM_NagiosConfiguratorDaemon::getPerformance();
		$this->view=new SM_NagiosConfiguratorDaemonView($data);
		$this->view->setOp("refresh::plot");*/
		
		$response['result']="Builder Configurator: ";
		$response['result'].=SiiMobilityBuilder::saveSettings($data)?"Settings save successfully!":"Error when saving settings!";
		$this->view=new SiiMobilityBuilderView($response);
		$this->view->setOp("settings");
	}

}