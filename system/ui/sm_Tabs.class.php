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

define("TAB_LEFT","tabs-left");
define("TAB_RIGHT","tabs-right");

class sm_Tabs extends sm_UIElement
{
	protected $tabs;
	protected $panels;
	protected $active;
	protected $orientation;
	protected $tab_class;
	protected $class;
	
	function __construct($id=null)
	{
		parent::__construct($id);
		$this->newTemplate("tabs","ui.tpl.html");
		$this->tabs=array();	
		$this->active=null;
		$this->orientation=null;
		$this->tab_class=null; //"light-gray";
		$this->class=null;
	}
	
	
	function setTabClass($class)
	{
		$this->tab_class=$class;
	}
	
	function setClass($class)
	{
		$this->class=$class;
	}
	
	function insert($var, $obj)
	{
		if(isset($obj['title']) && isset($obj['paneldata']))
		{
			$this->tabs[$var]=array(
					"tab_id"=>$var,
					"tab_title"=>$obj['title'],
					"tab_data"=>$obj['paneldata'],
					"tab_panel_class"=>isset($obj['panel_class'])?$obj['panel_class']:""
			);
		}
		
	}
	
	function setActive($id)
	{
		$this->active=$id;
	}
	
	function setLeftOrientation()
	{
		$this->orientation=TAB_LEFT;
	}
	
	function setRightOrientation()
	{
		$this->orientation=TAB_RIGHT;
	}
	
	function render()
	{
		if(!$this->active)
		{
			$k=array_keys($this->tabs);
			$this->active=$k[0];
		}
		foreach($this->tabs as $k=>$obj)
		{
			$active=$this->active==$k?"active":"";
			$this->tabs[$k]['tab_active']=$active;
		//	$this->tabs[$k]["tab_data"]=$this->tabs[$k]["tab_data"]->render();
			if(is_a($this->tabs[$k]["tab_data"],"sm_UIElement"))
			{
				$obj = $this->tabs[$k]["tab_data"];
				$this->tabs[$k]["tab_data"]=$obj->render();
				$this->copyCSS($obj);
				$this->copyJs($obj);
				
			}
			else 
				$this->tabs[$k]["tab_data"]=$this->tabs[$k]["tab_data"];
			
		}
		$this->addTemplateDataRepeat("tabs", 'li', $this->tabs);
		$this->addTemplateDataRepeat("tabs", 'div', $this->tabs);
		if($this->orientation)
		{
			$this->addTemplateData("tabs", array("id"=>$this->getId(),"orientation"=>$this->orientation,"class"=>$this->class));
			$this->addCss("bootstrap.vertical-tabs.min.css",$this->tpl_id,"css/bootstrap3/");
		}
		else 
			$this->addTemplateData("tabs", array("id"=>$this->getId(),"tab_class"=>$this->tab_class,"class"=>$this->class));
		return $this->display("tabs");
	}
}