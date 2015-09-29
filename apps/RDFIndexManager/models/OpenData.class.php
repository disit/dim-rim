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
* Class Name:       OpenData
* File Name:        OpenData.class.php
* Generated:        Wednesday, Feb 25, 2015 - 11:21:16 CET
*  - for Table:     process_manager2
*  - in Database:   SiiMobility
* Created by: 
********************************************************************************/

// Files required by class:
// No files required.

// Begin Class "OpenData"
class OpenData extends modObject
{
	// Variable declaration
	protected $process; // Primary Key
	protected $Resource;
	protected $Resource_Class;
	protected $Category;
	protected $Format;
	protected $Automaticity;
	protected $Process_type;
	protected $Access;
	protected $Real_time;
	protected $Source;
	protected $A;
	protected $B;
	protected $C;
	protected $D;
	protected $E;
	protected $status_A;
	protected $status_B;
	protected $status_C;
	protected $status_D;
	protected $status_E;
	protected $time_A;
	protected $time_B;
	protected $time_C;
	protected $time_D;
	protected $time_E;
	protected $exec_A;
	protected $exec_B;
	protected $exec_C;
	protected $exec_D;
	protected $exec_E;
	protected $error_A;
	protected $error_B;
	protected $error_C;
	protected $error_D;
	protected $error_E;
	protected $period;
	protected $overtime;
	protected $param;
	protected $last_update;
	protected $last_triples;
	protected $Triples_count;
	protected $Triples_countRepository;
	protected $triples_insertDate;
	protected $error;
	protected $description;
	protected $url_web_disit;
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
	public function getprocess() {
		return($this->process);
	}
	
	public function getResource() {
		return($this->Resource);
	}
	
	public function getResource_Class() {
		return($this->Resource_Class);
	}
	
	public function getCategory() {
		return($this->Category);
	}
	
	public function getFormat() {
		return($this->Format);
	}
	
	public function getAutomaticity() {
		return($this->Automaticity);
	}
	
	public function getProcess_type() {
		return($this->Process_type);
	}
	
	public function getAccess() {
		return($this->Access);
	}
	
	public function getReal_time() {
		return($this->Real_time);
	}
	
	public function getSource() {
		return($this->Source);
	}
	
	public function getA() {
		return($this->A);
	}
	
	public function getB() {
		return($this->B);
	}
	
	public function getC() {
		return($this->C);
	}
	
	public function getD() {
		return($this->D);
	}
	
	public function getE() {
		return($this->E);
	}
	
	public function getstatus_A() {
		return($this->status_A);
	}
	
	public function getstatus_B() {
		return($this->status_B);
	}
	
	public function getstatus_C() {
		return($this->status_C);
	}
	
	public function getstatus_D() {
		return($this->status_D);
	}
	
	public function getstatus_E() {
		return($this->status_E);
	}
	
	public function gettime_A() {
		return($this->time_A);
	}
	
	public function gettime_B() {
		return($this->time_B);
	}
	
	public function gettime_C() {
		return($this->time_C);
	}
	
	public function gettime_D() {
		return($this->time_D);
	}
	
	public function gettime_E() {
		return($this->time_E);
	}
	
	public function getexec_A() {
		return($this->exec_A);
	}
	
	public function getexec_B() {
		return($this->exec_B);
	}
	
	public function getexec_C() {
		return($this->exec_C);
	}
	
	public function getexec_D() {
		return($this->exec_D);
	}
	
	public function getexec_E() {
		return($this->exec_E);
	}
	
	public function geterror_A() {
		return($this->error_A);
	}
	
	public function geterror_B() {
		return($this->error_B);
	}
	
	public function geterror_C() {
		return($this->error_C);
	}
	
	public function geterror_D() {
		return($this->error_D);
	}
	
	public function geterror_E() {
		return($this->error_E);
	}
	
	public function getperiod() {
		return($this->period);
	}
	
	public function getovertime() {
		return($this->overtime);
	}
	
	public function getparam() {
		return($this->param);
	}
	
	public function getlast_update() {
		return($this->last_update);
	}
	
	public function getlast_triples() {
		return($this->last_triples);
	}
	
	public function getTriples_count() {
		return($this->Triples_count);
	}
	
	public function getTriples_countRepository() {
		return($this->Triples_countRepository);
	}
	
	public function gettriples_insertDate() {
		return($this->triples_insertDate);
	}
	
	public function geterror() {
		return($this->error);
	}
	
	public function getdescription() {
		return($this->description);
	}
	
