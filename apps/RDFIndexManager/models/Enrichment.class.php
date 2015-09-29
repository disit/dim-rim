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

/*******************************************************************************
* Class Name:       Enrichment
* File Name:        Enrichment.class.php
* Generated:        Friday, Jul 31, 2015 - 12:39:18 CEST
*  - for Table:     enrichments
*  - in Database:   rim
* Created by: 
********************************************************************************/

// Files required by class:
// No files required.

// Begin Class "Enrichment"
class Enrichment extends modObject
{
	// Variable declaration
	protected $Name; // Primary Key
	protected $Query;
	protected $Description;
	protected $Type;
	// Class Constructor
	public function __construct() {
		$this->Type="";
		$this->Query="";
		$this->Description="";
		parent::__construct();
	}
	
	// Class Destructor
	public function __destruct() {
		parent::__destruct();
	}
	
	// GET Functions
	public function getName() {
		return($this->Name);
	}
	
	public function getQuery() {
		return($this->Query);
	}
	
	public function getDescription() {
		return($this->Description);
	}
	
	public function getType() {
		return($this->Type);
	}
	
	
	// SET Functions
	public function setName($mValue) {
		$this->Name = $mValue;
	}
	
	public function setQuery($mValue) {
		$this->Query = $mValue;
	}
	
	public function setDescription($mValue) {
		$this->Description = $mValue;
	}
	
	public function setType($mValue) {
		$this->Type = $mValue;
	}
	
	public function select($mID) { // SELECT Function
		// Execute SQL Query to get record.
		$sSQL = "SELECT * FROM enrichments WHERE Name = '$mID';";
		$oResult = $this->db->query($sSQL);
		$oRow=null;
		if ($oResult) {
			$oRow =(object)$oResult[0];
		}
		else {
			$err=$this->db->getError();
			if ($err!=""){
				trigger_error($err);
			}
			return false;
		}
		// Assign results to class.
		$this->Name = $oRow->Name; // Primary Key
		$this->Query = $oRow->Query;
		$this->Description = $oRow->Description;
		$this->Type = $oRow->Type;
		return true;
	}
	
	public function insert() {
		$oResult = NULL; // Remove primary key value for insert
		$values['Name'] = $this->Name; // Primary Key
		$values['Query'] = $this->Query ;
		$values['Description'] = $this->Description;
		$values['Type'] = $this->Type;
		//$sSQL = "INSERT INTO enrichments (`Name`, `Query`, `Description`) VALUES ('$this->Name', '$this->Query', '$this->Description');";
		$oResult = $this->db->save('enrichments',$values);
		
		return $oResult != NULL;
	}
	
	function update() {
		$where['Name'] = $this->Name;
		$oResult = NULL;
	//	$sSQL = "UPDATE enrichments SET (Name = '$this->Name', `Query` = '$this->Query', `Description` = '$this->Description') WHERE Name = '$mID';";
		$values['Name'] = $this->Name; // Primary Key
		$values['Query'] = $this->Query ;
		$values['Description'] = $this->Description;
		$values['Type'] = $this->Type;
		$oResult = $this->db->save('enrichments',$values,$where);
		return $oResult != NULL;
	}
	
	public function delete($mID) {
		$oResult = NULL;
		$where['Name'] =$mID;
		$oResult = $this->db->delete('enrichments',$where);
		return $oResult != NULL;
	}

}
// End Class "Enrichment"
?>