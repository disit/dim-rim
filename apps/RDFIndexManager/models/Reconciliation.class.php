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
* Class Name:       Reconciliation
* File Name:        Reconciliation.class.php
* Generated:        Wednesday, Feb 25, 2015 - 11:21:48 CET
*  - for Table:     Reconciliations
*  - in Database:   SiiMobility
* Created by: 
********************************************************************************/

// Files required by class:
// No files required.

// Begin Class "Reconciliation"
class Reconciliation extends modObject
{
	// Variable declaration
	protected $Name; // Primary Key
	protected $Macroclasses;
	protected $Triples;
	protected $Description;
	protected $SecurityLevel;
	// Class Constructor
	public function __construct() {
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
	
	public function getMacroclasses() {
		return($this->Macroclasses);
	}
	
	public function getTriples() {
		return($this->Triples);
	}
	
	public function getDescription() {
		return($this->Description);
	}
	
	public function getSecurityLevel() {
		return($this->SecurityLevel);
	}
	
	// SET Functions
	public function setName($mValue) {
		$this->Name = $mValue;
	}
	
	public function setMacroclasses($mValue) {
		$this->Macroclasses = $mValue;
	}
	
	public function setTriples($mValue) {
		$this->Triples = $mValue;
	}
	
	public function setDescription($mValue) {
		$this->Description = $mValue;
	}
	
	public function setSecurityLevel($mValue) {
		$this->SecurityLevel = $mValue;
	}
	
	public function select($mID) { // SELECT Function
		// Execute SQL Query to get record.
		$sSQL = "SELECT * FROM Reconciliations WHERE Name = '$mID';";
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
		$this->Macroclasses = $oRow->Macroclasses;
		$this->Triples = $oRow->Triples;
		$this->Description = $oRow->Description;
		$this->SecurityLevel = $oRow->SecurityLevel;
		return true;
	}
	
	public function insert() {
		//$this->Name = NULL; // Remove primary key value for insert
		$sSQL = "INSERT INTO Reconciliations (`Name`,`Macroclasses`, `Triples`, `Description`, `SecurityLevel`) VALUES ('$this->Name','$this->Macroclasses', '$this->Triples', '$this->Description','$this->SecurityLevel');";
		$oResult = $this->db->query($sSQL);
		$id = $this->db->getLastInsertedId();
		return  isset($id);
	}
	
	function update() {
		$oResult=null;
		$sSQL = "UPDATE Reconciliations SET Name = '$this->Name', `Macroclasses` = '$this->Macroclasses', `Triples` = '$this->Triples', `Description` = '$this->Description', `SecurityLevel` = '$this->SecurityLevel' WHERE Name = '$this->Name';";
		$oResult = $this->db->query($sSQL);
		return isset($oResult);
	}
	
	public function delete($mID) {
		$oResult=null;
		$sSQL = "DELETE FROM Reconciliations WHERE Name = '$mID';";
		$oResult = $this->db->query($sSQL);
		return isset($oResult);
	}

}
// End Class "Reconciliation"
?>