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
* Class Name:       Reconciliations_Generations
* File Name:        Reconciliations_Generations.class.php
* Generated:        Tuesday, Feb 24, 2015 - 18:06:46 CET
*  - for Table:     Reconciliations_Generations
*  - in Database:   SiiMobility
* Created by: 
********************************************************************************/

// Files required by class:
// No files required.

// Begin Class "Reconciliations_Generations"
class ReconciliationRep extends modObject//Reconciliation
{
	// Variable declaration
	protected $ID_Reconciliation; // Primary Key
	protected $ID_Generation;
	protected $TripleDate;
	protected $Clone;
	protected $Locked;
	// Class Constructor
	public function __construct() {
		$this->TripleDate="0000-00-00 00:00:00";
		$this->Clone=0;
		$this->Locked=0;
		parent::__construct();
	}
	
	// Class Destructor
	public function __destruct() {
		parent::__destruct();
	}
	
	// GET Functions
	public function getID_Reconciliation() {
		return($this->ID_Reconciliation);
	}
	
	public function getID_Generation() {
		return($this->ID_Generation);
	}
	
	public function getTripleDate() {
		return($this->TripleDate);
	}
	
	public function getClone() {
		return($this->Clone);
	}
	
	public function getLocked() {
		return($this->Locked);
	}
	
	// SET Functions
	public function setID_Reconciliation($mValue) {
		$this->ID_Reconciliation = $mValue;
	}
	
	public function setID_Generation($mValue) {
		$this->ID_Generation = $mValue;
	}
	
	public function setTripleDate($mValue) {
		$this->TripleDate = $mValue;
	}
	
	public function setClone($mValue)
	{
		$this->Clone=$mValue?1:0;
	}
	
	public function setLocked($mValue)
	{
		$this->Locked=$mValue?1:0;
	}
	
	public function select($ID_Reconciliation,$ID_Generation) { // SELECT Function
		// Execute SQL Query to get record.
		$sSQL = "SELECT * FROM Reconciliations_Generations WHERE ID_Reconciliation = '$ID_Reconciliation' AND ID_Generation='$ID_Generation';";
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
		$this->ID_Reconciliation = $oRow->ID_Reconciliation; // Primary Key
		$this->ID_Generation = $oRow->ID_Generation;
		$this->TripleDate = $oRow->TripleDate;
		$this->Clone = $oRow->Clone;
		$this->Locked=$oRow->Locked;
		return true;
	}
	
	public function insert() {
		$sSQL = "INSERT INTO Reconciliations_Generations (`ID_Reconciliation`,`ID_Generation`, `TripleDate`, `Clone`, `Locked`) VALUES ('$this->ID_Reconciliation','$this->ID_Generation', '$this->TripleDate', '$this->Clone', '$this->Locked');";
		$oResult = $this->db->query($sSQL);
	}
	
	function update() {
		$sSQL = "UPDATE Reconciliations_Generations SET ID_Reconciliation = '$this->ID_Reconciliation', `ID_Generation` = '$this->ID_Generation', `TripleDate` = '$this->TripleDate', `Clone` = '$this->Clone', `Locked` = '$this->Locked' WHERE ID_Reconciliation = '$this->ID_Reconciliation' AND ID_Generation='$this->ID_Generation';";
		$oResult = $this->db->query($sSQL);
	}
	
	public function delete() {
		$sSQL = "DELETE FROM Reconciliations_Generations WHERE ID_Reconciliation = '$this->ID_Reconciliation' AND ID_Generation='$this->ID_Generation';";
		$oResult = $this->db->query($sSQL);
	}
	
	public function load($ID_Reconciliation,$mID)
	{
		$this->select($ID_Reconciliation,$mID);
		//parent::select($this->ID_Reconciliation);
	}

}
// End Class "Reconciliations_Generations"
?>