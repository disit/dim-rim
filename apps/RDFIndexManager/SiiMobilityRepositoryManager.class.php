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

define("GENERATIONSDB","Generations");
define("ONTOLOGIESDB","ontologies");

class SiiMobilityRepositoryManager extends MySqlConfig
{
		
	function __construct()
	{
		parent::__construct();
		
	}
	
	
	
	/**
	 * 
	 * Geneations sections
	 * 
	 */
	
	
	/**
	 *
	 * @desc Returns the ID and Date of all the generations
	 *
	 */
	function getAllGenerations($limit="") {

		$loadingsQuery = "SELECT * FROM ".GENERATIONSDB." WHERE SessionEnd ORDER BY ID DESC ".$limit;
		$result = $this->db->query($loadingsQuery);
		return $result;
	
	}
	
	/**
	 *
	 * @desc Returns generations
	 *
	 */
	function getGenerations($where=array(),$limit=null) {
	
		if(isset($limit))
			$limit=str_replace("LIMIT", "", $limit);
		$result = $this->db->select(GENERATIONSDB,$where,array(),$limit,array("ID"),"DESC");
		return $result;
	
	}
	
	/**
	 *
	 * @desc Returns number of generations in the DB
	 *
	 */
	function getAllCountGenerations($where=null) {
	
		$whereCond="";
		if(!empty($where))
			$whereCond=$this->db->buildWhereClause(GENERATIONSDB, $where);
		$loadingsQuery = "SELECT count(*) as n FROM ".GENERATIONSDB." ".$whereCond;
		$result = $this->db->query($loadingsQuery);
		return $result[0]['n'];
	
	}
	
	/**
	 *
	 * @desc Returns number of ontologies in the DB
	 *
	 */
	function getAllCountOntologies($where=null) {
	
		$whereCond="";
		if(!empty($where))
			$whereCond=$this->db->buildWhereClause(ONTOLOGIESDB, $where);
		$loadingsQuery = "SELECT count(*) as n FROM ".ONTOLOGIESDB." ".$whereCond;
		$result = $this->db->query($loadingsQuery);
		return $result[0]['n'];
	
	}
	
	/**
	 *
	 * @desc Returns Ontologies
	 *
	 */
	function getOntologies($where=array(),$limit=null) {
	
		if(isset($limit))
			$limit=str_replace("LIMIT", "", $limit);
		$result = $this->db->select(ONTOLOGIESDB,$where,array(),$limit,array("Name"),"DESC");
		return $result;
	
	}
	
	/**
	 *
	 * @desc Delete a repository by ID
	 *
	 */
	function deleteRepository($mID=null) {
	
		if($mID)
		{
			$repos = new SiiMobilityRepository();
			$repos->load($mID);
			$event = new sm_Event("DeleteRepository",$repos);
			sm_EventManager::handle($event);
			return $repos->delete();
		}
		return false;
	
	}
	
