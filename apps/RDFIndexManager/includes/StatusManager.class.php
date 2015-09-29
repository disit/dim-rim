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

/**
 * 
 * Manager for copy or clone a generation and for select an item version
 * 
 */
class StatusManager {

	/**
	 *
	 * @desc Help function to set the parameters to use, basing on the action and data type
	 *
	 * @param string $action The action to perform: copy, clone or select
	 * @param string $dataType The type of data: Ontologies, StaticData, RealTimeData or Reconciliations
	 * @return array an array of parameters to perform an action
	 */
	static function getParameters($action, $dataType) {
	
		// Case Ontologies
		if($dataType=="Ontologies") {
	
			// Copy parameters
			if ($action == "copy") {
	
				return array (
						"tableName" => "Ontologies_Generations",
						"primaryKey" => "ID_Generation",
						"readFields" => "Ontologies_Generations.ID_Generation, Ontologies_Generations.ID_Ontology",
						"writeFields" => "Ontologies_Generations.ID_Generation, Ontologies_Generations.ID_Ontology, Ontologies_Generations.TripleDate",
						"nestedTable" => "ontologies",
						"nestedTablePrimaryKey" => "Name",
						"foreignKey" => "ID_Ontology",
						"basePath" => "/media/Ontologie/",
						"classifier" => ""
				);
	
			}
				
			// Clone parameters
			else if ($action == "clone") {
	
				return array (
						"tableName" => "Ontologies_Generations",
						"primaryKey" => "ID_Generation",
						"readFields" => "ID_Generation, ID_Ontology, TripleDate",
						"writeFields" => "ID_Generation, ID_Ontology, TripleDate",
						"nestedTable" => "ontologies",
						"nestedTablePrimaryKey" => "Name",
						"foreignKey" => "ID_Ontology",
						"insertPrimaryKey" => "ID_Ontology",
						"insertCloneField" => "TripleDate"
				);
	
			}
	
			// Select parameters
			else if ($action == "select") {
	
				return  array (
						"tableName" => "Ontologies_Generations",
						"fields" => "ID_Generation, ID_Ontology, TripleDate",
						"updateFields" => array("TripleDate"),
						"primaryKey" => "ID_Generation",
						"foreignKey" => "ID_Ontology"
				);
			}
	
		}
		// Case Static Data
		else if($dataType=="StaticData") {
	
			// Copy parameters
			if ($action == "copy") {
	
				return array (
						"tableName" => "OpenData_Generations",
						"primaryKey" => "ID_Generation",
						"readFields" => "OpenData_Generations.ID_Generation, OpenData_Generations.ID_OpenData, process_manager2.Category",
						"writeFields" => "OpenData_Generations.ID_Generation, OpenData_Generations.ID_OpenData, OpenData_Generations.TripleStart",
						"nestedTable" => "process_manager2",
						"nestedTablePrimaryKey" => "Process",
						"nestedTableFilterField" => "Real_time",
						"nestedTableFilterValue" => "no",
						"foreignKey" => "ID_OpenData",
						"basePath" => "/media/Triples/",
						"classifier" => "Category"
				);
	
			}
				
			// Clone parameters
			else if ($action == "clone") {
	
				return array (
						"tableName" => "OpenData_Generations",
						"primaryKey" => "ID_Generation",
						"readFields" => "OpenData_Generations.ID_Generation, OpenData_Generations.ID_OpenData, OpenData_Generations.TripleStart",
						"writeFields" => "OpenData_Generations.ID_Generation, OpenData_Generations.ID_OpenData, OpenData_Generations.TripleStart",
						"nestedTable" => "process_manager2",
						"nestedTablePrimaryKey" => "Process",
						"nestedTableFilterField" => "Real_time",
						"nestedTableFilterValue" => "no",
						"foreignKey" => "ID_OpenData",
 						"insertPrimaryKey" => "ID_OpenData",
 						"insertCloneField" => "TripleStart"
				);
	
			}
	
			// Select parameters
			else if ($action == "select") {
	
				return  array (
						"tableName" => "OpenData_Generations",
						"fields" => "ID_Generation, ID_OpenData, TripleStart",
						"updateFields" => array ("TripleStart"),
						"primaryKey" => "ID_Generation",
						"foreignKey" => "ID_OpenData"
				);
			}
	
		}
		// Case Real Time Data
		else if($dataType=="RealTimeData") {
		
			// Copy parameters
// 			if ($action == "copy") {
		
// 				return array (
// 						"tableName" => "OpenData_Generations",
// 						"primaryKey" => "ID_Generation",
// 						"readFields" => "OpenData_Generations.ID_Generation, OpenData_Generations.ID_OpenData, OpenData.Category",
// 						"writeFields" => "OpenData_Generations.ID_Generation, OpenData_Generations.ID_OpenData, OpenData_Generations.TripleStart, OpenData_Generations.TripleEnd",
// 						"nestedTable" => "OpenData",
// 						"nestedTablePrimaryKey" => "Process",
// 						"foreignKey" => "ID_OpenData",
// 						"basePath" => "/media/Triples/",
// 						"classifier" => "Category"
// 				);
		
// 			}
		
			// Clone parameters
			if ($action == "clone") {
		
				return array (
						"tableName" => "OpenData_Generations",
						"primaryKey" => "ID_Generation",
						"readFields" => "OpenData_Generations.ID_Generation, OpenData_Generations.ID_OpenData, OpenData_Generations.TripleStart, OpenData_Generations.TripleEnd",
						"writeFields" => "OpenData_Generations.ID_Generation, OpenData_Generations.ID_OpenData, OpenData_Generations.TripleStart, OpenData_Generations.TripleEnd",
						"nestedTable" => "process_manager2",
						"nestedTablePrimaryKey" => "Process",
						"nestedTableFilterField" => "Real_time",
						"nestedTableFilterValue" => "yes",
						"foreignKey" => "ID_OpenData",
 						"insertPrimaryKey" => "ID_OpenData",
 						"insertCloneField" => "TripleStart",
						"insertCloneFieldEnd" => "TripleEnd"
				);
		
			}
		
			// Select parameters
			else if ($action == "select") {
		
				return  array (
						"tableName" => "OpenData_Generations",
						"fields" => "ID_Generation, ID_OpenData, TripleStart, TripleEnd",
						"updateFields" => array("TripleStart", "TripleEnd"),
						"primaryKey" => "ID_Generation",
						"foreignKey" => "ID_OpenData"
				);
			}
		
		}
		// Case Reconciliations
		else if($dataType=="Reconciliations") {
		
			// Copy parameters
			if ($action == "copy") {
		
				return array (
						"tableName" => "Reconciliations_Generations",
						"primaryKey" => "ID_Generation",
						"readFields" => "Reconciliations_Generations.ID_Generation, Reconciliations_Generations.ID_Reconciliation",
						"writeFields" => "Reconciliations_Generations.ID_Generation, Reconciliations_Generations.ID_Reconciliation, Reconciliations_Generations.TripleDate",
						"nestedTable" => "Reconciliations",
						"nestedTablePrimaryKey" => "Name",
						"foreignKey" => "ID_Reconciliation",
						"basePath" => "/media/Triples/Riconciliazioni",
						"classifier" => ""
				);
		
			}
		
			// Clone parameters
			else if ($action == "clone") {
		
				return array (
						"tableName" => "Reconciliations_Generations",
						"primaryKey" => "ID_Generation",
						"readFields" => "ID_Generation, ID_Reconciliation, TripleDate",
						"writeFields" => "ID_Generation, ID_Reconciliation, TripleDate",
						"insertPrimaryKey" => "ID_Reconciliation",
						"insertCloneField" => "TripleDate",
						"nestedTable" => "Reconciliations",
						"nestedTablePrimaryKey" => "Name",
						"foreignKey" => "ID_Reconciliation",
				);
		
			}
		
			// Select parameters
			else if ($action == "select") {
		
				return  array (
						"tableName" => "Reconciliations_Generations",
						"fields" => "ID_Generation, ID_Reconciliation, TripleDate",
						"updateFields" => array("TripleDate"),
						"primaryKey" => "ID_Generation",
						"foreignKey" => "ID_Reconciliation"
				);
			}
		
		}
		// Case Enrichments
		else if($dataType=="Enrichments") {
			if ($action == "select") {
			
				return  array (
						"tableName" => "enrichments_generations",
						"fields" => "ID_Generation, ID_Enrichment,Clone",
						"updateFields" => array("Clone"),
						"primaryKey" => "ID_Generation",
						"foreignKey" => "ID_Enrichment"
				);
		}
		
	}
}
	
	
	
