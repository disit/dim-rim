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

class SiiMobilityBuilderView extends sm_ViewElement
{
	protected $tail;
	function __construct($data=NULL)
	{
		parent::__construct($data);
		$this->tail = null;
		if(class_exists("sm_TailView"))
		{
			$this->tail = new sm_TailView($this->model);
		//	sm_View::instance()->unregister($this->tail);
		}
	}
	
	/**
	 * Create the HTML code for the module.
	 */
	public function build() {
	
		$op = $this->getOp();
		if($op=="command")
		{
			$html=new sm_HTML();
			$html->setTemplateId('message', 'ui.tpl.html');
			if($this->model['result'])
				$html->insertArray(array(
					'type'=>'success',
					'message'=>$this->model['message'])
			);
			else
			{
				$html->insertArray(array(
						'type'=>'danger',
						'message'=>$this->model['message'])
				);
			}
			$this->uiView = new sm_JSON();
			$this->uiView->insert(array("result"=>$html->render()));
			return;
		}
		if($op=="refresh" && $this->tail)
		{
			$this->tail->setOp($op);
			/*$this->tail->build();
			$this->uiView = $this->tail->getUIView(); */
		}
		if($op=="refresh::plot" || $op=="settings")
		{
			$this->uiView = new sm_JSON();
			$this->uiView->insert($this->model);
			return;
		}
		if($op=="refresh::progress")
		{
			$this->model['queue']=$this->queue()->render();
			$this->model['progress']=$this->progress()->render();
			$this->uiView = new sm_JSON();
			$this->uiView->insert($this->model);
			return;
		}
		if($op=="view")
		{
			$title=$this->model['title'];
			$this->uiView=new sm_Panel("BuilderView");
			$this->uiView->setType("default");
			$this->uiView->setTitle($title);
			$this->uiView->addCss("tail.css","main",sm_TailPlugin::instance()->getFolderUrl("css"));
			$this->uiView->addJs("jqtail.js","main",sm_TailPlugin::instance()->getFolderUrl("js"));
			$this->uiView->addJs("var tailRefreshUrl='".$this->model['refreshUrl']."';");
		//	$this->uiView->addJs("plotRefreshUrl='".$this->model['plotRefreshUrl']."';");
			$this->uiView->addCss("builder.css","main",SiiMobilityApp::getFolderUrl("css"));
		/*	$this->uiView->addCss("jquery.jqplot.min.css","main",SiiMobilityApp::getFolderUrl("css"));
			$this->uiView->addJs("jquery.jqplot.js","main",SiiMobilityApp::getFolderUrl("js"));
			$this->uiView->addJs("jqplot.dateAxisRenderer.js","main",SiiMobilityApp::getFolderUrl("js/plugins"));
			$this->uiView->addJs("jqplot.highlighter.js","main",SiiMobilityApp::getFolderUrl("js/plugins"));
			$this->uiView->addJs("jqplot.cursor.js","main",SiiMobilityApp::getFolderUrl("js/plugins"));
			
			$this->uiView->addJS("SM_NagiosDaemonQueue.js","main",SM_NagiosPlugin::instance()->getFolderUrl("js"));
			$this->uiView->addJS("SM_NagiosDaemonSetting.js","main",SM_NagiosPlugin::instance()->getFolderUrl("js"));*/
			$this->uiView->addJS("Builder.js","main",SiiMobilityApp::instance()->getFolderUrl("js"));
			$this->uiView->addJs("builder.refreshUrl='".$this->model['builderRefreshUrl']."';");
			$this->addViewMenu();
			
			
			$queue = new sm_Panel("Queue");		
			$queue->setTitle("Queue");
			$queue->icon("<i class='sm-icon sm-icon-queue-small'> </i>");
			$queue->insert($this->queue());
			$progress = new sm_Panel("BuilderProgress");
			$progress->setTitle("Progress");
			$progress->insert($this->progress());
			$progress->icon("<i class='sm-icon sm-icon-graph-small'> </i>");
		/*	$panel3 = new sm_Panel("NagiosDaemonSettings");
			$panel3->setTitle("Settings");
			$panel3->insert($this->settings());
			$panel3->icon("<i class='sm-icon sm-icon-daemon-graph-small'> </i>");*/
			$panelTail = new sm_Panel("NoPanel");	
			
			if($this->tail)
				$panelTail = $this->tail->panel(); 
			
			$left=new sm_Grid("BuilderLeft");
			$left->addRow(array($panelTail,$queue),array(12,12));
			$status=new sm_Grid("BuilderStatus");
			$status->addRow(array($left,$progress),array(8,4));
			
			
			$this->uiView->insert($status);
			
			return;
		}
		
		
	}
	
