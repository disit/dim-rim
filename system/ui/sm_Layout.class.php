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

class sm_Layout extends sm_HTML
{
	protected $layout;
	protected $class;
	protected $layoutOptions;
	/**
	 * 
	 * @param string $id
	 * @param string $parent
	 */
	function __construct($id=null,$parent=null)
	{
		parent::__construct($id,$parent);
		$this->layout=array();
		$this->class="";
		$this->layoutOptions=array();
		
	}
	
	function setOptions($settings=array())
	{
		$this->layoutOptions=array_merge($this->layoutOptions,$settings);
	}

	function setClass($class)
	{
		$this->class=$class;
	}
	
	/**
	 * 
	 * @param unknown $obj
	 */
	function insertEast($obj,$settings=array()){
		if(!isset($this->layout["east"]))
			$this->layout["east"]=array();
		$this->layout["east"]["east-".count($this->layout["east"])]=$obj;
		$this->layoutOptions['east']=$settings;
	}
	/**
	 *
	 * @param unknown $obj
	 */
	function insertWest($obj,$settings=array()){
		if(!isset($this->layout["west"]))
			$this->layout["west"]=array();
		$this->layout["west"]["west-".count($this->layout["west"])]=$obj;
		$this->layoutOptions['west']=$settings;
	}
	/**
	 *
	 * @param unknown $obj
	 */
	function insertNorth($obj,$settings=array()){
		if(!isset($this->layout["north"]))
			$this->layout["north"]=array();
		$this->layout["north"]["north-".count($this->layout["north"])]=$obj;
		$this->layoutOptions['north']=$settings;
	}
	
	function insertCenter($obj,$settings=array()){
		if(!isset($this->layout["center"]))
			$this->layout["center"]=array();
		$this->layout["center"]["center-".count($this->layout["center"])]=$obj;
	//	$this->layoutOptions['center']=$settings;
		
	}
	/**
	 *
	 * @param unknown $obj
	 */
	function insertSouth($obj,$settings=array()){
		if(!isset($this->layout["south"]))
			$this->layout["south"]=array();
		$this->layout["south"]["south-".count($this->layout["south"])]=$obj;
		$this->layoutOptions['south']=$settings;
	}


	function render()
	{

		$this->insert("pre","<div id='".$this->id."' class='".$this->class."'>");
		
		if(isset($this->layout["center"]))
		{
			$this->insert("pre-center","<div class='pane ui-layout-center'>");
			$this->insertArray($this->layout["center"]);
			$this->insert("end-center","</div>");
		}

		if(isset($this->layout["north"]))
		{
			$this->insert("pre-north","<div class='pane ui-layout-north'>");
			$this->insertArray($this->layout["north"]);
			$this->insert("end-north","</div>");
		}
		
		if(isset($this->layout["south"]))
		{
			$this->insert("pre-south","<div class='pane ui-layout-south' style='height:300px'>");
			$this->insertArray($this->layout["south"]);
			$this->insert("end-south","</div>");
		}
		
		if(isset($this->layout["east"]))
		{
			$this->insert("pre-east","<div class='pane ui-layout-east' style='width:300px'>");
			$this->insertArray($this->layout["east"]);
			$this->insert("end-east","</div>");
		}
		
		if(isset($this->layout["west"]))
		{
			$this->insert("pre-west","<div class='pane ui-layout-west' style='width:300px'>");
			$this->insertArray($this->layout["west"]);
			$this->insert("end-west","</div>");
		}
		
		
		
		$this->layoutOptions['applyDefaultStyles']= true;
		if(isset($this->layout["north"]) && !isset($this->layoutOptions['north']['size']))
			$this->layoutOptions['north']['size']="300";
		if(isset($this->layout["south"]) &&!isset($this->layoutOptions['south']['size']))
			$this->layoutOptions['south']['size']="300";
		if(isset($this->layout["west"]) &&!isset($this->layoutOptions['west']['size']))
			$this->layoutOptions['west']['size']="200";
		if(isset($this->layout["east"]) &&!isset($this->layoutOptions['east']['size']))
			$this->layoutOptions['east']['size']="200";
		$this->insert("end","</div>");
		$js = '$(document).ready(function () {
			$("#'.$this->id.'").layout('.json_encode($this->layoutOptions).');
			});';
		$this->addJS($js);
		$this->addJS("jquery.layout.js");
		$this->addCSS("layout-default.css");
		return parent::render();
	}

	
	
}
