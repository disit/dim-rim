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
* Class Name:       Generation
* File Name:        Generation.class.php
* Generated:        Tuesday, Feb 24, 2015 - 13:39:21 CET
*  - for Table:     Generations
*  - in Database:   SiiMobility
* Created by: 
********************************************************************************/

// Files required by class:
// No files required.

// Begin Class "Generation"
class Generation extends modObject
{
	// Variable declaration
	protected $ID; // Primary Key
	protected $SessionStart;
	protected $SessionEnd;
	protected $SessionIP;
	protected $GenerationStart;
	protected $GenerationEnd;
	protected $ScriptPath;
	protected $RepositoryID;
	protected $Description;
	protected $ParentID;
	protected $Type;
	protected $Version;
	protected $SubVersion;
	protected $SecurityLevel;
	// Class Constructor
	public function __construct() {
		$this->reset();
		parent::__construct();
	}
	
	// Class Destructor
	public function __destruct() {
		parent::__destruct();
	}
	
	protected function reset()
	{
		$this->SessionStart=date("Y-m-d H:i:s");
		$this->SessionEnd="0000-00-00 00:00:00";
		$this->GenerationStart="0000-00-00 00:00:00";
		$this->GenerationEnd="0000-00-00 00:00:00";
		$this->ScriptPath="";
		$this->Description="";
		$this->ParentID="";
		$this->SessionIP = $_SERVER["REMOTE_ADDR"];
		$this->Type="";
		$this->Version=0;
		$this->SubVersion=0;
		$this->SecurityLevel=3;
	}
	
	// GET Functions
	public function getID() {
		return($this->ID);
	}
	
	public function getSessionStart() {
		return($this->SessionStart);
	}
	
	public function getSessionEnd() {
		return($this->SessionEnd);
	}
	
	public function getSessionIP() {
		return($this->SessionIP);
	}
	
	public function getGenerationStart() {
		return($this->GenerationStart);
	}
	
	public function getGenerationEnd() {
		return($this->GenerationEnd);
	}
	
	public function getScriptPath() {
		return($this->ScriptPath);
	}
	
	public function getRepositoryID() {
		return($this->RepositoryID);
	}
	
	public function getDescription() {
		return($this->Description);
	}
	
	public function getParentID() {
		return($this->ParentID);
	}
	
	public function getType(){
		return($this->Type);
	}
	
	public function getVersion(){
		return($this->Version);
	}
	
	public function getSubVersion(){
		return($this->SubVersion);
	}
	
	public function getSecurityLevel(){
		return($this->SecurityLevel);
	}
	
	// SET Functions
	public function setID($mValue) {
		$this->ID = $mValue;
	}
	
	public function setSessionStart($mValue) {
		$this->SessionStart = $mValue;
	}
	
	public function setSessionEnd($mValue) {
		$this->SessionEnd = $mValue;
	}
	
	public function setSessionIP($mValue) {
		$this->SessionIP = $mValue;
	}
	
	public function setGenerationStart($mValue) {
		$this->GenerationStart = $mValue;
	}
	
	public function setGenerationEnd($mValue) {
		$this->GenerationEnd = $mValue;
	}
	
	public function setScriptPath($mValue) {
		$this->ScriptPath = $mValue;
	}
	
	public function setRepositoryID($mValue) {
		$this->RepositoryID = $mValue;
	}
	
	public function setDescription($mValue) {
		$this->Description= $mValue;
	}
	
	public function setParentID($mValue) {
		$this->ParentID= $mValue;
	}
	
	public function setType($mValue){
		$this->Type= $mValue;
	}
	
	public function setVersion($mValue){
		$this->Version= $mValue;
	}
	
	public function setSubVersion($mValue){
		$this->SubVersion= $mValue;
	}
	
	public function setSecurityLevel($mValue){
		$this->SecurityLevel= $mValue;
	}
	
	public function select($mID) { // SELECT Function
		// Execute SQL Query to get record.
		$sSQL = "SELECT * FROM Generations WHERE ID = $mID;";
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
		$this->ID = $oRow->ID; // Primary Key
		$this->SessionStart = $oRow->SessionStart;
		$this->SessionEnd = $oRow->SessionEnd;
		$this->SessionIP = $oRow->SessionIP;
		$this->GenerationStart = $oRow->GenerationStart;
		$this->GenerationEnd = $oRow->GenerationEnd;
		$this->ScriptPath = $oRow->ScriptPath;
		$this->RepositoryID = $oRow->RepositoryID;
		$this->Description = $oRow->Description;
		$this->ParentID = $oRow->ParentID;
		$this->Type = $oRow->Type;
		$this->Version = $oRow->Version;
	//	$this->SubVersion = $oRow->SubVersion;
		$this->SecurityLevel = $oRow->SecurityLevel;
		return true;
	}
	