	/**
	 *
	 * Copy a generation for a peculiar data type
	 *
	 * @param integer $currentGeneration The id of the current generation
	 * @param integer $column The id of the column to copy
	 * @param array $params The parameters to use to perform the action
	 * @param array $sql_details SQL Details
	 */
	static function copyGeneration($currentGeneration, $column, $params, $sql_details) {
	
		//
		// Select the elements to copy
		//
		$selectQuery = "SELECT " . $params['readFields'] . "
			FROM " . $params['tableName'] . "
			INNER JOIN " . $params['nestedTable'] . "
			ON " . $params['tableName'] . "." . $params['foreignKey'] . " = " . $params['nestedTable'] . "." . $params['nestedTablePrimaryKey'] . "
			AND " . $params['tableName'] . "." . $params['primaryKey'] . " = '$column'";			
		// For static and real time data, a filter is needed
		if (isset($params['nestedTableFilterField'])) {
			$selectQuery = $selectQuery . " AND " . $params['nestedTable'] . "." . $params['nestedTableFilterField'] . " = '". $params['nestedTableFilterValue'] . "'";
		}
		
		// Open the connection to the database
		$db = MySqlConnector::sql_connect ($sql_details);
	
		// Execute the select query
		$elements = MySqlConnector::sql_select($db, $selectQuery);
	
		// Set the delete query to remove previous elements, eventually existing
		$deleteQuery = "DELETE " . $params['tableName'] . "
			FROM " . $params['tableName'] . "
			INNER JOIN ". $params['nestedTable'] . "
			ON " . $params['tableName'] . "." . $params['foreignKey'] . " = " . $params['nestedTable'] . "." . $params['nestedTablePrimaryKey'] . "
			AND ". $params['tableName'] . ".". $params['primaryKey'] . " = '$currentGeneration'";
		// For static and real time data, a filter is needed
		if (isset($params['nestedTableFilterField'])) {
			$deleteQuery = $deleteQuery . " AND " . $params['nestedTable'] . "." . $params['nestedTableFilterField'] . " = '". $params['nestedTableFilterValue'] . "'";
		}
		
		
		// Set the insert query
		$insertQueryArray = array();
		
		//
		// Select the last version of elements
		//
		foreach ($elements as $element) {
	
			// Set the part of the insert query, related to this element
			$insertQueryArray[] =  "($currentGeneration, '" . $element[$params['foreignKey']] . "', '" .
					Versioner::getResourceLastVersion($params['basePath'] . $element[$params['classifier']] . "/" . $element[$params['foreignKey']]). "')";
				
		}
		$insertQuery = "INSERT INTO " . $params['tableName'] . " ("
				. $params['writeFields'] . " )" .
				" VALUES " . implode(", ", $insertQueryArray);
			
		// Execute the transaction
		self::copyOrCloneTransaction($db, $deleteQuery, $insertQuery);
	
	}
	
	
	/**
	 *
	 * Clone a generation for a peculiar data type
	 *
	 * @param integer $currentGeneration The id of the current generation
	 * @param integer $column The id of the column to clone
	 * @param array $params The parameters to use to perform the action
	 * @param array $sql_details SQL Details
	 */
	static function cloneGeneration($currentGeneration, $column, $params, $sql_details) {
	
		//
		// Select the elements to clone
		//
	
		// Select query, with two cases:
		// data to be filtered or not
		

		$selectQuery = "SELECT " . $params['readFields'] . " 
			FROM " . $params['tableName'] . " 
			INNER JOIN " . $params['nestedTable'] . " 
			ON " . $params['tableName'] . "." . $params['foreignKey'] . " = " . $params['nestedTable'] . "." . $params['nestedTablePrimaryKey'] . " 
			AND " . $params['tableName'] . "." . $params['primaryKey'] . " = '$column'";
		// For static and real time data, a filter is needed
		if (isset($params['nestedTableFilterField'])) {
			$selectQuery = $selectQuery . " AND " . $params['nestedTable'] . "." . $params['nestedTableFilterField'] . " = '". $params['nestedTableFilterValue'] . "'";
		}
					
		// Open the connection to the database
		$db = MySqlConnector::sql_connect ($sql_details);
	
		// Execute the select query
		$elements = MySqlConnector::sql_select($db, $selectQuery);
	
	
		// Set the delete query to remove previous elements, eventually existing
		$deleteQuery = "DELETE " . $params['tableName'] . "
			FROM " . $params['tableName'] . "
			INNER JOIN ". $params['nestedTable'] . "
			ON " . $params['tableName'] . "." . $params['foreignKey'] . " = " . $params['nestedTable'] . "." . $params['nestedTablePrimaryKey'] . "
			AND ". $params['tableName'] . ".". $params['primaryKey'] . " = '$currentGeneration'";
		// For static and real time data, a filter is needed
		if (isset($params['nestedTableFilterField'])) {
			$deleteQuery = $deleteQuery . " AND " . $params['nestedTable'] . "." . $params['nestedTableFilterField'] . " = '". $params['nestedTableFilterValue'] . "'";
		}
				
		// Set the insert query
		$insertQueryArray = array();
		// Analize each resource of the result data
		foreach ($elements as $element) {
			//TODO Vedere se riesco a fare un'unica espressione
			if(isset($params['insertCloneFieldEnd'])) {
				$insertQueryArray[] =  "($currentGeneration, '" . $element[$params['insertPrimaryKey']] . "', '" . $element[$params['insertCloneField']] . "', '" . $element[$params['insertCloneFieldEnd']] . "')";
			}
			else 
				$insertQueryArray[] =  "($currentGeneration, '" . $element[$params['insertPrimaryKey']] . "', '" . $element[$params['insertCloneField']] . "')";
		}
		$insertQuery = "INSERT INTO " . $params['tableName'] . " ("
				. $params['writeFields'] . ")
			VALUES " . implode(", ", $insertQueryArray);
	
		// Execute the transaction
		self::copyOrCloneTransaction($db, $deleteQuery, $insertQuery);
	
	}
	
	
	/**
	 *
	 * Select a version for a peculiar data
	 *
	 * @param integer $currentGeneration The id of the current generation
	 * @param integer $id The id of the element
	 * @param integer $version The version to select
	 * @param array $params The parameters to use to perform the action
	 * @param array $sql_details SQL Details
	 *
	 */
	static function selectItem($currentGeneration, $id, $version, $params, $sql_details) {
			
		// Update query section
		$updateQuery = "";
		foreach ($params['updateFields'] as $element) {
			$updateQuery = $updateQuery =="" ? $element . " = VALUES(" .$element . ")" : $updateQuery . ", " . $element . " = VALUES(" .$element . ")";
		}
		
		// If a version was selected, create an insert or update query
		if (implode("", $version) != "") {

			// Create the sql query
			// The implode is useful for real time data query		
			$query = "INSERT INTO " . $params['tableName'] . "
			(" . $params['fields'] . ")
				VALUES ('$currentGeneration','$id','" . implode ("','" , $version) . "')
			ON DUPLICATE KEY UPDATE " . $updateQuery;
			
		}
		// If a version was not selected, create a delete query
		else {
			$query = "DELETE FROM " . $params['tableName'] . " 
			WHERE " . $params['primaryKey'] . " = " . $currentGeneration . " AND " . $params['foreignKey'] . " = '" . $id . "'";
		}
	
		// Open the connection to the database
		$db = MySqlConnector::sql_connect ($sql_details);
	
		// Execute the insert or update query
		MySqlConnector::sql_exec($db, $query);
	
	}
	
	
	/**
	 *
	 * Performs a delete SQL and an insert SQL as a transaction
	 *
	 * @param resource $db Database handler
	 * @param string $deleteQuery Delete SQL query
	 * @param string $insertQuery Insert SQL query
	 * @throws PDOException
	 */
	static function copyOrCloneTransaction($db, $deleteQuery, $insertQuery) {
	
		try {
	
			// Begin the transaction
			$db->beginTransaction();
				
			// Remove previous elements, eventually existing
			MySqlConnector::sql_exec($db, $deleteQuery);
				
			// Execute the insert query
			MySqlConnector::sql_exec($db, $insertQuery);
				
			// Commit the transaction
			$db->commit();
	
		}
			
		// Catch: the transaction is rolled back
		catch(PDOException $e) {
	
			if(stripos($e->getMessage(), 'DATABASE IS LOCKED') !== false) {
				$db->commit();
				usleep(250000);
			} else {
				$db->rollBack();
				throw $e;
			}
		}
	
	}
	
}