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

class ReconciliationsData extends DataCollection
{
	
	/**
	 * 
	 * @var Generation parent
	 */
	private $parent;
	
	function __construct(Generation $parent=null)
	{
		$this->parent=$parent;
		parent::__construct();
	}
	
	function load($mID)
	{
		$sSQL = "SELECT ID_Reconciliation FROM Reconciliations_Generations WHERE ID_Generation= '$mID';";
		$oResult = $this->db->query($sSQL);
		
		if (!$oResult) {
			$err=$this->db->getError();
			if ($err!=""){
				trigger_error($err);
			}
			return false;
		}
		$this->clear();
		foreach ($oResult as $row)
		{
			$rec = new ReconciliationDescriptor();
			$rec->select($row['ID_Reconciliation'],$mID);
			$this->add($rec,$row['ID_Reconciliation']);
		}
		return true;
	}
	
	function fill($dom)
	{
		$reconciliations = $dom->getElementsByTagName('reconciliation');
		foreach ($reconciliations as $data) {
				
			$ID_Reconciliation = $data->getElementsByTagName('ID_Reconciliation')->item(0)->nodeValue;
			$ID_Generation = $this->parent?$this->parent->getID():"";// $data->getElementsByTagName('ID_Generation')->item(0)->nodeValue;
			$TripleDate = $data->getElementsByTagName('TripleDate')->item(0)->nodeValue;
			
			$rec = new ReconciliationDescriptor();
			$rec->setID_Reconciliation($ID_Reconciliation);
			$rec->setID_Generation($ID_Generation);
			$rec->setTripleDate($TripleDate);
			
			$this->add($rec,$ID_Reconciliation);
		}
	}
	
	function save(){
		$ID_Generation = $this->parent?$this->parent->getID():"";
		foreach ($this as $k=>$v)
			$v->setID_Generation($ID_Generation);
		parent::save();
	}
	
	function _clone(){
		$ID_Generation = $this->parent?$this->parent->getID():"";
		foreach ($this as $k=>$v)
		{
			$v->setID_Generation($ID_Generation);
			$v->setClone(true);
			$v->setLocked(true);
		}
		parent::save();
	}
	
	function _copy(){
		$reconciliationsPath=sm_Config::get('RECONCILIATIONSPATH',"/media/rim/Triples");
		$ID_Generation = $this->parent?$this->parent->getID():"";
		foreach ($this as $k=>$v)
		{
			$v->setID_Generation($ID_Generation);
			$v->setClone(false);
			$v->setLocked(false);
			$v->setTripleDate(Versioner::getResourceLastVersion($reconciliationsPath ."/Riconciliazioni/" . $v->getID_Reconciliation()));
		}
		parent::save();
	}
	
	
	
	function _lockElement($key=null){
		foreach ($this as $k=>$v)
		{
			if($v->getID_Reconciliation()==$key)
			{
				$v->setLocked(true);
				$v->update();
				break;
			}
		}
		
	}
	
	function _lockAll(){
		foreach ($this as $k=>$v)
		{
			$v->setLocked(true);
		}
		parent::update();
	}
	
	
	
	
	function _unlockElement($key){
	
		foreach ($this as $k=>$v)
		{
			if($v->getID_Reconciliation()==$key )
			{
				if(!$v->getClone())
				{
					$v->setLocked(false);
					$v->update();
				}
				break;
			}
		}
	}
	
	function _unlockAll(){
	
		foreach ($this as $k=>$v)
		{
			if(!$v->getClone())
			{
				$v->setLocked(false);
				$v->update();
			}
		}
	
	}
	
	
	
}
