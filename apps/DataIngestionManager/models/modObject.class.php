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

// Files required by class:
// No files required.

// Begin Class "modObject"

class modObject
{
	protected $db;
	// Class Constructor
	public function __construct() {
		$dbConfig = new MySqlConfig();
		$this->db = $dbConfig->getDatabase();
	}
	
	// Class Destructor
	public function __destruct() {
		unset($this->db);
	}
	
	// Class validator
	public function validate($context=null) {
		return true;
	}
	
	public function setDatabase($db)
	{
		$this->db=$db;
	}
	
	
	public function toArray($on=null){
		return $this->_toArray($on);
	}
	
	protected function _toArray($on=null)
	{
		$prop=array();
		$prop = array_keys(get_class_vars(get_called_class()));
		$prop2 = array_keys(get_class_vars(__CLASS__));
		if($on)
		{
			$parents = class_parents($on);
			if(is_array($parents))
				$prop2 = array_keys(get_class_vars(key($parents)));
		}
			
		if(__CLASS__!=$on)
			$prop = array_diff($prop,$prop2);
		
		$_data = get_object_vars($this);
		$data=array();
		foreach($prop as $p)
			$data[$p]=$_data[$p];
		
		return $data; //Array2XML::createXML("root",$data);
	}
}
//End Class
