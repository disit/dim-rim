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
* Class Name:       OpenDataRep
* File Name:        OpenDataRep.class.php
* Generated:        Tuesday, Feb 24, 2015 - 18:06:30 CET
*  - for Table:     OpenData_Generations
*  - in Database:   SiiMobility
* Created by: 
********************************************************************************/

// Files required by class:
// No files required.

// Begin Class "OpenDataRep"
class OpenDataRep extends modObject//OpenData
{
	// Variable declaration
	protected $ID_OpenData; // Primary Key
	protected $ID_Generation;
	protected $TripleStart;
	protected $TripleEnd;
	protected $Clone;
	protected $Locked;
	// Class Constructor
	public function __construct() {
		$this->TripleStart="0000-00-00 00:00:00";
		$this->TripleEnd="0000-00-00 00:00:00";
		$this->Clone=0;
		$this->Locked=0;
		parent::__construct();
	}
	
	// Class Destructor
	public function __destruct() {
		parent::__destruct();
	}
	
	// GET Functions
	public function getID_OpenData() {
		return($this->ID_OpenData);
	}
	
	public function getID_Generation() {
		return($this->ID_Generation);
	}
	
	public function getTripleStart() {
		return($this->TripleStart);
	}
	
	public function getTripleEnd() {
		return($this->TripleEnd);
	}
	
	public function getClone() {
		return($this->Clone);
	}
	
	public function getLocked() {
		return($this->Locked);
	}
	
	// SET Functions
	public function setID_OpenData($mValue) {
		$this->ID_OpenData = $mValue;
	}
	
	public function setID_Generation($mValue) {
		$this->ID_Generation = $mValue;
	}
	
	public function setTripleStart($mValue) {
		$this->TripleStart = $mValue;
	}
	
	public function setTripleEnd($mValue) {
		$this->TripleEnd = $mValue;
	}
	
	public function setClone($mValue)
	{
		$this->Clone=$mValue?1:0;
	}
	
	public function setLocked($mValue)
	{
		$this->Locked=$mValue?1:0;
	}
	
	public function select($ID_OpenData,$ID_Generation) { // SELECT Function
		// Execute SQL Query to get record.
		$sSQL = "SELECT * FROM OpenData_Generations WHERE ID_OpenData = '$ID_OpenData' AND ID_Generation='$ID_Generation';";
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
		$this->ID_OpenData = $oRow->ID_OpenData; // Primary Key
		$this->ID_Generation = $oRow->ID_Generation;
		$this->TripleStart = $oRow->TripleStart;
		$this->TripleEnd = $oRow->TripleEnd;
		$this->Clone = $oRow->Clone;
		$this->Locked = $oRow->Locked;
		return true;
	}
	
	public function insert() {
		$sSQL = "INSERT INTO OpenData_Generations (`ID_OpenData`, `ID_Generation`, `TripleStart`, `TripleEnd`, `Clone`, `Locked`) VALUES ('$this->ID_OpenData', '$this->ID_Generation', '$this->TripleStart', '$this->TripleEnd', '$this->Clone', '$this->Locked');";
		$oResult = $this->db->query($sSQL);
		//$this->ID_OpenData = $this->db->getLastInsertedId();
	}
	
	function update() {
		$sSQL = "UPDATE OpenData_Generations SET ID_OpenData = '$this->ID_OpenData', `ID_Generation` = '$this->ID_Generation', `TripleStart` = '$this->TripleStart', `TripleEnd` = '$this->TripleEnd' , `Clone` = '$this->Clone', `Locked` = '$this->Locked' WHERE ID_OpenData = '$this->ID_OpenData' and ID_Generation='$this->ID_Generation';";
		$oResult = $this->db->query($sSQL);
		//sm_Logger::write($sSQL);
	}
	
	public function delete() {
		$sSQL = "DELETE FROM OpenData_Generations WHERE ID_OpenData = '$this->ID_OpenData' and ID_Generation='$this->ID_Generation';";
		$oResult = $this->db->query($sSQL);
	}
	
	public function load($ID_OpenData,$mID)
	{
		$this->select($ID_OpenData,$mID);
		//parent::select($this->ID_OpenData);
	}
	
	
}
// End Class "OpenDataRep"
?>