	public function insert() {
		$oResult = null;
		$this->ID = NULL; // Remove primary key value for insert
		$data['SessionStart'] = $this->SessionStart;
		$data['SessionEnd'] = $this->SessionEnd;
		$data['SessionIP'] = $this->SessionIP;
		$data['GenerationStart'] = $this->GenerationStart;
		$data['GenerationEnd'] = $this->GenerationEnd;
		$data['ScriptPath'] = $this->ScriptPath;
		$data['RepositoryID'] = $this->RepositoryID;
		$data['Description'] = $this->Description;
		$data['ParentID'] = $this->ParentID;
		$data['Type'] = $this->Type;
		$data['Version'] = $this->Version;
//		$data['SubVersion'] = $this->SubVersion;
		$data['SecurityLevel'] = $this->SecurityLevel;
		//$description = $this->db->escapeValue($this->Description);
	//	$sSQL = "INSERT INTO Generations (`SessionStart`, `SessionEnd`, `SessionIP`, `GenerationStart`, `GenerationEnd`, `ScriptPath`, `RepositoryID`, `Description`, `ParentID`, `Type`, `Version`, `SecurityLevel`) VALUES ('$this->SessionStart', '$this->SessionEnd', '$this->SessionIP', '$this->GenerationStart', '$this->GenerationEnd', '$this->ScriptPath', '$this->RepositoryID', '$description', '$this->ParentID', '$this->Type', '$this->Version', '$this->SecurityLevel');";
		$oResult = $this->db->save("Generations", $data);
		//$oResult = $this->db->query($sSQL);
		$this->ID = $this->db->getLastInsertedId();
		return $oResult;
	}
	
	function update() {
		$oResult = null;
		$data['SessionStart'] = $this->SessionStart;
		$data['SessionEnd'] = $this->SessionEnd;
		$data['SessionIP'] = $this->SessionIP;
		$data['GenerationStart'] = $this->GenerationStart;
		$data['GenerationEnd'] = $this->GenerationEnd;
		$data['ScriptPath'] = $this->ScriptPath;
		$data['RepositoryID'] = $this->RepositoryID;
		$data['Description'] = $this->Description;
		$data['ParentID'] = $this->ParentID;
		$data['Type'] = $this->Type;
		$data['Version'] = $this->Version;
//		$data['SubVersion'] = $this->SubVersion;
		$data['SecurityLevel'] = $this->SecurityLevel;
		//$description = $this->db->escapeValue($this->Description);
		//$sSQL = "UPDATE Generations SET `SessionStart` = '$this->SessionStart', `SessionEnd` = '$this->SessionEnd', `SessionIP` = '$this->SessionIP', `GenerationStart` = '$this->GenerationStart', `GenerationEnd` = '$this->GenerationEnd', `ScriptPath` = '$this->ScriptPath', `RepositoryID` = '$this->RepositoryID', `Description` = '$description', `ParentID` = '$this->ParentID' , `Type` = '$this->Type', `Version` ='$this->Version', `SecurityLevel` = '$this->SecurityLevel' WHERE ID = $this->ID;";
		$where=array("ID" => $this->ID);
		$oResult = $this->db->save("Generations", $data, $where);
		//$oResult = $this->db->query($sSQL);
		return $oResult;
	}
	
	public function delete($mID) {
		$oResult = null;
		//$sSQL = "DELETE FROM Generations WHERE ID = $mID;";
		$where['ID']=$mID;
		$oResult = $this->db->delete("Generations",$where); //$this->db->query($sSQL);
		return $oResult;
	}
	
	function deleteScript(){
		$script = "/".$this->getScriptPath(); sm_Logger::write($script);
		if($script!="/" && is_file($script) && file_exists($script))
		{
			sm_Logger::write($script);
			unlink($script);
		}
	}
	
	public function fill($dom)
	{
		if($dom->getElementsByTagName('SessionStart')->item(0))
			$this->SessionStart= $dom->getElementsByTagName('SessionStart')->item(0)->nodeValue;
		if($dom->getElementsByTagName('SessionEnd')->item(0))
			$this->SessionEnd=$dom->getElementsByTagName('SessionEnd')->item(0)->nodeValue;
		if($dom->getElementsByTagName('SessionIP')->item(0))
			$this->SessionIP=$dom->getElementsByTagName('SessionIP')->item(0)->nodeValue;
		if($dom->getElementsByTagName('GenerationStart')->item(0))
			$this->GenerationStart=$dom->getElementsByTagName('GenerationStart')->item(0)->nodeValue;
		if($dom->getElementsByTagName('GenerationEnd')->item(0))
			$this->GenerationEnd=$dom->getElementsByTagName('GenerationEnd')->item(0)->nodeValue;
		if($dom->getElementsByTagName('ScriptPath')->item(0))
			$this->ScriptPath=$dom->getElementsByTagName('ScriptPath')->item(0)->nodeValue;
		if($dom->getElementsByTagName('RepositoryID')->item(0))
			$this->RepositoryID=$dom->getElementsByTagName('RepositoryID')->item(0)->nodeValue;
		if($dom->getElementsByTagName('Description')->item(0))
			$this->Description=$dom->getElementsByTagName('Description')->item(0)->nodeValue;
		if($dom->getElementsByTagName('ParentID')->item(0))
			$this->ParentID=$dom->getElementsByTagName('ParentID')->item(0)->nodeValue;
		if($dom->getElementsByTagName('Type')->item(0))
			$this->Type=$dom->getElementsByTagName('Type')->item(0)->nodeValue;
		if($dom->getElementsByTagName('Version')->item(0))
			$this->Version=$dom->getElementsByTagName('Version')->item(0)->nodeValue;
		if($dom->getElementsByTagName('SubVersion')->item(0))
			$this->SubVersion=$dom->getElementsByTagName('SubVersion')->item(0)->nodeValue;
		if($dom->getElementsByTagName('SecurityLevel')->item(0))
			$this->SecurityLevel=$dom->getElementsByTagName('SecurityLevel')->item(0)->nodeValue;
	}
}
// End Class "Generation"
?>