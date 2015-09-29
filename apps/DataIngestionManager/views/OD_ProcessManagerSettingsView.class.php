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

class OD_ProcessManagerSettingsView extends sm_ViewElement 
{
	
	function __construct($model)
	{
		parent::__construct($model);
		
	}

	public function build() {
		$this->uiView=new sm_Page("ProcessManagerSettingsView");
		$this->uiView->setTitle("Settings");
		
		$menu = new sm_NavBar("cView_navbar");
		foreach(array_keys($this->model) as $k=>$title)
			$menu->insert($k,array("url"=>"#".$title,"title"=>str_replace("OD_ProcessManager","",$title),"icon"=>"sm-icon icon-".strtolower(str_replace("OD_ProcessManager","",$title))));
		
		
		$panel = new sm_Panel();
		$panel->setTitle("Process Manager Modules");
		$panel->icon("<i class='glyphicon glyphicon-list'></i>");
		$panel->insert($menu);
		
		$this->uiView->insert($panel);
				
		$panel = new sm_Panel("ProcessManagerSettingsPanel");
		$panel->setTitle("Process Manager Settings");
		$panel->icon("<i class='glyphicon glyphicon-cog'></i>");
		
		$this->uiView->insert($panel);
		$panel->insert(sm_Form::buildForm("OD_settings", $this));
		$this->uiView->addJS("config.js");
		//$this->uiView->addCss("SM.css","main",SM_IcaroApp::getFolderUrl("css"));	
		//$this->addView();
	}
	
	function OD_settings_form($form){
		$form->configure(array(
				"prevent" => array("bootstrap", "jQuery", "focus"),
				//	"view" => new View_Vertical,
				//"labelToPlaceholder" => 0,
				"action"=>"ProcessManager/settings"
		));
	

			$m='OD_ProcessManagerApp';
			$form->addElement(new Element_HTML('<div class="cView_panel" id="'.$m.'">'));
			$form->addElement(new Element_HTML('<legend>'.str_replace("OD_ProcessManager","",$m).'</legend>'));
			foreach($this->model[$m] as $item=>$i)
			{
				$form->addElement(new Element_Textbox($i['description'],$i['name'],array('value'=>$i['value'],'label'=>$i['description'])));
			}
			$form->addElement(new Element_Button("Save","",array("class"=>"button light-gray")));
			$form->addElement(new Element_HTML('</div>'));
		
	
	
	}
	
	
	
	
	/*static function menu(sm_MenuManager $menu)
	{
		$menu->setSubLink("Settings","Process Manager","ProcessManager/settings");
	}*/
}
