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

class SiiMobilityRepository extends Generation
{
	const ONTOLOGIESDATA = 0;
	const STATICDATA = 1;
	const REALTIMEDATA = 2;
	const RECONCILIATIONSDATA = 3;
	const ENRICHMENTSDATA = 4;
	
	private $ontologies;
	private $realtimedata;
	private $staticdata;
	private $reconciliations;
	private $enrichments;
	
	function __construct($mID=null){
		parent::__construct();
		$this->ontologies = new OntologiesData($this);
		$this->realtimedata = new RealTimeData($this);
		$this->staticdata = new StaticData($this);
		$this->reconciliations = new ReconciliationsData($this);
		$this->enrichments = new EnrichmentsData($this);
		$this->mID=$mID;
		if($mID)
			$this->load($mID);
	}
	
	function load($mID)
	{
		if($mID)
		{
			$this->mID=$mID;
			$this->select($mID);			
			$this->ontologies->load($mID);		
			$this->realtimedata->load($mID);		
			$this->staticdata->load($mID);			
			$this->reconciliations->load($mID);
			$this->enrichments->load($mID);
		}
	}
	
	function toXMLString()
	{

		$data['SiiMobilityIndex']= $this->toArray();
		$ont = $this->ontologies->toArray();
		$data['SiiMobilityIndex']['ontologies']=$ont?array('ontology'=>$ont):array();
		$rt = $this->realtimedata->toArray('OpenDataRep');
		$data['SiiMobilityIndex']['realtimedata']=$rt?array('opendatart'=>$rt):array();
		$st = $this->staticdata->toArray('OpenDataRep');
		$data['SiiMobilityIndex']['staticdata']=$st?array('opendata'=>$st):array();
		$rec=$this->reconciliations->toArray();
		$data['SiiMobilityIndex']['reconciliations']=$rec?array('reconciliation'=>$rec):array();
		$enrichments=$this->enrichments->toArray();
		$data['SiiMobilityIndex']['enrichments']=$rec?array('enrichment'=>$enrichments):array();
		
		return Array2XML::createXML("SiiMobilityIndex",$data['SiiMobilityIndex'])->saveXML();
	}
	
	function importXML($xml)
	{
		if(is_string($xml))
		{
			$dom = new DOMDocument();
			$dom->loadXML($xml);
			$this->fill($dom);			
			$this->ontologies->fill($dom);
			$this->realtimedata->fill($dom);
			$this->staticdata->fill($dom);
			$this->reconciliations->fill($dom);	
			$this->enrichments->fill($dom);
		}
	}
	
	function save(){
		$this->insert();
		$this->ontologies->save();
		$this->realtimedata->save();
		$this->staticdata->save();
		$this->reconciliations->save();
		$this->enrichments->save();
	}
	
	function _clone(){
		$this->insert();
		$this->ontologies->_clone();
		$this->realtimedata->_clone();
		$this->staticdata->_clone();
		$this->reconciliations->_clone();
		$this->enrichments->_clone();
	}
	
	function _copy(){
		$this->insert();
		$this->ontologies->_copy();
		$this->realtimedata->_copy();
		$this->staticdata->_copy();
		$this->reconciliations->_copy();
		$this->enrichments->_copy();
	}
	
/*	function _copy_update(){
		$this->insert();
		$this->ontologies->_copy_update();
		$this->realtimedata->_copy_update();
		$this->staticdata->_copy_update();
		$this->reconciliations->_copy_update();
	}*/
	
	function cloneRepository($id){
		$this->load($id);
		$parentID = $this->getRepositoryID();
		$description = $this->getDescription();
		$security = $this->getSecurityLevel();
		$type = $this->getType();
		$this->reset();
		$this->setParentID($parentID);
		$this->setType($type);
		$this->setDescription($description);
		$this->setSecurityLevel($security);
		
	}
	
	function copyRepository($id){
		$this->load($id);
		$this->reset();	
	}
	
	function lock($data="all",$key=null)
	{
		sm_Logger::write("Locking ".$data);
		
		if($data=="all" || $data=="ontologies")
			$this->ontologies->_lock($key);
		if($data=="all" || $data=="realtimedata")
			$this->realtimedata->_lock($key);
		if($data=="all" || $data=="staticdata")
			$this->staticdata->_lock($key);
		if($data=="all" || $data=="reconciliations")
			$this->reconciliations->_lock($key);
		if($data=="all" || $data=="enrichments")
			$this->enrichments->_lock($key);
		
	}
	
	function commit($data="all",$key=null)
	{
		sm_Logger::write("Committing ".$data);
				
		if($data=="all" || $data=="ontologies")
			$this->ontologies->_commit($key);
		if($data=="all" || $data=="realtimedata")
			$this->realtimedata->_commit($key);
		if($data=="all" || $data=="staticdata")
			$this->staticdata->_commit($key);
		if($data=="all" || $data=="reconciliations")
			$this->reconciliations->_commit($key);
		if($data=="all" || $data=="enrichments")
			$this->enrichments->_commit($key);
		if($data=="all")
		{
			$this->Version+=1;
			$this->update();
			//$xml=$this->toXMLString();
		}
	
	}
	
	function unlock($data="all",$key=null)
	{
		sm_Logger::write("Unlocking ".$data." on ".$key);
		if($data=="all" || $data=="ontologies")
			$this->ontologies->_unlock($key);
		if($data=="all" || $data=="realtimedata")
			$this->realtimedata->_unlock($key);
		if($data=="all" || $data=="staticdata")
			$this->staticdata->_unlock($key);
		if($data=="all" || $data=="reconciliations")
			$this->reconciliations->_unlock($key);
		if($data=="all" || $data=="enrichments")
			$this->enrichments->_unlock($key);
	}
	
	
	function delete($mID=null)
	{
		if($mID)
			$this->load($mID);
		else 
			$mID=$this->getID();
		SiiMobilityRepositoryManager::removeScript($this);
		if(parent::delete($mID))
		{
			$this->ID=null;
			return true;
		}
		return false;
	}
	
	function getDataCollection($collection=null){
		$data=array();
		$data[]=$this->ontologies;
		$data[]=$this->staticdata;
		$data[]=$this->realtimedata;
		$data[]=$this->reconciliations;
		$data[]=$this->enrichments;
		if(!$collection)
		{
			return $data;
		}
		if($collection>=self::ONTOLOGIESDATA && $collection<=self::ENRICHMENTSDATA)
			return $data[$collection];
			
		return array();

	}
	
	function getFullVersion()
	{
		$ver = $this->getVersion().".".$this->getSubVersion();
		return $ver;
	}
	
	
}
