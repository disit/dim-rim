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


require ('../config.php');

// DB table to use
// $table = 'process_manager';
$table = 'process_manager2';

// Table's primary key
// $primaryKey = 'Process';
$primaryKey = 'Process';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array (
		array (
				'db' => 'process',
				'dt' => 'Process ID' 
		),
		array (
				'db' => 'Resource',
				'dt' => 'Resource' 
		),
		array (
				'db' => 'Category',
				'dt' => 'Category' 
		),
		array (
				'db' => 'Format',
				'dt' => 'Format' 
		),
		array (
				'db' => 'Automaticity',
				'dt' => 'Automaticity' 
		),
		array (
				'db' => 'Process_type',
				'dt' => 'Process type' 
		),
		array (
				'db' => 'Access',
				'dt' => 'Access' 
		),
		array (
				'db' => 'Source',
				'dt' => 'Source' 
		),
		array (
				'db' => 'A',
				'dt' => 'A' 
		),
		array (
				'db' => 'B',
				'dt' => 'B' 
		),
		array (
				'db' => 'status_A',
				'dt' => 'Status A' 
		),
		array (
				'db' => 'status_B',
				'dt' => 'Status B' 
		),
		array (
				'db' => 'status_C',
				'dt' => 'Status C' 
		),
		array (
				'db' => 'time_A',
				'dt' => 'Time A' 
		),
		array (
				'db' => 'time_B',
				'dt' => 'Time B' 
		),
		array (
				'db' => 'time_C',
				'dt' => 'Time C' 
		),
		array (
				'db' => 'exec_A',
				'dt' => 'Exec A' 
		),
		array (
				'db' => 'exec_B',
				'dt' => 'Exec B' 
		),
		array (
				'db' => 'exec_C',
				'dt' => 'Exec C' 
		),
		array (
				'db' => 'period',
				'dt' => 'Period' 
		),
		array (
				'db' => 'overtime',
				'dt' => 'Overtime' 
		),
		array (
				'db' => 'param',
				'dt' => 'Parameters' 
		),
		array (
				'db' => 'last_update',
				'dt' => 'Last Update' 
		),
		array (
				'db' => 'last_triples',
				'dt' => 'Last Triples' 
		),
		array (
				'db' => 'error',
				'dt' => 'Error' 
		),
		array (
				'db' => 'description',
				'dt' => 'Description' 
		) 
);

// SQL server connection information
$sql_details = array (
		'user' => $DB_user,
		'pass' => $DB_password,
		'db' => $DB_name2,
		'host' => $DB_host 
);

require ('ssp.class.php');

echo json_encode ( SSP::simple ( $_GET, $sql_details, $table, $primaryKey, $columns ) );
