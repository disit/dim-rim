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

class DataCollection extends Collection
{
	protected $db;
	function __construct()
	{
		$dbConfig = new MySqlConfig();
		$this->db = $dbConfig->getDatabase();
	}
	
	function toArray($on=null)
	{
		$data=array();
		foreach ($this as $k=>$v)
			$data[]=$v->toArray($on);
		return $data;
	}
	
	function save(){
		foreach ($this as $k=>$v)
			$v->insert();
	}
	
	function update(){
		foreach ($this as $k=>$v)
			$v->update();
	}
	
	
	function _commit($key=null){
		if($key)
			$this->_commitElement($key);
		else
			$this->_commitAll();
	}
	
	function _lock($key=null){
		if($key)
			$this->_lockElement($key);
		else
			$this->_lockAll();
	}
	
	function _unlock($key=null){
	
		if($key)
			$this->_unlockElement($key);
		else
			$this->_unlockAll();
	}
	
	function _commitElement($key)
	{
		$this->_lockElement($key);
	}
	
	function _commitAll()
	{
		$this->_lockAll();
	}
	
	function _lockElement($key){
	
		
	
	}
	
	function _lockAll(){
		
	}
	
	function _unlockElement($key){
	
	
	
	}
	
	protected function _unlockAll(){
	
	}
}