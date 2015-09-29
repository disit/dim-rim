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
* Class Name:       Ontology
* File Name:        Ontology.class.php
* Generated:        Wednesday, Feb 25, 2015 - 11:34:51 CET
*  - for Table:     ontologies
*  - in Database:   SiiMobility
* Created by: 
********************************************************************************/

// Files required by class:
// No files required.

// Begin Class "Ontology"
class Ontology extends modObject
{
	// Variable declaration
	protected $Name; // Primary Key
	protected $URIPrefix;
	protected $SecurityLevel;
	protected $LicenseUrl;
	protected $LicenseText;
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
	
	public function getURIPrefix() {
		return($this->URIPrefix);
	}
	
	public function getSecurityLevel() {
		return($this->SecurityLevel);
	}
	
	public function getLicenseUrl() {
		return($this->LicenseUrl);
	}
	
	public function getLicenseText() {
		return($this->LicenseText);
	}
	
	// SET Functions
	public function setName($mValue) {
		$this->Name = $mValue;
	}
	
	public function setURIPrefix($mValue) {
		$this->URIPrefix = $mValue;
	}
	
	public function setSecurityLevel($mValue) {
		$this->SecurityLevel = $mValue;
	}
	
	public function setLicenseUrl($mValue) {
		$this->LicenseUrl = $mValue;
	}
	
	public function setLicenseText($mValue) {
		$this->LicenseText = $mValue;
	}
	
	public function select($mID) { // SELECT Function
		// Execute SQL Query to get record.
		$sSQL = "SELECT * FROM ontologies WHERE Name = '$mID';";
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
		$this->URIPrefix = $oRow->URIPrefix;
		$this->SecurityLevel = $oRow->SecurityLevel;
		$this->LicenseUrl = $oRow->LicenseUrl;
		$this->LicenseText = $oRow->LicenseText;
		return true;
	}
	
	public function insert() {
		//$this->Name = NULL; // Remove primary key value for insert
		$sSQL = "INSERT INTO ontologies (`Name`,`URIPrefix`,`SecurityLevel`, `LicenseUrl` , `LicenseText`) VALUES ('$this->Name','$this->URIPrefix','$this->SecurityLevel','$this->LicenseUrl','$this->LicenseText');";
		$oResult = $this->db->query($sSQL);
		return $oResult; //$this->Name = $this->db->getLastInsertedId();
	}
	
	function update($mID) {
		$sSQL = "UPDATE ontologies SET Name = '$this->Name', `URIPrefix` = '$this->URIPrefix', `SecurityLevel` = '$this->SecurityLevel', `LicenseUrl` = '$this->LicenseUrl', `LicenseText` = '$this->LicenseText' WHERE Name = '$mID';";
		$oResult = $this->db->query($sSQL);
	}
	
	public function delete($mID) {
		$sSQL = "DELETE FROM ontologies WHERE Name = '$mID';";
		$oResult = $this->db->query($sSQL);
		return $oResult;
	}

}
// End Class "Ontology"
?>