	public function geturl_web_disit() {
		return($this->url_web_disit);
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
	public function setprocess($mValue) {
		$this->process = $mValue;
	}
	
	public function setResource($mValue) {
		$this->Resource = $mValue;
	}
	
	public function setResource_Class($mValue) {
		$this->Resource_Class = $mValue;
	}
	
	public function setCategory($mValue) {
		$this->Category = $mValue;
	}
	
	public function setFormat($mValue) {
		$this->Format = $mValue;
	}
	
	public function setAutomaticity($mValue) {
		$this->Automaticity = $mValue;
	}
	
	public function setProcess_type($mValue) {
		$this->Process_type = $mValue;
	}
	
	public function setAccess($mValue) {
		$this->Access = $mValue;
	}
	
	public function setReal_time($mValue) {
		$this->Real_time = $mValue;
	}
	
	public function setSource($mValue) {
		$this->Source = $mValue;
	}
	
	public function setA($mValue) {
		$this->A = $mValue;
	}
	
	public function setB($mValue) {
		$this->B = $mValue;
	}
	
	public function setC($mValue) {
		$this->C = $mValue;
	}
	
	public function setD($mValue) {
		$this->D = $mValue;
	}
	
	public function setE($mValue) {
		$this->E = $mValue;
	}
	
	public function setstatus_A($mValue) {
		$this->status_A = $mValue;
	}
	
	public function setstatus_B($mValue) {
		$this->status_B = $mValue;
	}
	
	public function setstatus_C($mValue) {
		$this->status_C = $mValue;
	}
	
	public function setstatus_D($mValue) {
		$this->status_D = $mValue;
	}
	
	public function setstatus_E($mValue) {
		$this->status_E = $mValue;
	}
	
	public function settime_A($mValue) {
		$this->time_A = $mValue;
	}
	
	public function settime_B($mValue) {
		$this->time_B = $mValue;
	}
	
	public function settime_C($mValue) {
		$this->time_C = $mValue;
	}
	
	public function settime_D($mValue) {
		$this->time_D = $mValue;
	}
	
	public function settime_E($mValue) {
		$this->time_E = $mValue;
	}
	
	public function setexec_A($mValue) {
		$this->exec_A = $mValue;
	}
	
	public function setexec_B($mValue) {
		$this->exec_B = $mValue;
	}
	
	public function setexec_C($mValue) {
		$this->exec_C = $mValue;
	}
	
	public function setexec_D($mValue) {
		$this->exec_D = $mValue;
	}
	
	public function setexec_E($mValue) {
		$this->exec_E = $mValue;
	}
	
	public function seterror_A($mValue) {
		$this->error_A = $mValue;
	}
	
	public function seterror_B($mValue) {
		$this->error_B = $mValue;
	}
	
	public function seterror_C($mValue) {
		$this->error_C = $mValue;
	}
	
	public function seterror_D($mValue) {
		$this->error_D = $mValue;
	}
	
	public function seterror_E($mValue) {
		$this->error_E = $mValue;
	}
	
	public function setperiod($mValue) {
		$this->period = $mValue;
	}
	
	public function setovertime($mValue) {
		$this->overtime = $mValue;
	}
	
	public function setparam($mValue) {
		$this->param = $mValue;
	}
	
	public function setlast_update($mValue) {
		$this->last_update = $mValue;
	}
	
	public function setlast_triples($mValue) {
		$this->last_triples = $mValue;
	}
	
	public function setTriples_count($mValue) {
		$this->Triples_count = $mValue;
	}
	
	public function setTriples_countRepository($mValue) {
		$this->Triples_countRepository = $mValue;
	}
	
	public function settriples_insertDate($mValue) {
		$this->triples_insertDate = $mValue;
	}
	
	public function seterror($mValue) {
		$this->error = $mValue;
	}
	
	public function setdescription($mValue) {
		$this->description = $mValue;
	}
	
	public function seturl_web_disit($mValue) {
		$this->url_web_disit = $mValue;
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
		$sSQL = "SELECT * FROM process_manager2 WHERE process = '$mID';";
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
		$this->process = $oRow->process; // Primary Key
		$this->Resource = $oRow->Resource;
		$this->Resource_Class = $oRow->Resource_Class;
		$this->Category = $oRow->Category;
		$this->Format = $oRow->Format;
		$this->Automaticity = $oRow->Automaticity;
		$this->Process_type = $oRow->Process_type;
		$this->Access = $oRow->Access;
		$this->Real_time = $oRow->Real_time;
		$this->Source = $oRow->Source;
		$this->A = $oRow->A;
		$this->B = $oRow->B;
		$this->C = $oRow->C;
		$this->D = $oRow->D;
		$this->E = $oRow->E;
		$this->status_A = $oRow->status_A;
		$this->status_B = $oRow->status_B;
		$this->status_C = $oRow->status_C;
		$this->status_D = $oRow->status_D;
		$this->status_E = $oRow->status_E;
		$this->time_A = $oRow->time_A;
		$this->time_B = $oRow->time_B;
		$this->time_C = $oRow->time_C;
		$this->time_D = $oRow->time_D;
		$this->time_E = $oRow->time_E;
		$this->exec_A = $oRow->exec_A;
		$this->exec_B = $oRow->exec_B;
		$this->exec_C = $oRow->exec_C;
		$this->exec_D = $oRow->exec_D;
		$this->exec_E = $oRow->exec_E;
		$this->error_A = $oRow->error_A;
		$this->error_B = $oRow->error_B;
		$this->error_C = $oRow->error_C;
		$this->error_D = $oRow->error_D;
		$this->error_E = $oRow->error_E;
		$this->period = $oRow->period;
		$this->overtime = $oRow->overtime;
		$this->param = $oRow->param;
		$this->last_update = $oRow->last_update;
		$this->last_triples = $oRow->last_triples;
		$this->Triples_count = $oRow->Triples_count;
		$this->Triples_countRepository = $oRow->Triples_countRepository;
		$this->triples_insertDate = $oRow->triples_insertDate;
		$this->error = $oRow->error;
		$this->description = $oRow->description;
		$this->url_web_disit = $oRow->url_web_disit; 
		$this->SecurityLevel = $oRow->SecurityLevel;
		$this->LicenseUrl = $oRow->LicenseUrl;
		$this->LicenseText = $oRow->LicenseText;
		return true;
	}
	
	public function insert() {
		$this->process = NULL; // Remove primary key value for insert
		$sSQL = "INSERT INTO process_manager2 (`Resource`, `Resource_Class`, `Category`, `Format`, `Automaticity`, `Process_type`, `Access`, `Real_time`, `Source`, `A`, `B`, `C`, `D`, `E`, `status_A`, `status_B`, `status_C`, `status_D`, `status_E`, `time_A`, `time_B`, `time_C`, `time_D`, `time_E`, `exec_A`, `exec_B`, `exec_C`, `exec_D`, `exec_E`, `error_A`, `error_B`, `error_C`, `error_D`, `error_E`, `period`, `overtime`, `param`, `last_update`, `last_triples`, `Triples_count`, `Triples_countRepository`, `triples_insertDate`, `error`, `description`, `url_web_disit`,`SecurityLevel`,`LicenseUrl`,`LicenseText`) VALUES ('$this->Resource', '$this->Resource_Class', '$this->Category', '$this->Format', '$this->Automaticity', '$this->Process_type', '$this->Access', '$this->Real_time', '$this->Source', '$this->A', '$this->B', '$this->C', '$this->D', '$this->E', '$this->status_A', '$this->status_B', '$this->status_C', '$this->status_D', '$this->status_E', '$this->time_A', '$this->time_B', '$this->time_C', '$this->time_D', '$this->time_E', '$this->exec_A', '$this->exec_B', '$this->exec_C', '$this->exec_D', '$this->exec_E', '$this->error_A', '$this->error_B', '$this->error_C', '$this->error_D', '$this->error_E', '$this->period', '$this->overtime', '$this->param', '$this->last_update', '$this->last_triples', '$this->Triples_count', '$this->Triples_countRepository', '$this->triples_insertDate', '$this->error', '$this->description', '$this->url_web_disit','$this->SecurityLevel','$this->LicenseUrl' ,'$this->LicenseText');";
		$oResult = $this->db->query($sSQL);
		$this->process = $this->db->getLastInsertedId();
	}
	
	function update($mID) {
		$sSQL = "UPDATE process_manager2 SET process = '$this->process', `Resource` = '$this->Resource', `Resource_Class` = '$this->Resource_Class', `Category` = '$this->Category', `Format` = '$this->Format', `Automaticity` = '$this->Automaticity', `Process_type` = '$this->Process_type', `Access` = '$this->Access', `Real_time` = '$this->Real_time', `Source` = '$this->Source', `A` = '$this->A', `B` = '$this->B', `C` = '$this->C', `D` = '$this->D', `E` = '$this->E', `status_A` = '$this->status_A', `status_B` = '$this->status_B', `status_C` = '$this->status_C', `status_D` = '$this->status_D', `status_E` = '$this->status_E', `time_A` = '$this->time_A', `time_B` = '$this->time_B', `time_C` = '$this->time_C', `time_D` = '$this->time_D', `time_E` = '$this->time_E', `exec_A` = '$this->exec_A', `exec_B` = '$this->exec_B', `exec_C` = '$this->exec_C', `exec_D` = '$this->exec_D', `exec_E` = '$this->exec_E', `error_A` = '$this->error_A', `error_B` = '$this->error_B', `error_C` = '$this->error_C', `error_D` = '$this->error_D', `error_E` = '$this->error_E', `period` = '$this->period', `overtime` = '$this->overtime', `param` = '$this->param', `last_update` = '$this->last_update', `last_triples` = '$this->last_triples', `Triples_count` = '$this->Triples_count', `Triples_countRepository` = '$this->Triples_countRepository', `triples_insertDate` = '$this->triples_insertDate', `error` = '$this->error', `description` = '$this->description', `url_web_disit` = '$this->url_web_disit', `SecurityLevel` = '$this->SecurityLevel' , `LicenseUrl` = '$this->LicenseUrl', `LicenseText` = '$this->LicenseText' WHERE process = '$mID';";
		$oResult = $this->db->query($sSQL);
	}
	
	public function delete($mID) {
		$sSQL = "DELETE FROM process_manager2 WHERE process = '$mID';";
		$oResult = $this->db->query($sSQL);
	}

}
// End Class "OpenData"
?>