	protected function progress(){
		
		$html = new sm_HTML("wrapper");
		
		
		$grid = new sm_Grid("ProgressValues");
		$left=new sm_HTML("LeftCol");
		$left->insert("value","<b>Total Items:</b> ".$this->model['total']);
		$right=new sm_HTML("RightCol");
		if(!empty($this->model['committed']) && isset($this->model['committed'][0]))
			$n = count($this->model['committed']);
		else 
			$n = 0;
		$right->insert("value","<b>Committed Items:</b> ".$n);
		
		$grid->addRow(array($left,$right),array(6,6));
		
		$gauge = new sm_HTML();
		$gauge->setTemplateId("progress_bar","ui.tpl.html");
		$gauge->insert("class","progress-bar-success");
		$gauge->insert("value",$this->model['progress']);
		$gauge->insert("title",sprintf("%01.2f%%" ,$this->model['progress']));
		$gauge->insert("text",sprintf("%01.2f%%" ,$this->model['progress']));
		$gauge->insert("min",0);
		$gauge->insert("max",100);
		
		$html->insert("pre","<div id='progress-wrapper'><b>Work done</b>");
		$html->insert("gauge",$gauge);
		$html->insert("values",$grid);
		$html->insert("committed",$this->committedData());
		
		$html->insert("end","</div>");
		return $html;
	}
	
	protected function committedData(){
		if(isset($this->model['committed']) && count($this->model['committed'])>0)
		{
			$content=new sm_HTML("Time");
			
			$table=new sm_Table("BuilderCommittedTable");
			$table->makeResponsive();
			$table->addHRow("",array("data-type"=>"table-header"));
			$table->addHeaderCell("Name");
			$table->addHeaderCell("Start Insert");
			$table->addHeaderCell("End Insert");
			$table->addHeaderCell("Time (s)");
			$totalTime=0;
			$maxTime=0;
			foreach ($this->model['committed'] as $q)
			{
				$table->addRow();
			
				foreach($q as $k)
				{					
					$table->addCell($k);	
				}
				$format = 'd/m/y H:i:s';
				$d2 = DateTime::createFromFormat($format,$q[2]);
				$d1 = DateTime::createFromFormat($format,$q[1]);
			//	sm_Logger::write($d2);
			//	sm_Logger::write($d1);
			//	$delta = strtotime($q[2])-strtotime($q[1]);
				$delta = strtotime($d2->format('Y-m-d H:i:s'))-strtotime($d1->format('Y-m-d H:i:s'));
				$delta=$delta<1?1:$delta;//sm_Logger::write($d1);
				$totalTime+=$delta;
				$maxTime=$maxTime<$delta?$delta:$maxTime;
				$table->addCell($delta);
			}
			$totalTime=isset($this->model['Time'])?$this->model['Time']:$totalTime;
			$grid = new sm_Grid("TimeValues");
			$left="<b>Total Time:</b> ".gmdate("H:i:s",$totalTime);
			$right="<b>Max Time:</b> ".gmdate("H:i:s",$maxTime);
			$grid->addRow(array($left,$right),array(6,6));
			$content->insert("value",$grid);
			$content->insert("tabletitle","<h4>Commitments List</h4>");
				
			$content->insert("table",$table);
		}
		else 
		{
			$content = new sm_HTML("BuilderCommittedTable");
			$content->insert("Empty", "<div id=BuilderCommittedTable class='alert alert-info' role='alert'>Committed items not found!</div>");
		}
		return $content;
	}
	
	
	protected function queue(){
		$queue=$this->model['queue'];
		$html = new sm_HTML("BuilderQueue");
		if(is_array($queue) && count($queue)>0)
		{
			$html->insert("pre", "<div id=BuilderQueue>");
			$content=new sm_Table("BuilderQueueTable");
			$content->makeResponsive();
			$content->addHRow("",array("data-type"=>"table-header"));
			foreach(array_keys($queue[0]) as $k)
			{
						$content->addHeaderCell($k);
			}

			foreach ($queue as $q)
			{
				$content->addRow();
				
				foreach(array_keys($q) as $k)
				{
					
						$v=$q[$k];
						$content->addCell($v);
											
				}		
			}
			
			$html->insert("table", $content);
			$html->insert("end", "</div>");
		}
		else
			$html->insert("Empty", "<div id=BuilderQueue class='alert alert-info' role='alert'>Queue is empty!</div>");
		return $html;
	}
	
