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

// DB table to use
$table = 'Reconciliations';

// Table's primary key
$primaryKey = 'Name';

// The generations parameter of the GET request are used
if(isset($_GET['generations'])) {
	$generations = $_GET['generations'];
}

// Show only the reconciliations that were selected in the current generation
if(isset($_GET['selected'])) {
	$selectionField = "TripleDate";
}
else $selectionField = null;

// A global filter of data
$filter = array (
		'selectionTable' => 'SelectedVersion_Table',
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
	);

require_once ('dbDetails.php');
require_once ('ServerSideProcessor.class.php');
require_once ('Versioner.class.php');

// Get the data from the SQL database
$data = ServerSideProcessor::nested($_GET, $sql_details, $table, $primaryKey, $columns, $filter);

// Analize each resource of the result data
 foreach ($data["data"] as &$item) {

 	// Get the versions of the resource
 	$item["Versions"] = Versioner::getResourceVersions("/media/Triples/Riconciliazioni/" . $item["Name"]);
	
 	// Turn the value of the selected version into the index of the element in the Versions array
 	foreach ($item["Versions"] as $version) {
 		if ($item["SelectedVersion"] == $version["Data"]) {
 			$item["SelectedVersionIndex"] = $version["ID"];
 		}
 	}
	
 	// Get the last file date of the resource
 	$item["LastFileDate"] = Versioner::getResourceLastVersion("/media/Triples/Riconciliazioni/" . $item["Name"]);
 	
 }

echo json_encode($data);
