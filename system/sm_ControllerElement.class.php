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

class sm_ControllerElement implements sm_Module
{
	//reference to the ViewElement
	protected $view;
	//Current action/operation
	protected $op;
	
	protected $model;
	
	
	function __construct()
	{
		$this->view=null;
		$this->op=null;
		$this->model=null;
	}
	
	public function setOp($op=null)
	{
		$this->op=$op;
	}
	
	public function getOp()
	{
		return $this->op;
	}
	
	public function getModel()
	{
		return $this->model;
	}
	
	public function getView()
	{
		return $this->view;
	}
	
	public function setView(sm_ViewElement $view = null)
	{
		$this->view=$view;
	}
	
	/* STATIC METHOD as Hook */
	
	static function install($db)
	{
		return true;
	}
	
	static function uninstall($db)
	{
		return true;
	}
		
}