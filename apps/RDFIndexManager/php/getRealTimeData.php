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
$table = 'process_manager2';

// Table's primary key
$primaryKey = 'Process';

// The generations parameter of the GET request are used
if(isset($_GET['generations'])) {
	$generations = $_GET['generations'];
}

// Show only the real time data that were selected in the current generation
if(isset($_GET['selected'])) {
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
	);

require_once ('dbDetails.php');
require_once ('ServerSideProcessor.class.php');
require_once ('Versioner.class.php');

// Get the data from the SQL database
$data = ServerSideProcessor::nested($_GET, $sql_details, $table, $primaryKey, $columns, $filter);

echo json_encode($data);
