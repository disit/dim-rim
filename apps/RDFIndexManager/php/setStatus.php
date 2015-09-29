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

	require_once 'dbDetails.php';
	require ('StatusManager.class.php');
	
	// Get the current session ID
	$currentSession = $_GET['currentSession'];
	
	//
	//
	// REQUEST PARAMETERS
	//
	//
	
	// The data type: Ontologies, StaticData, RealTimeData or Reconciliations
	$dataType = $_GET['dataType'];
	
	// The action to perform: copy, clone or select
	$action = $_GET['action'];
	
	// The column for the copy or clone action
	if (isset($_GET['column'])) {
		$column = $_GET['column'];
	}
	
	// The id and version of the selected data
	if (isset($_GET['select'])) {
		$select = $_GET['select'];
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
		StatusManager::copyGeneration($currentSession, $column, $params, $sql_details);
	}
	// Clone
	else if ($action == "clone") {
		StatusManager::cloneGeneration($currentSession, $column, $params, $sql_details);		
	}
	// Select
	else if ($action == "select") {
		StatusManager::selectItem($currentSession, $id, $version, $params, $sql_details);
	}
	
?>