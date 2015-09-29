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

require_once ('dbDetails.php');
require_once ('ServerSideProcessor.class.php');

// The id of the data
$id = $_GET['id'];

//Create the bindings array
$bindings = array ();

// Get an handle to the database connection
$db = MySqlConnector::sql_connect ($sql_details);

// The query to get the data
$data = MySqlConnector::sql_select( $db, $bindings,
		"SELECT * 
		FROM process_manager2 
		WHERE Process='$id'
		LIMIT 1");

// The array of MySql have double key pairs: a numeric and a string one
// This cicle remove the numeric one
foreach(array_keys($data[0]) as $key) {
	if(is_int($key)) {
		unset($data[0][$key]);
	}
}
	
echo json_encode($data[0]);