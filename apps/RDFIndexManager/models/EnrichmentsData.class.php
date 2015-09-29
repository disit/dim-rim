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

class EnrichmentsData extends DataCollection
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
		$sSQL = "SELECT ID_Enrichment FROM enrichments_generations WHERE ID_Generation= '$mID';";
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
			$enrichment = new EnrichmentDescriptor();
			$enrichment->select($row['ID_Enrichment'],$mID);
			$this->add($enrichment,$row['ID_Enrichment']);
		}
		return true;
	}
	
	function fill($dom)
	{
		$enrichments = $dom->getElementsByTagName('enrichments');
		foreach ($enrichments as $data) {
				
			$ID_Enrichment = $data->getElementsByTagName('ID_Enrichment')->item(0)->nodeValue;
			$ID_Generation = $this->parent?$this->parent->getID():"";// $data->getElementsByTagName('ID_Generation')->item(0)->nodeValue;
			$Query = $data->getElementsByTagName('Query')->item(0)->nodeValue;
			
			$enrichment = new EnrichmentDescriptor();
			$enrichment->setID_Enrichment($ID_Enrichment);
			$enrichment->setID_Generation($ID_Generation);
			$enrichment->setQuery($Query);
			
			$this->add($enrichment,$ID_Enrichment);
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
			$v->setLocked(false);
		}
		parent::save();
	}
	
	function _copy(){
	
		$ID_Generation = $this->parent?$this->parent->getID():"";
		foreach ($this as $k=>$v)
		{
			$v->setID_Generation($ID_Generation);
			$v->setClone(false);
			$v->setLocked(false);
			//$v->setQuery();
		}
		parent::save();
	}
	
	
	
	function _lockElement($key=null){
		foreach ($this as $k=>$v)
		{
			if($v->getID_Enrichment()==$key)
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
			if($v->getID_Enrichment()==$key )
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
