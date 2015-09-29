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

class sm_ApplicationManagerView extends sm_ViewElement
{
	function __construct($model=null)
	{
		parent::__construct($model);	
		$this->uiView =new sm_Page("ApplicationManagerView");
		$this->uiView->setTitle("Applications");
		$this->uiView->addCSS("plugins.css");
	}
	
	function setOp($op=null) //,$args=null)
	{
		$this->op=$op;
	
	}
	function build()
	{
		// display main template
	
		switch ($this->op)
		{
			case 'list':
				$this->applications_list();
			break;
				
		}
	}
	
	function applications_list()
	{		
	
		$panel = new sm_Panel("ApplicationsInstalled");
		$panel->setTitle("Application Installed");
		$panel->setClass("pluginManagerList");
		$panel->icon("<i class='glyphicon glyphicon-cog'></i>");
		foreach($this->model['installed'] as $k=>$app_data)
		{
			$this->app_data=$app_data;
			$panel->insert(sm_Form::buildForm("app_installed",$this));
		}
		$this->uiView->insert($panel);
		
		$panel = new sm_Panel("ApplicationsAvailable");
		$panel->setTitle("Application Available");
		$panel->setClass("pluginManagerList");
		$panel->icon(sm_formatIcon("paperclip"));
		foreach($this->model['available'] as $k=>$app_data)
		{
			$this->app_data=$app_data;
			$panel->insert(sm_Form::buildForm("app_available",$this));
		}
		$this->uiView->insert($panel);
		
		//$this->addView();

	}
	
	function app_installed_form($form){
		$form->configure(array(
				"prevent" => array("bootstrap", "jQuery", "focus"),
				"view" => new View_Vertical(),
				//"labelToPlaceholder" => 1,
				"action"=>"applications/list/actions",
				
		));
	
	
	    $count=0;
	    //$form->addElement(new Element_HTML("<h4>Installed</h4>"));
		//foreach($this->model['installed'] as $k=>$plugin_data)
	    $app_data=$this->app_data;
		{
			$form->configure(array("id"=>$app_data['class'],"type"=>"plugin_installed"));
			/*if($count%4==0)
				$form->addElement(new Element_HTML("<div class='row'>"));*/
			$this->setTemplateId("plugin","plugins.tpl.html");
			ob_start();
			if(isset($app_data['status']))
			{
				$btn = new Element_GenericButton("Uninstall","",array("name"=>$app_data['class'],"class"=>"button light-gray btn-xs btn"));
				$btn->render();
			}
			
			 
			$app_data['actions']=ob_get_contents();
			ob_end_clean();
		//	var_dump($plugin_data)	;	
			$this->tpl->addTemplatedata(
					'plugin',
					$app_data
			);
			$form->addElement(new Element_HTML("<div class='col-lg-3 col-md-4 col-sm-6'>"));
			//$form->addElement(new Element_Checkbox("","plugin",array($v),array("class"=>"")));
			$form->addElement(new Element_HTML($this->tpl->getTemplate("plugin")));
			
			$form->addElement(new Element_HTML("</div>"));
		/*	if(($count!=0 && ($count+1)%4==0) || count($this->model['installed'])==$count+1)
				$form->addElement(new Element_HTML("</div>"));
			$count++;*/
		}
		
		//exit();		
	
	
	}
	
	function app_available_form($form){
		$form->configure(array(
				"prevent" => array("bootstrap", "jQuery", "focus"),
				"view" => new View_Vertical(),
				//"labelToPlaceholder" => 1,
				"action"=>"applications/list/actions",
				
		));
	
	
		$count=0;
		//$form->addElement(new Element_HTML("<h4>Installed</h4>"));
		//foreach($this->model['available'] as $k=>$plugin_data)
		$app_data=$this->app_data;
		{
			$form->configure(array("id"=>$app_data['class'],"type"=>"plugin_available"));
			/*if($count%4==0)
				$form->addElement(new Element_HTML("<div class='row'>"));*/
			$this->setTemplateId("plugin","plugins.tpl.html");
			ob_start();
			
				$btn = new Element_GenericButton("Install","",array("name"=>$app_data['class'],"class"=>"button light-gray btn-xs btn"));
				$btn->render();
				
			$app_data['actions']=ob_get_contents();
			ob_end_clean();
			//	var_dump($plugin_data)	;
			$this->tpl->addTemplatedata(
					'plugin',
					$app_data
			);
			$form->addElement(new Element_HTML("<div class='col-md-3'>"));
			//$form->addElement(new Element_Checkbox("","plugin",array($v),array("class"=>"")));
			$form->addElement(new Element_HTML($this->tpl->getTemplate("plugin")));
				
			$form->addElement(new Element_HTML("</div>"));
		/*	if(($count!=0 && ($count+1)%4==0) || count($this->model['available'])==$count+1)
				$form->addElement(new Element_HTML("</div>"));
			$count++;*/
		}
	
		//exit();
	
	
	}
	
	static public function menu(sm_MenuManager $menu)
	{
		if($menu)
		{
			$menu->setSubLink("Settings",'Applications','applications/list');
		}	
	}
}