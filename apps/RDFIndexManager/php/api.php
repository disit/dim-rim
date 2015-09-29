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

require_once 'API.class.php';
require_once 'Session.class.php';
require_once 'Script.class.php';
require_once 'dbDetails.php';

// Gets the endpoint
$endpoint = $_REQUEST["endpoint"];

// Case /session/* request
if ($endpoint == "session") {
	
	// Gets the request arguments
	$args = explode ('/', rtrim($_REQUEST['request'], '/'));
	
	// If there are more then two arguments, there is an error
	if (count($args) > 2) {
		header("HTTP/1.1 400 Bad Request");
		return;
	}
	// If there are two arguments, check if the second is script
	else if (count($args) == 2) {
		
		// Case /session/{id}/script HTTP request
		if ($args[1] == "script") {
			$API = new Script($_REQUEST, $sql_details, $_SERVER["REMOTE_ADDR"]);
		}
		else {
			header("HTTP/1.1 400 Bad Request");
			return;
		}
	}
	// Otherwise /session/* HTTP request
	else {
		$API = new Session($_REQUEST, $sql_details, $_SERVER["REMOTE_ADDR"]);
	}
}
// Case bad request
else {
	header("HTTP/1.1 404 Not Found");
	return;
}


// Process the HTTP request
echo $API->processAPI();