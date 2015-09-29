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

class VIRTUOSOView extends sm_ViewElement
{
	
	function __construct($data=NULL)
	{
		parent::__construct($data);
	}
	
	/**
	 * Create the HTML code for the module.
	 */
	public function build() {
	
		if(isset($this->model))
		{
			$url=$this->model["url"];
			$this->uiView = $html = new sm_HTML();
			$html->insert("tool_frame","<iframe src='".$url."' id='owlim_tool_view' ></iframe>");
			$html->addCss("virtuoso.css","main",VIRTUOSOPlugin::instance()->getFolderUrl("css"));
			//$this->addView();
		}
	}
	
	static function menu(sm_MenuManager $menu)
	{
		$menu->setMainLink("RDF Tools",'',"eye-open");
		$menu->setSubLink("RDF Tools","VIRTUOSO Workbench","virtuoso/workbench");
	}
		
	
	public function onExtendView(sm_Event &$event)
	{
		$obj = $event->getData();
		if(is_object($obj))
		{
			if(get_class($obj)=="SiiMobilitySettingsView")
			{
				
				$panel=$obj->getUIView()->getUIElement("SiiMobilitySettingsPanel");
				$this->model=$obj->getModel();
				$panel->insert(sm_Form::buildForm("VIRTUOSO_config", $this));
				$panel->addCss("virtuoso.css","main",VIRTUOSOPlugin::instance()->getFolderUrl("css"));
			}
			if(get_class($obj)=="SiiMobilityIndexGeneratorView" && ($obj->getOp()=="editor" || $obj->getOp()=="welcome"))
			{
			
				$panel=$obj->getUIView()->getUIElement("WelcomePanel");
				if($panel)
				{
					$m=array(
						'link'=>'virtuoso/workbench',
						'description'=>"OpenLink Virtuoso",
						'type'=>"virtuoso",
						'title'=>"Virtuoso",
						'class'=>"col-md-2"
					);
					$icon = new sm_HTML();
					$icon->setTemplateId("iconizedlink",SiiMobilityApp::getFolder("templates")."iconizedlink.tpl.html");
					$icon->insertArray($m);
					$panel->insert("stores", $icon);
				}
				
			}
		}
		if(is_object($obj) && is_a($obj,"sm_ViewElement") && !sm_Controller::instance()->isCallback())
		{
			$ui=$obj->getUIView();
			$ui->addCSS("virtuoso.css",$ui->getTemplateId(),VIRTUOSOPlugin::instance()->getFolderUrl("css"));
		}
	}
	
	function VIRTUOSO_config_form($form){
		$form->configure(array(
				"prevent" => array("bootstrap", "jQuery", "focus"),
				//	"view" => new View_Vertical,
				//"labelToPlaceholder" => 0,
				"action"=>"virtuoso/settings"
		));
	
		//foreach($this->data['HLM'] as $m=>$p)
		{
			$m='VIRTUOSO';
			$form->addElement(new Element_HTML('<div class="cView_panel" id="'.$m.'">'));
			$form->addElement(new Element_HTML('<legend>'.str_replace("SM_","",$m).'</legend>'));
			foreach($this->model[$m] as $item=>$i)
			{
				$form->addElement(new Element_Textbox($i['description'],$i['name'],array('value'=>$i['value'],'label'=>$i['description'])));
			}
			$form->addElement(new Element_Button("Save","",array("class"=>"button light-gray")));
			$form->addElement(new Element_HTML('</div>'));
		}
	
	
	}
	

}