	protected function performance(){
		$performance=$this->model['performance'];
		
		$chart=new sm_HTML("chart_mem");
		$chart->setTemplateId("monitor_graph",SiiMobilityApp::instance()->getFolderUrl("templates")."builder.tpl.html");
		$chart->insert("id","mem");
		$chart->insert("title","Memory Usage (%)");
		$chart->insert("data",$performance['mem']);
	
		$chart2=new sm_HTML("chart_cpu");
		$chart2->setTemplateId("monitor_graph",SiiMobilityApp::instance()->getFolderUrl("templates")."builder.tpl.html");
		$chart2->insert("id","cpu");
		$chart2->insert("title","Cpu Usage (%)");
		$chart2->insert("data",$performance['cpu']);
		
		$html = new sm_HTML();
		$html->insert("1",$chart);
		$html->insert("2",$chart2);
		
		return $html;
	}
	
/*	protected function settings(){
		$html = new sm_HTML();
		$html->insert("form",sm_Form::buildForm("settings", $this));
		
		return $html;
	}
	
	public function settings_form(sm_Form $form){
		$form->configure(array(
				"prevent" => array("bootstrap","jQuery","focus","redirect"),
				"action"=>"nagios/configurator/daemon/settings"
		));
		$form->addElement(new Element_YesNo("Enable Rollback", "rollback",array('value'=>$this->model['settings']['rollback']['value'])));
		$form->addElement(new Element_Number("Set Sleep (secs)", "sleep",array('value'=>$this->model['settings']['sleep']['value'],"min"=>1,"max"=>3600,"step"=>1)));
		$form->addElement(new Element_Button("Apply","",array("class"=>"button light-gray btn-xs")));
	}
	
	protected function commandMenu()
	{
		
		$menu = new sm_NavBar("NagiosDaemonMenu");
		$menu->insert("brand","NagiosDaemon");
		
		if(sm_Config::get("SMNAGIOSCONFIGRUN",0) && sm_Config::get("SMNAGIOSCONFIGDAEMONSHUTDOWN",0)==0)
			$menu->insert("isAlive", array("id"=>"isAliveCmd","url"=>"nagios/configurator/daemon/command/alive","title"=>'Check Alive',"icon"=>"sm-icon sm-icon-daemon-alive","link_attr"=>"data-target='.message'"));
		if(sm_Config::get("SMNAGIOSCONFIGDAEMONSHUTDOWN",0)==0)
		{
			if(sm_Config::get("SMNAGIOSCONFIGRUN",0)==0)
			{
					
				//paused or stopped
				$menu->insert("run", array("id"=>"runCmd","url"=>"nagios/configurator/daemon/command/run","title"=>'Resume',"icon"=>"sm-icon sm-icon-daemon-play","link_attr"=>"data-toggle='#pauseCmd' data-target='.message'"));
				//$menu->insert("pause", array("id"=>"pauseCmd","url"=>"nagios/configurator/daemon/command/pause","title"=>'Pause',"icon"=>"sm-icon sm-icon-daemon-pause","link_attr"=>"style='display:none;' data-toggle='#runCmd' data-target='.message'"));
			}
			else
			{
				//running
				//$menu->insert("run", array("id"=>"runCmd","url"=>"nagios/configurator/daemon/command/run","title"=>'Resume',"icon"=>"sm-icon sm-icon-daemon-play","link_attr"=>"style='display:none;' data-toggle='#pauseCmd' data-target='.message'"));
				$menu->insert("pause", array("id"=>"pauseCmd","url"=>"nagios/configurator/daemon/command/pause","title"=>'Pause',"icon"=>"sm-icon sm-icon-daemon-pause","link_attr"=>"data-toggle='#runCmd' data-target='.message'"));
			}
			//on
			$menu->insert("shutdown", array("id"=>"shutdownCmd","url"=>"nagios/configurator/daemon/command/shutdown","title"=>'Service Off',"icon"=>"sm-icon sm-icon-daemon-off","link_attr"=>"data-toggle='#startCmd' data-target='.message'"));
			//$menu->insert("start", array("id"=>"startCmd","url"=>"nagios/configurator/daemon/command/start","title"=>'Service On',"icon"=>"sm-icon sm-icon-daemon-on","link_attr"=>"style='display:none;' data-toggle='#shutdownCmd' data-target='.message'"));
		}
		else
		{
			//off
			//$menu->insert("shutdown", array("id"=>"shutdownCmd","url"=>"nagios/configurator/daemon/command/shutdown","title"=>'Service Off',"icon"=>"sm-icon sm-icon-daemon-off","link_attr"=>"style='display:none;' data-toggle='#startCmd' data-target='.message'"));
			$menu->insert("start", array("id"=>"startCmd","url"=>"nagios/configurator/daemon/command/start","title"=>'Service On',"icon"=>"sm-icon sm-icon-daemon-on","link_attr"=>"data-toggle='#shutdownCmd' data-target='.message'"));
		}	
		return $menu;
	}
	*/
	protected function addViewMenu()
	{
		/*$this->uiView->menu($this->commandMenu());
		$this->uiView->addJS("SM_NagiosDaemonMenu.js","main",SM_NagiosPlugin::instance()->getFolderUrl("js"));*/
	}
	
	
}