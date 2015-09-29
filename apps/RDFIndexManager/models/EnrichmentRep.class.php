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
* Class Name:       EnrichmentRep
* File Name:        EnrichmentRep.class.php
* Generated:        Friday, Jul 31, 2015 - 12:39:38 CEST
*  - for Table:     enrichments_generations
*  - in Database:   rim
* Created by: 
********************************************************************************/

// Files required by class:
// No files required.

// Begin Class "EnrichmentRep"
class EnrichmentRep extends modObject
{
	// Variable declaration
	protected $ID_Generation; // Primary Key
	protected $ID_Enrichment;
	protected $Clone;
	protected $Locked;
	// Class Constructor
	public function __construct() {
		$this->Clone=0;
		$this->Locked=0;
		parent::__construct();
	}
	
	// Class Destructor
	public function __destruct() {
		parent::__destruct();
	}
	
	// GET Functions
	public function getID_Generation() {
		return($this->ID_Generation);
	}
	
	public function getID_Enrichment() {
		return($this->ID_Enrichment);
	}
	
	public function getClone() {
		return($this->Clone);
	}
	
	public function getLocked() {
		return($this->Locked);
	}
	
	// SET Functions
	public function setID_Generation($mValue) {
		$this->ID_Generation = $mValue;
	}
	
	public function setID_Enrichment($mValue) {
		$this->ID_Enrichment = $mValue;
	}
	
	public function setClone($mValue) {
		$this->Clone = $mValue?1:0;;
	}
	
	public function setLocked($mValue) {
		$this->Locked = $mValue?1:0;;
	}
	
	public function select($ID_Enrichment,$ID_Generation) { // SELECT Function
		// Execute SQL Query to get record.
		$sSQL = "SELECT * FROM enrichments_generations WHERE ID_Enrichment='$ID_Enrichment' AND ID_Generation = '$ID_Generation'";
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
		$this->ID_Generation = $oRow->ID_Generation; // Primary Key
		$this->ID_Enrichment = $oRow->ID_Enrichment;
		$this->Clone = $oRow->Clone;
		$this->Locked = $oRow->Locked;
		return true;
	}
	
	public function insert() {
		$oResult  = NULL; // Remove primary key value for insert
		$sSQL = "INSERT INTO enrichments_generations (`ID_Generation`, `ID_Enrichment`, `Clone`, `Locked`) VALUES ('$this->ID_Generation', '$this->ID_Enrichment', '$this->Clone', '$this->Locked');";
		$oResult = $this->db->query($sSQL);
		
		return $oResult != NULL;
	}
	
	function update() {
		$oResult = NULL;
		$sSQL = "UPDATE enrichments_generations SET `ID_Generation` = '$this->ID_Generation', `ID_Enrichment` = '$this->ID_Enrichment', `Clone` = '$this->Clone', `Locked` = '$this->Locked' WHERE ID_Generation = '$this->ID_Generation' AND ID_Enrichment  = '$this->ID_Enrichment';";
		$oResult = $this->db->query($sSQL);
		return $oResult != NULL;
	}
	
	public function delete() {
		$oResult = NULL;
		$sSQL = "DELETE FROM enrichments_generations WHERE ID_Generation = '$this->ID_Generation' AND ID_Enrichment  = '$this->ID_Enrichment' ;";
		$oResult = $this->db->query($sSQL);
		return $oResult != NULL;
	}

}
// End Class "EnrichmentRep"
?>