	function getAllOntologies($request) 
	{
	
		// DB table to use
		$table = 'ontologies';
		
		// Table's primary key
		$primaryKey = 'Name';
		
	
		if(isset($request['generations'])) {
			$generations = $request['generations'];
		}
		
		// Show only the ontologies that were selected in the current generation
		if(isset($request['selected'])) {
			$selectionField = "TripleDate";
		}
		else $selectionField = null;
		
		// A global filter of data
		$filter = array (
				'selectionTable' => 'SelectedVersion_Table',
				'selectionField' => $selectionField
		);
		
		// Get the current session ID
		$currentSession = $request['currentSession'];
		
		
		// Get the current session ID
		//$currentSession = $_GET['currentSession'];
		
		// Check if the generation parameters are set
		for ($i=0; $i <5; $i++) {
			if (! isset($generations[$i])) {
				$generations[$i] = 0;
			}	
		}
		
		// Array of database columns which should be read and sent back to DataTables.
		// The `db` parameter represents the column name in the database, while the `dt`
		// parameter represents the DataTables column identifier. In this case simple
		// indexes
		$columns = array (
			    array(
		 	        'db' => 'Name',
		 	        'dt' => 'DT_RowId',
			    		'formatter' => function( $id, $row ) {
		 	            return 'row_'.$id;
		 	        }
		 	    ),
		 	    array (
						'db' => 'Name',
						'dt' => 'Name',
		 	    ),
				array (
						'db' => 'URIPrefix',
						'dt' => 'URIPrefix', 
				),
				array (
						'db' => 'SecurityLevel',
						'dt' => 'SecurityLevel',
				),
				array (
						'db' => 'Generation0',
						'dt' => 'Generation0',
						'table' => 'Ontologies_Generations',
						'field' => 'TripleDate',
						'primaryKey' => 'ID_Ontology',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['0']
				),
				array (
						'db' => 'Generation1',
						'dt' => 'Generation1',
						'table' => 'Ontologies_Generations',
						'field' => 'TripleDate',
						'primaryKey' => 'ID_Ontology',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['1']
				),
				array (
						'db' => 'Generation2',
						'dt' => 'Generation2',
						'table' => 'Ontologies_Generations',
						'field' => 'TripleDate',
						'primaryKey' => 'ID_Ontology',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['2']
				),
				array (
						'db' => 'Generation3',
						'dt' => 'Generation3',
						'table' => 'Ontologies_Generations',
						'field' => 'TripleDate',
						'primaryKey' => 'ID_Ontology',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['3']
				),
				array (
						'db' => 'Generation4',
						'dt' => 'Generation4',
						'table' => 'Ontologies_Generations',
						'field' => 'TripleDate',
						'primaryKey' => 'ID_Ontology',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['4']
				),
		 		array (
		 				'db' => 'SelectedVersion',
		 				'dt' => 'SelectedVersion',
		 				'table' => 'Ontologies_Generations',
		 				'field' => 'TripleDate',
		 				'primaryKey' => 'ID_Ontology',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
		 		array (
		 				'db' => 'Clone',
		 				'dt' => 'Clone',
		 				'table' => 'Ontologies_Generations',
		 				'field' => 'Clone',
		 				'primaryKey' => 'ID_Ontology',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
		 		array (
		 				'db' => 'Locked',
		 				'dt' => 'Locked',
		 				'table' => 'Ontologies_Generations',
		 				'field' => 'Locked',
		 				'primaryKey' => 'ID_Ontology',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
			);
		
			/*require_once ('dbDetails.php');
			require_once ('ServerSideProcessor.class.php');
			require_once ('Versioner.class.php');*/
			
			// Get the data from the SQL database
			$data = ServerSideProcessor::nested($request, $this->sql_details, $table, $primaryKey, $columns, $filter);
			$ontologiesPath=sm_Config::get('ONTOLOGIESPATH',"/media/rim/Ontologie");
			// Analize each resource of the result data
			foreach ($data["data"] as &$item) {
			
			 	// Get the versions of the resource
			 	$item["Versions"] = Versioner::getResourceVersions($ontologiesPath ."/" . $item["Name"]);
				
			 	// Turn the value of the selected version into the index of the element in the Versions array
			 	foreach ($item["Versions"] as $version) {
			 		if ($item["SelectedVersion"] == $version["Data"]) {
			 			$item["SelectedVersionIndex"] = $version["ID"];
			 		}
			 	}
				
			 	// Get the last file date of the resource
			 	$item["LastFileDate"] = Versioner::getResourceLastVersion($ontologiesPath ."/" . $item["Name"]);
			 	
			 }
			 return $data;
			
	
	}
	
	/**
	 *
	 * Returns the ID and Date of all the generations
	 *
	
	 */
	function getAllRealTimeData($request) {
	
				// DB table to use
		$table = 'process_manager2';
		
		// Table's primary key
		$primaryKey = 'Process';
		
		// The generations parameter of the GET request are used
		if(isset($request['generations'])) {
			$generations = $request['generations'];
		}
		
		// Show only the real time data that were selected in the current generation
		if(isset($request['selected'])) {
			$selectionField = "TripleStart";
		}
		else $selectionField = null;
		
		// A global filter of data
		$filter = array (
				'filterField' => 'Real_time',
				'filterValue' => 'yes',
				'selectionTable' => 'SelectedStartDateTime_Table',
				'selectionField' => $selectionField
		);
		
		// Get the current session ID
		$currentSession = $_GET['currentSession'];
		
		// Check if the generation parameters are set
		for ($i=0; $i <5; $i++) {
			if (! isset($generations[$i])) {
				$generations[$i] = 0;
			}	
		}
		
		// Array of database columns which should be read and sent back to DataTables.
		// The `db` parameter represents the column name in the database, while the `dt`
		// parameter represents the DataTables column identifier. In this case simple
		// indexes
		$columns = array (
			    array(
		 	        'db' => 'Process',
		 	        'dt' => 'DT_RowId',
			    		'formatter' => function( $id, $row ) {
		 	            return 'row_'.$id;
		 	        }
		 	    ),
		 	    array (
						'db' => 'Process',
						'dt' => 'Name',
		 	    ),
				array (
						'db' => 'Resource',
						'dt' => 'Resource', 
				),
				array (
						'db' => 'description',
						'dt' => 'Description', 
				),
				array (
						'db' => 'SecurityLevel',
						'dt' => 'SecurityLevel',
				),
				array (
						'db' => 'Category',
						'dt' => 'Category',
				),
				array (
						'db' => 'last_update',
						'dt' => 'LastUpdate', 
				),
				array (
						'db' => 'Generation0Start',
						'dt' => 'Generation0Start',
						'table' => 'OpenData_Generations',
						'field' => 'TripleStart',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['0']
				),
				array (
						'db' => 'Generation0End',
						'dt' => 'Generation0End',
						'table' => 'OpenData_Generations',
						'field' => 'TripleEnd',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['0']
				),
				array (
						'db' => 'Generation1Start',
						'dt' => 'Generation1Start',
						'table' => 'OpenData_Generations',
						'field' => 'TripleStart',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['1']
				),
				array (
						'db' => 'Generation1End',
						'dt' => 'Generation1End',
						'table' => 'OpenData_Generations',
						'field' => 'TripleEnd',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['1']
				),
				array (
						'db' => 'Generation2Start',
						'dt' => 'Generation2Start',
						'table' => 'OpenData_Generations',
						'field' => 'TripleStart',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['2']
				),
				array (
						'db' => 'Generation2End',
						'dt' => 'Generation2End',
						'table' => 'OpenData_Generations',
						'field' => 'TripleEnd',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['2']
				),
				array (
						'db' => 'Generation3Start',
						'dt' => 'Generation3Start',
						'table' => 'OpenData_Generations',
						'field' => 'TripleStart',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['3']
				),
				array (
						'db' => 'Generation3End',
						'dt' => 'Generation3End',
						'table' => 'OpenData_Generations',
						'field' => 'TripleEnd',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['3']
				),
				array (
						'db' => 'Generation4Start',
						'dt' => 'Generation4Start',
						'table' => 'OpenData_Generations',
						'field' => 'TripleStart',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['4']
				),
				array (
						'db' => 'Generation4End',
						'dt' => 'Generation4End',
						'table' => 'OpenData_Generations',
						'field' => 'TripleEnd',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['4']
				),
				array (
		 				'db' => 'SelectedStartDateTime',
		 				'dt' => 'SelectedStartDateTime',
		 				'table' => 'OpenData_Generations',
		 				'field' => 'TripleStart',
		 				'primaryKey' => 'ID_OpenData',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
				array (
		 				'db' => 'SelectedEndDateTime',
		 				'dt' => 'SelectedEndDateTime',
		 				'table' => 'OpenData_Generations',
		 				'field' => 'TripleEnd',
		 				'primaryKey' => 'ID_OpenData',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
		 		array (
		 				'db' => 'Clone',
		 				'dt' => 'Clone',
		 				'table' => 'OpenData_Generations',
		 				'field' => 'Clone',
		 				'primaryKey' => 'ID_OpenData',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
		 		array (
		 				'db' => 'Locked',
		 				'dt' => 'Locked',
		 				'table' => 'OpenData_Generations',
		 				'field' => 'Locked',
		 				'primaryKey' => 'ID_OpenData',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
			);
		
		
		
		// Get the data from the SQL database
		$data = ServerSideProcessor::nested($request, $this->sql_details, $table, $primaryKey, $columns, $filter);
		return $data;	
	}
	
	/**
	 *
	 * Returns the Reconciliations Data set
	 *
	
	 */
	function getAllReconciliationsData($request) {
	
		// DB table to use
		$table = 'Reconciliations';
		
		// Table's primary key
		$primaryKey = 'Name';
		
		// The generations parameter of the GET request are used
		if(isset($request['generations'])) {
			$generations = $request['generations'];
		}
		
		// Show only the reconciliations that were selected in the current generation
		if(isset($request['selected'])) {
			$selectionField = "TripleDate";
		}
		else $selectionField = null;
		
		// A global filter of data
		$filter = array (
				'selectionTable' => 'SelectedVersion_Table',
				'selectionField' => $selectionField
		);
		
		// Get the current session ID
		$currentSession = $request['currentSession'];
		
		// Check if the generation parameters are set
		for ($i=0; $i <5; $i++) {
			if (! isset($generations[$i])) {
				$generations[$i] = 0;
			}	
		}
		
		// Array of database columns which should be read and sent back to DataTables.
		// The `db` parameter represents the column name in the database, while the `dt`
		// parameter represents the DataTables column identifier. In this case simple
		// indexes
		$columns = array (
			    array(
		 	        'db' => 'Name',
		 	        'dt' => 'DT_RowId',
			    		'formatter' => function( $id, $row ) {
		 	            return 'row_'.$id;
		 	        }
		 	    ),
		 	    array (
						'db' => 'Name',
						'dt' => 'Name',
		 	    ),
		 	    array (
						'db' => 'Macroclasses',
						'dt' => 'Macroclasses',
		 	    ),
				array (
						'db' => 'Triples',
						'dt' => 'Triples', 
				),
				array (
						'db' => 'Description',
						'dt' => 'Description', 
				),
				array (
						'db' => 'SecurityLevel',
						'dt' => 'SecurityLevel',
				),
				array (
						'db' => 'Generation0',
						'dt' => 'Generation0',
						'table' => 'Reconciliations_Generations',
						'field' => 'TripleDate',
						'primaryKey' => 'ID_Reconciliation',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['0']
				),
				array (
						'db' => 'Generation1',
						'dt' => 'Generation1',
						'table' => 'Reconciliations_Generations',
						'field' => 'TripleDate',
						'primaryKey' => 'ID_Reconciliation',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['1']
				),
				array (
						'db' => 'Generation2',
						'dt' => 'Generation2',
						'table' => 'Reconciliations_Generations',
						'field' => 'TripleDate',
						'primaryKey' => 'ID_Reconciliation',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['2']
				),
				array (
						'db' => 'Generation3',
						'dt' => 'Generation3',
						'table' => 'Reconciliations_Generations',
						'field' => 'TripleDate',
						'primaryKey' => 'ID_Reconciliation',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['3']
				),
				array (
						'db' => 'Generation4',
						'dt' => 'Generation4',
						'table' => 'Reconciliations_Generations',
						'field' => 'TripleDate',
						'primaryKey' => 'ID_Reconciliation',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['4']
				),
		 		array (
		 				'db' => 'SelectedVersion',
		 				'dt' => 'SelectedVersion',
		 				'table' => 'Reconciliations_Generations',
		 				'field' => 'TripleDate',
		 				'primaryKey' => 'ID_Reconciliation',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
		 		array (
		 				'db' => 'Clone',
		 				'dt' => 'Clone',
		 				'table' => 'Reconciliations_Generations',
		 				'field' => 'Clone',
		 				'primaryKey' => 'ID_Reconciliation',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
		 		array (
		 				'db' => 'Locked',
		 				'dt' => 'Locked',
		 				'table' => 'Reconciliations_Generations',
		 				'field' => 'Locked',
		 				'primaryKey' => 'ID_Reconciliation',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
			);

		
		// Get the data from the SQL database
		$data = ServerSideProcessor::nested($request, $this->sql_details, $table, $primaryKey, $columns, $filter);
		$reconciliationsPath=sm_Config::get('RECONCILIATIONSPATH',"/media/rim/Triples");
		// Analize each resource of the result data
		 foreach ($data["data"] as &$item) {
		
		 	// Get the versions of the resource
		 	$item["Versions"] = Versioner::getResourceVersions($reconciliationsPath."/Riconciliazioni/" . $item["Name"]);
			
		 	// Turn the value of the selected version into the index of the element in the Versions array
		 	foreach ($item["Versions"] as $version) {
		 		if ($item["SelectedVersion"] == $version["Data"]) {
		 			$item["SelectedVersionIndex"] = $version["ID"];
		 		}
		 	}
			
		 	// Get the last file date of the resource
		 	$item["LastFileDate"] = Versioner::getResourceLastVersion($reconciliationsPath."/Riconciliazioni/" . $item["Name"]);
		 	
		 }
		return $data;
	
	}

	/**
	 *
	 * Returns the ID and Date of all the generations
	 *
	
	 */
	function getAllStaticData($request) {
	
			// DB table to use
		$table = 'process_manager2';
		
		// Table's primary key
		$primaryKey = 'Process';
		
		// The generations parameter of the GET request are used
		if(isset($request['generations'])) {
			$generations = $request['generations'];
		}
		
		// Show only the static data that were selected in the current generation
		if(isset($request['selected'])) {
			$selectionField = "TripleStart";
		}
		else $selectionField = null;
		
		// A global filter of data
		$filter = array (
				'filterField' => 'Real_time',
				'filterValue' => 'no',
				'selectionTable' => 'SelectedVersion_Table',
				'selectionField' => $selectionField
				);
		
		// Get the current session ID
		$currentSession = $request['currentSession'];
		
		// Check if the generation parameters are set
		for ($i=0; $i <5; $i++) {
			if (! isset($generations[$i])) {
				$generations[$i] = 0;
			}	
		}
		
		// Array of database columns which should be read and sent back to DataTables.
		// The `db` parameter represents the column name in the database, while the `dt`
		// parameter represents the DataTables column identifier. In this case simple
		// indexes
		$columns = array (
			    array(
		 	        'db' => 'Process',
		 	        'dt' => 'DT_RowId',
			    		'formatter' => function( $id, $row ) {
		 	            return 'row_'.$id;
		 	        }
		 	    ),
		 	    array (
						'db' => 'Process',
						'dt' => 'Name',
		 	    ),
				array (
						'db' => 'Resource',
						'dt' => 'Resource', 
				),
				array (
						'db' => 'description',
						'dt' => 'Description', 
				),
				array (
						'db' => 'SecurityLevel',
						'dt' => 'SecurityLevel',
				),
				array (
						'db' => 'Category',
						'dt' => 'Category',
				),
				array (
						'db' => 'last_triples',
						'dt' => 'LastUpdate', 
				),
				array (
						'db' => 'Generation0',
						'dt' => 'Generation0',
						'table' => 'OpenData_Generations',
						'field' => 'TripleStart',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['0']
				),
				array (
						'db' => 'Generation1',
						'dt' => 'Generation1',
						'table' => 'OpenData_Generations',
						'field' => 'TripleStart',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['1']
				),
				array (
						'db' => 'Generation2',
						'dt' => 'Generation2',
						'table' => 'OpenData_Generations',
						'field' => 'TripleStart',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['2']
				),
				array (
						'db' => 'Generation3',
						'dt' => 'Generation3',
						'table' => 'OpenData_Generations',
						'field' => 'TripleStart',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['3']
				),
				array (
						'db' => 'Generation4',
						'dt' => 'Generation4',
						'table' => 'OpenData_Generations',
						'field' => 'TripleStart',
						'primaryKey' => 'ID_OpenData',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $generations['4']
				),
		 		array (
		 				'db' => 'SelectedVersion',
		 				'dt' => 'SelectedVersion',
		 				'table' => 'OpenData_Generations',
		 				'field' => 'TripleStart',
		 				'primaryKey' => 'ID_OpenData',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
		 		array (
		 				'db' => 'Clone',
		 				'dt' => 'Clone',
		 				'table' => 'OpenData_Generations',
		 				'field' => 'Clone',
		 				'primaryKey' => 'ID_OpenData',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
		 		array (
		 				'db' => 'Locked',
		 				'dt' => 'Locked',
		 				'table' => 'OpenData_Generations',
		 				'field' => 'Locked',
		 				'primaryKey' => 'ID_OpenData',
		 				'clauseField' => 'ID_Generation',
		 				'clauseValue' => $currentSession
		 		),
		);
	
	
	
		// Get the data from the SQL database
		$data = ServerSideProcessor::nested($request, $this->sql_details, $table, $primaryKey, $columns, $filter);
		$staticDataPath=sm_Config::get('STATICDATAPATH',"/media/rim/Triples");
		// Analize each resource of the result data
		foreach ($data["data"] as &$item) {
		
			// Get the versions of the resource
			$item["Versions"] = Versioner::getResourceVersions($staticDataPath ."/" . $item["Category"] . "/" . $item["Name"]);
			
			// Turn the value of the selected version into the index of the element in the Versions array
			foreach ($item["Versions"] as $version) {
				if ($item["SelectedVersion"] == $version["Data"]) {
					$item["SelectedVersionIndex"] = $version["ID"];
				}
			}
			
		}
		return $data;
		
	}
	
	/**
	 *
	 * Returns the Enrichments Data set
	 *
	
	 */
	function getAllEnrichmentsData($request) {
	
		// DB table to use
		$table = 'enrichments';
	
		// Table's primary key
		$primaryKey = 'Name';
	
		
	
		// Show only the Enrichments that were selected in the current generation
		if(isset($request['selected'])) {
			$selectionField = "ID_Enrichment";
		}
		else $selectionField = null;
	
		// A global filter of data
		$filter = array (
				'selectionTable' => 'SelectedVersion_Table',
				'selectionField' => $selectionField
		);
	
		// Get the current session ID
		$currentSession = $request['currentSession'];
	
	
	
		// Array of database columns which should be read and sent back to DataTables.
		// The `db` parameter represents the column name in the database, while the `dt`
		// parameter represents the DataTables column identifier. In this case simple
		// indexes
		$columns = array (
				array(
						'db' => 'Name',
						'dt' => 'DT_RowId',
						'formatter' => function( $id, $row ) {
							return 'row_'.$id;
						}
				),
				array (
						'db' => 'Name',
						'dt' => 'Name',
				),
				
				array (
						'db' => 'Description',
						'dt' => 'Description',
				),
				array (
						'db' => 'Query',
						'dt' => 'Query',
				),
				array (
						'db' => 'Clone',
						'dt' => 'Clone',
						'table' => 'enrichments_generations',
						'field' => 'Clone',
						'primaryKey' => 'ID_Enrichment',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $currentSession
				),
				array (
						'db' => 'Locked',
						'dt' => 'Locked',
						'table' => 'enrichments_generations',
						'field' => 'Locked',
						'primaryKey' => 'ID_Enrichment',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $currentSession
				),
			
				array (
						'db' => 'SelectedVersion',
						'dt' => 'SelectedVersion',
						'table' => 'enrichments_generations',
						'field' => 'ID_Enrichment',
						'primaryKey' => 'ID_Enrichment',
						'clauseField' => 'ID_Generation',
						'clauseValue' => $currentSession
				),
			
		);
	
	
		// Get the data from the SQL database
		$data = ServerSideProcessor::nested($request, $this->sql_details, $table, $primaryKey, $columns, $filter);
		foreach ($data["data"] as &$item) {
			$item['Query']=htmlentities($item['Query']);
		}
	/*	$reconciliationsPath=sm_Config::get('RECONCILIATIONSPATH',"/media/rim/Triples");
		// Analize each resource of the result data
		foreach ($data["data"] as &$item) {
	
			// Get the versions of the resource
			$item["Versions"] = Versioner::getResourceVersions($reconciliationsPath."/Riconciliazioni/" . $item["Name"]);
				
			// Turn the value of the selected version into the index of the element in the Versions array
			foreach ($item["Versions"] as $version) {
				if ($item["SelectedVersion"] == $version["Data"]) {
					$item["SelectedVersionIndex"] = $version["ID"];
				}
			}
				
			// Get the last file date of the resource
			$item["LastFileDate"] = Versioner::getResourceLastVersion($reconciliationsPath."/Riconciliazioni/" . $item["Name"]);
	
		}*/
		return $data;
	
	}
	
	/**
	 *
	 * Returns the ID and Date of all the generations
	 *
	
	 */
	function getDataInfo($id=null) {
	
		$query = "SELECT *
				FROM process_manager2
				WHERE Process='$id'
				LIMIT 1";
		$data = $this->db->query($query);
		
		// The array of MySql have double key pairs: a numeric and a string one
		// This cicle remove the numeric one
		foreach(array_keys($data[0]) as $key) {
			if(is_int($key)) {
				unset($data[0][$key]);
			}
		}
		
		return $data[0];
	
	}
	
	function setStatus($request)
	{
		
		// Get the current session ID
		$currentSession = $request['currentSession'];
		
		//
		//
		// REQUEST PARAMETERS
		//
		//
		
		// The data type: Ontologies, StaticData, RealTimeData, Reconciliations or Enrichments
		$dataType = $request['dataType'];
		
		// The action to perform: copy, clone or select
		$action = $request['action'];
		
		// The column for the copy or clone action
		if (isset($request['column'])) {
			$column = $request['column'];
		}
		
		// The id and version of the selected data
		if (isset($request['select'])) {
			$select = $request['select'];
			$id = $select["id"];
			if($dataType == "RealTimeData") {
				$version = Array($select["from"], $select["to"]);
			}
			else $version = Array ($select["version"]);
		}
		
		// Get the parameters to perform the required action
		$params = StatusManager::getParameters($action, $dataType);
		
		// Copy
		if ($action == "copy") {
			StatusManager::copyGeneration($currentSession, $column, $params, $this->sql_details);
		}
		// Clone
		else if ($action == "clone") {
			StatusManager::cloneGeneration($currentSession, $column, $params, $this->sql_details);
		}
		// Select
		else if ($action == "select") {
			StatusManager::selectItem($currentSession, $id, $version, $params, $this->sql_details);
		}
	}
	
	
	function doScript(SiiMobilityRepository $generation)
	{
		$event = new sm_Event("GenerateScriptEvent",$generation);
		sm_EventManager::handle($event);
		if(!$event->hasToStop())
		{
			if($generation->getType()=="")
				return array('path' => "Not available");
			 $currentSession = $generation->getID();
			 $repositoryID=$generation->getRepositoryID();
			 $res = $this->_getScript($currentSession, $repositoryID);
			 $generation->setScriptPath(ltrim($res['path'],"/"));
			 $generation->update();
			 return $res;
			
		}
		else 
			return $event->getResult();
		
	}
	
	/**
	 *
	 * Get a script
	 *
	 * @param string $currentSession the id of the session whose the script is related
	 * @param string $repositoryID the id of the repository to create
	 */
	private function _getScript($currentSession, $repositoryID){
	
		$user = sm_Config::get('USERCONFIGCTRL',"");
		$passw = sm_Config::get('PWDCONFIGCTRL',"");
		$restcallUrl = "http://localhost".sm_Config::get("BASEURL","")."api/IndexGenerator/session/";
		$restAuth = "-u ".$user.":".$passw;
		
		$indexFile = "/opt/owlim/getting-started/index.txt";
		$lockFile  = "/opt/indexgenerator/script/".$repositoryID."/.lock";
		$logFile   = "/opt/indexgenerator/script/".$repositoryID."/".$repositoryID.".csv";
		
		$ontologiesPath=sm_Config::get('ONTOLOGIESPATH',"/media/rim/Ontologie");
		$staticDataPath=sm_Config::get('STATICDATAPATH',"/media/rim/Triples");
		$realTimeDataPath=sm_Config::get('REALTIMEDATAPATH',"/media/rim/Triples");
		$reconciliationsPath=sm_Config::get('RECONCILIATIONSPATH',"/media/rim/Triples");
		
		$scriptPath= "/opt/owlim/getting-started/example.sh";
		// The name of the script file
		//TODO Va messo in cartella con data?
		//$fileName = '/opt/lampp/htdocs/indexgenerator/script/generateindex.sh';
		//$fileName = '/home/ubuntu/Desktop/owlim-lite-5.4.6287/getting-started/generateindex.sh';
		$fileDir = '/opt/indexgenerator/script/'.$repositoryID;
		if (!file_exists($fileDir)) {
    		mkdir($fileDir, 0777, true);
		}
		$fileName = $fileDir.'/generateindex.sh';
		// Create the script file
		$fp = fopen($fileName, "w");
	
		// First line of the script
		fwrite($fp, "#!/bin/bash\n");
		fwrite($fp, "\n");
	
		// Set configuration parameters
		fwrite($fp, "#Set configuration parameters\n");
		fwrite($fp, "sessionId=\"" . $currentSession . "\" # DO NOT CHANGE THIS PARAMETER!\n");
		fwrite($fp, "scriptPath=\"".$scriptPath."\"\n");
		fwrite($fp, "ontologiesPath=\"/media/rim/Ontologie\"\n");
		fwrite($fp, "staticDataPath=\"/media/rim/Triples\"\n");
		fwrite($fp, "realTimeDataPath=\"/media/rim/Triples\"\n");
		fwrite($fp, "reconciliationsPath=\"/media/rim/Triples\"\n");
		fwrite($fp, "sesameUrl=\"http://localhost:8080/openrdf-sesame/\"\n");
		fwrite($fp, "repositoryId=\"" . $repositoryID . "\"\n");
		fwrite($fp, "baseContext=\"http://www.disit.org/km4city/resource/\"\n");
		fwrite($fp, "indexFile=\"".$indexFile."\"\n");
		fwrite($fp, "lockFile=\"".$lockFile."\"\n");
		fwrite($fp, "\n");
		fwrite($fp, "# Moves to load script directory\n");
		fwrite($fp, "cd /opt/owlim/getting-started/\n");
		fwrite($fp, "\n");
	
		# Looking for lock file
		fwrite($fp, "# Looks for the lock file; if found it, the process ends\n");
		fwrite($fp, "if [ -f \$lockFile ] ; then\n");
				fwrite($fp, "	clear\n");
				fwrite($fp, "	echo\n");
				fwrite($fp, "	echo \"---------------------------------------------------------------\"\n");
				fwrite($fp, "	echo\n");
				fwrite($fp, "	echo \"ERROR!!!\"\n");
				fwrite($fp, "	echo \"Lock file found (".$lockFile.")\"\n");
				fwrite($fp, "	echo \"Another index generation is already running.\"\n");
				fwrite($fp, "	echo \"Is not possible to generate a new index!\"\n");
				fwrite($fp, "	echo\n");
				fwrite($fp, "	echo\n");
				fwrite($fp, "	exit\n");
				fwrite($fp, "fi\n");
				fwrite($fp, "\n");
	
				# Create lock file and save generation start date and time
				fwrite($fp, "# Create the lock file\n");
				fwrite($fp, "echo \"\" > \$lockFile\n");
				fwrite($fp, "# Save to database the generation start dateTime\n");
				fwrite($fp, "curl -X PUT ".$restAuth." ".$restcallUrl."\$sessionId -d \"{ \\\"repositoryID\\\":\\\"\$repositoryId\\\",\\\"status\\\":\\\"generationStart\\\"}\"\n");
				fwrite($fp, "\n");
	
	
	
				//
				//
				// ONTOLOGIES
				//
				//
	
				// Get the ontologies array
				$ontologies = $this->_getData("ontologies", $currentSession);
	
				if (count($ontologies) > 0) {
	
					fwrite($fp, "# Declares each ontology to use\n");
	
					// For each ontology, declares it in the script array
					for($i = 0; $i < count($ontologies); $i ++) {
						$folder = Versioner::getPathFromDateTime($ontologies[$i]["SelectedVersion"]);
						fwrite($fp, "declare -A ontology" . $i ."=([name]=" . $ontologies[$i]["Name"] . " [category]=Ontology [SelectedVersion]=\"" . $folder . "\")\n");
					}
					fwrite($fp, "\n");
	
					// Loop over each ontology, to get parameters and load triples
					fwrite($fp, "# Loops over each ontology\n");
					fwrite($fp, "for i in {0.." . (count($ontologies) - 1) . "}\n");
					fwrite($fp, "do\n");
					fwrite($fp, "	name=\"ontology\$i[name]\"\n");
					fwrite($fp, "	category=\"ontology\$i[category]\"\n");
					fwrite($fp, "	SelectedVersion=\"ontology\$i[SelectedVersion]\"\n");
					fwrite($fp, "	# Start dateTime\n");
					fwrite($fp, "	startDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Run the load process\n");
					fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category} " .
							"preload=\${ontologiesPath}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					fwrite($fp, "	# End dateTime\n");
					fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Save execution time in logs\n");
					fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> ".$logFile."\n");
					fwrite($fp, "done\n");
					fwrite($fp, "\n");
					fwrite($fp, "\n");
	
				}
	
	
	
	
	
				//
				//
				// STATIC DATA
				//
				//
	
				// Get the static data array
				$staticData = $this->_getData("staticData", $currentSession);
	
				if (count($staticData) > 0) {
	
					fwrite($fp, "# Declares each static data to use\n");
	
					// For each static data, declares it in the script array
					for($i = 0; $i < count($staticData); $i ++) {
						$folder = Versioner::getPathFromDateTime($staticData[$i]["SelectedVersion"]);
						fwrite($fp, "declare -A staticdata" . $i ."=([name]=" . $staticData[$i]["Name"] . " [category]=" . $staticData[$i]["Category"] . " [SelectedVersion]=\"" . $folder . "\")\n");
					}
					fwrite($fp, "\n");
	
					// Loop over each static data, to get parameters and load triples
					fwrite($fp, "# Loops over each static data\n");
					fwrite($fp, "for i in {0.." . (count($staticData) - 1) . "}\n");
					fwrite($fp, "do\n");
					fwrite($fp, "	name=\"staticdata\$i[name]\"\n");
					fwrite($fp, "	category=\"staticdata\$i[category]\"\n");
					fwrite($fp, "	SelectedVersion=\"staticdata\$i[SelectedVersion]\"\n");
					fwrite($fp, "	# Start dateTime\n");
					fwrite($fp, "	startDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Run the load process\n");
					fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category}/\${!name} " .
							"preload=\${staticDataPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					fwrite($fp, "	# End dateTime\n");
					fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Save execution time in logs\n");
					fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> ".$logFile."\n");
					fwrite($fp, "done\n");
					fwrite($fp, "\n");
					fwrite($fp, "\n");
	
				}
	
	
	
	
	
				//
				//
				// REAL TIME DATA
				//
				//
	
				// Get the realtime data array
				$realTimeData = $this->_getData("realTimeData", $currentSession);
	
				if (count($realTimeData) > 0) {
	
					// Declare an associative array
					fwrite($fp, "# Declares each realtime data to use\n");
	
	
					// For each realtime data, declares it in the script array
					for($i = 0; $i < count($realTimeData); $i ++) {
						$startDateTime = $realTimeData[$i]["TripleStart"];
						if (strpos($startDateTime,'from first') !== false)
							$startDateTime = "1970-01-01 00:00:00";
						$endDateTime = $realTimeData[$i]["TripleEnd"];
						if (strpos($endDateTime,'until last') !== false)
							$endDateTime = "3000-01-01 00:00:00";
						fwrite($fp, "declare -A realtimedata" . $i ."=([name]=" . $realTimeData[$i]["Name"] . " [category]=" . $realTimeData[$i]["Category"] . " [TripleStart]=\"" . $startDateTime . "\" [TripleEnd]=\"" . $endDateTime . "\")\n");
					}
					fwrite($fp, "\n");
	
					// Loop over each realtime data, to get parameters and load triples
					fwrite($fp, "# Loops over each realtime data\n");
					fwrite($fp, "for i in {0.." . (count($realTimeData) - 1) . "}\n");
					fwrite($fp, "do\n");
					fwrite($fp, "	name=\"realtimedata\$i[name]\"\n");
					fwrite($fp, "	category=\"realtimedata\$i[category]\"\n");
					fwrite($fp, "	tripleStart=realtimedata\${i}[TripleStart]\n");
					fwrite($fp, "	tripleEnd=realtimedata\${i}[TripleEnd]\n");
					fwrite($fp, "	startDate=$(date -d \"\${!tripleStart}\" +\"%Y%m%d%H%M%S\")\n");
					fwrite($fp, "	endDate=$(date -d \"\${!tripleEnd}\" +\"%Y%m%d%H%M%S\")\n");
					fwrite($fp, "\n");
					fwrite($fp, "	# Start dateTime\n");
					fwrite($fp, "	startDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Change directory in order to optimize the result file\n");
					fwrite($fp, "	cd \$realTimeDataPath/\${!category}/\${!name}/\n");
					fwrite($fp, "	# Gets the list of the subdirectories for this real time data\n");
					fwrite($fp, "	find . -type d -fprint \${indexFile}\n");
					fwrite($fp, "\n");
					fwrite($fp, "	# Moves to load script directory\n");
					fwrite($fp, "	cd /home/ubuntu/Desktop/owlim-lite-5.4.6287/getting-started/\n");
					fwrite($fp, "	\n");
					fwrite($fp, "	# Explore the directories tree of the considered real time data\n");
					fwrite($fp, "	for line in $(cat \${indexFile})\n");
					fwrite($fp, "	do\n");
					fwrite($fp, "\n");
					fwrite($fp, "		# Checks that the considered directory is really a tree leaf and not a tree node\n");
					fwrite($fp, "		# In this case the lenght of the directory name should be at least 20, i.e. the length of ./YYYY_mm/dd/HH/MMSS\n");
					fwrite($fp, "		if [ \${#line} -ge 20 ];\n");
					fwrite($fp, "		then");
					fwrite($fp, "\n");
					fwrite($fp, "			# Get the datetime of the considered real time data\n");
					fwrite($fp, "			currentDateTime=$(date -d \"\${line:2:4}-\${line:7:2}-\${line:10:2} \${line:13:2}:\${line:16:2}:\${line:18:2}\" +\"%Y%m%d%H%M%S\")\n");
					fwrite($fp, "\n");
					fwrite($fp, "			# If the datetime of the considered real time date is in the choosen datetime range, load its data\n");
					fwrite($fp, "			if [ \$startDate -le \$currentDateTime ] && [ \$currentDateTime -le \$endDate ];\n");
					fwrite($fp, "			then\n");
					fwrite($fp, "				bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category}/\${!name} preload=\${realTimeDataPath}/\${!category}/\${!name}/\${line:2} username=admin password=ubuntu queryfile=none.\n");
					fwrite($fp, "			fi # [ \$startDate -le \$currentDateTime ] && [ \$currentDateTime -le \$endDate ];\n");
					fwrite($fp, "\n");
					fwrite($fp, "		fi #if [ \${#string} -ge 20 ];\n");
					fwrite($fp, "	done\n");
					fwrite($fp, "	# End dateTime\n");
					fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Save execution time in logs\n");
					fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> ".$logFile."\n");
					fwrite($fp, "done\n");
					fwrite($fp, "\n");
					fwrite($fp, "\n");
					fwrite($fp, "# Remove the temporary file\n");
					fwrite($fp, "rm \${indexFile}\n");
					fwrite($fp, "\n");
					fwrite($fp, "\n");
	
				}
	
	
	
	
	
				//
				//
				// RECONCILIATIONS
				//
				//
	
				// Get the reconciliations array
				$reconciliations = $this->_getData("reconciliations", $currentSession);
	
				if (count($reconciliations) > 0) {
	
					fwrite($fp, "# Declares each reconciliation procedure to use\n");
	
					// For each reconciliation, declares it in the script array
					for($i = 0; $i < count($reconciliations); $i ++) {
						$folder = Versioner::getPathFromDateTime($reconciliations[$i]["SelectedVersion"]);
						fwrite($fp, "declare -A reconciliation" . $i ."=([name]=" . $reconciliations[$i]["Name"] . " [category]=Riconciliazioni [SelectedVersion]=\"" . $folder . "\")\n");
					}
					fwrite($fp, "\n");
	
					// Loop over each reconciliation, to get parameters and load triples
					fwrite($fp, "# Loops over each reconciliation\n");
					fwrite($fp, "for i in {0.." . (count($reconciliations) - 1) . "}\n");
					fwrite($fp, "do\n");
					fwrite($fp, "	name=\"reconciliation\$i[name]\"\n");
					fwrite($fp, "	category=\"reconciliation\$i[category]\"\n");
					fwrite($fp, "	SelectedVersion=\"reconciliation\$i[SelectedVersion]\"\n");
					fwrite($fp, "	# Start dateTime\n");
					fwrite($fp, "	startDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Run the load process\n");
					fwrite($fp, "	bash \${scriptPath} url=\${sesameUrl} repository=\${repositoryId} context=\${baseContext}\${!category}/\${!name} " .
							"preload=\${reconciliationsPath}/\${!category}/\${!name}/\${!SelectedVersion} username=admin password=ubuntu queryfile=none.\n");
					fwrite($fp, "	# End dateTime\n");
					fwrite($fp, "	endDateTime=`date \"+%d/%m/%y %H:%M:%S\"`\n");
					fwrite($fp, "	# Save execution time in logs\n");
					fwrite($fp, "	echo \"\${!name};\$startDateTime;\$endDateTime\" >> ".$logFile."\n");
					fwrite($fp, "done\n");
					fwrite($fp, "\n");
					fwrite($fp, "\n");
	
				}
	
	
				// Close the generation on database and remove lock file
				fwrite($fp, "# Save to database the generation end dateTime\n");
				fwrite($fp, "curl -X PUT ".$restAuth." ".$restcallUrl."\$sessionId -d \"{ \\\"repositoryID\\\":\\\"\$repositoryId\\\",\\\"status\\\":\\\"generationEnd\\\"}\"\n");
				fwrite($fp, "# Remove the lock file\n");
				fwrite($fp, "rm \$lockFile\n");
	
	
				// Close the script file
				fclose($fp);
				return array('path' => $fileName);
				//return json_encode(array('path' => $fileName));;
					
	}
	
	
	private function _getData($dataType, $idGeneration) {
	
						// Set the select query according to the choosen data type
						if ($dataType == "ontologies") {
			$selectQuery = "SELECT ontologies.Name, Ontologies_Generations.TripleDate AS SelectedVersion
				FROM ontologies
				INNER JOIN Ontologies_Generations
				ON ontologies.Name = Ontologies_Generations.ID_Ontology AND Ontologies_Generations.Clone=0 AND Ontologies_Generations.ID_Generation = " . $idGeneration;
						}
						else if ($dataType == "staticData") {
			$selectQuery = "SELECT process_manager2.Process AS Name, process_manager2.Category, OpenData_Generations.TripleStart AS SelectedVersion
				FROM process_manager2
				INNER JOIN OpenData_Generations
				ON process_manager2.Process = OpenData_Generations.ID_OpenData AND OpenData_Generations.Clone=0 AND OpenData_Generations.ID_Generation = " . $idGeneration .
					" WHERE process_manager2.Real_time = 'no'";
						}
		else if ($dataType == "realTimeData") {
			$selectQuery = "SELECT process_manager2.Process AS Name, process_manager2.Category, OpenData_Generations.TripleStart, OpenData_Generations.TripleEnd
				FROM process_manager2
				INNER JOIN OpenData_Generations
				ON process_manager2.Process = OpenData_Generations.ID_OpenData AND OpenData_Generations.Clone=0 AND OpenData_Generations.ID_Generation = " . $idGeneration .
					" WHERE process_manager2.Real_time = 'yes'";
						}
						else if ($dataType == "reconciliations") {
			$selectQuery = "SELECT Reconciliations.Name, Reconciliations_Generations.TripleDate AS SelectedVersion
				FROM Reconciliations
				INNER JOIN Reconciliations_Generations
				ON Reconciliations.Name = Reconciliations_Generations.ID_Reconciliation AND Reconciliations_Generations.Clone=0 AND Reconciliations_Generations.ID_Generation = " . $idGeneration;
					}
	
					//Create the bindings array
					$bindings = array ();
	
					// Get an handle to the database connection
					$db = MySqlConnector::sql_connect ($this->sql_details);
	
					// The query to get the data
			$data = MySqlConnector::sql_select( $db, $bindings, $selectQuery);
	
			return $data;
			
	}
	
	
	static public function install($db)
	{
		sm_Config::set('RDFREPOSITORYTYPE',array("value",serialize(array()),"description"=>"RDF Repository Types Available"));
		return true;
	}
	
	static public function uninstall($db)
	{
		sm_Config::delete('RDFREPOSITORYTYPE');
		return true;
	}
	
	static public function addType($type)
	{
		$types = sm_Config::get('RDFREPOSITORYTYPE',null);
		$data=array();
		if($types)
		{
			$data = unserialize($types);
		}
		$data[]=$type;
		sm_Config::set('RDFREPOSITORYTYPE',array("value"=>serialize($data),"description"=>"RDF Repository Types Available"));
	}
	
	static public function removeType($type)
	{
		$types = sm_Config::get('RDFREPOSITORYTYPE',null);
		if($types)
		{
			$data = unserialize($types);
			$i=array_search($type, $data);
			if($i!==false)
			{
				unset($data[$i]);
			}
			sm_Config::set('RDFREPOSITORYTYPE',array("value"=>serialize($data),"description"=>"RDF Repository Types Available"));
		}
	}
	
	static public function removeScript(Generation $generation)
	{
		$repositoryID = $generation->getRepositoryID();
		$generation->deleteScript();
		$fileDir = '/opt/indexgenerator/script/'.$repositoryID;
		if(!empty($repositoryID) && is_dir($fileDir))
		{
			exec("rm -R ".$fileDir."/*");
			sm_deleteDirectory($fileDir);
		}
	}
	
}
