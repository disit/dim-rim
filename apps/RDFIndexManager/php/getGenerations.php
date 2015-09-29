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

	// Opens the connections towards the database
	$db = mysqli_connect($sql_details['host'],$sql_details['user'],$sql_details['pass'],$sql_details['db']) or die('Could not connect to database');
			
	
	echo getGenerations($db);
						
	mysqli_close($db);
		

	/**
	 * 
	 * Returns a JSON with the ID and Date of all the generations
	 * 
	 * @param mysqli $db a connections to a database
	 */
	function getGenerations($db) {
		
		// Query to select the last element of the loadings
		//TODO Rimuovi blocco generation
		
		$loadingsQuery = "SELECT * FROM Generations WHERE SessionEnd ORDER BY ID DESC";
//		$loadingsQuery = "SELECT * FROM Generations ORDER BY ID DESC";
		$loadings = mysqli_query($db, $loadingsQuery);
		
 		$result = array();
 		for ($i = 0; $i < mysqli_num_rows($loadings); $i++) {
 			$result[$i] = mysqli_fetch_assoc($loadings);
 		}
		
 		return json_encode($result);
 		
	}
	
	
	

?>