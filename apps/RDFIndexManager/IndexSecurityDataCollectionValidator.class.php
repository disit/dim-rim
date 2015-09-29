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

class IndexSecurityDataCollectionValidator 
{
	protected $validation;
	protected $securityLevel;
	function __construct(SiiMobilityRepository $index)
	{
		$this->validation=array();
		$this->securityLevel = IndexSecurityLevels::L_OPEN;
		if(method_exists($index, "getSecurityLevel"))
			$this->securityLevel = $index->getSecurityLevel();
		
	}
	
	function validate(DataCollection $dc)
	{
		$ret = true;
		foreach($dc as $v)
		{			
			if(method_exists($v, "getSecurityLevel"))
			{
				$securityLevel = $v->getSecurityLevel();
				$test = $securityLevel<=$this->securityLevel; 
				$ret &= $test;
				if(!$test)
				{
					$item="";
					$type = get_class($dc);
					switch(get_class($v))
					{
						case "OntologyDescriptor":
								$item = $v->getID_Ontology();
								$type="Ontologies";
						break;
						case "OpenDataDescriptor":
							$item = $v->getID_OpenData();
						break;
						case "ReconciliationDescriptor":
							$item = $v->getID_Reconciliation();
							$type="Reconciliations";
						break;
					}
					$this->validation[]=array("item"=>$item,"security Level"=>IndexSecurityLevels::toString($securityLevel),"type"=>$type);
			
				}			
			}
		}
		return $ret;
	}
	
	function getValidationResult()
	{
		return $this->validation;
	}
}