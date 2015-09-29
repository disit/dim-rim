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
require_once 'MySqlConnector.class.php';

/**
 * API manager class for the administrator sessions
*
* @author Riccardo Billero
*
*/
class Session extends API {

	public function __construct($request, $sql_details, $remoteIP){
		parent::__construct($request, $sql_details, $remoteIP);
	}
	

	/**
	 * 
	 * Manages the HTTP /session/* requests
	 * 
	 * @return string the response body
	 */
	public function processAPI() {
		
		// If there is more then an argument, the URI is wrong
		if (count($this->args) <> 1) {
			return $this->_response("", 400);
		}
		
		// Get the first argument
		$id = $this->args[0];
		
		// If the argument is null, then a / request was made
		if (($id == "")) {
		
			// Switch according to the required method
			switch ($this->method) {
				case 'GET' :
					return $this->_getSessions();
					break;
				case 'POST' :
					return $this->_postSession();
					break;
				default :
					return $this->_response("", 405);
			}
		}
		// If the argument is an integer, then a /{id} request was made
		else if ((is_numeric($id) && (int) $id == $id)) {
		
			// Switch according to the required method
			switch ($this->method) {
				case 'GET' :
					return $this->_getSession($id);
					break;
				case 'PUT' :
					return $this->_putSession($id);
					break;
				default :
					return $this->_response("", 405);
			}
		}
		// Otherwise return a 404 HTTP status
		else return $this->_response("", 404);
		
	}
	
	
	/**
	 * 
	 * Get an administrator session
	 * 
	 * @param string $id the id of the session to return
	 */
	private function _getSession($id){
		return "get a session";
	}
	
	
	/**
	 * 
	 * Get all the administrator sessions
	 * 
	 * @param string the response body
	 */
	private function _getSessions(){
		return "get all sessions";
	}
	
	
	/**
	 * 
	 * Create a new session
	 * 
	 * @param string the response body
	 */
	private function _postSession(){
				
		// Open the connection to the database
		$db = MySqlConnector::sql_connect($this->sql_details);
	
		// Insert a new session in the database
		$data = MySqlConnector::sql_insert($db,  
			"INSERT INTO Generations (SessionStart, SessionIP) VALUES (CURRENT_TIMESTAMP, '" . $this->remoteIP . "')");
		
		// Catch error on the session creation
		if ($data == "0") {
			return $this->_response("", 500);
		}
		
//		header("Location: http://http://150.217.15.64:40080/indexgenerator/v1/session/$data" );
		// Return a JSON representation of the new session
		return json_encode(array('id' => $data));
	}
	
	
	/**
	 * 
	 * Edit an existing session
	 * 
	 * @param string $id the id of the session to edit
	 */
	private function _putSession($id){
		
		// Parse the JSON in the request body
		$put_body = json_decode($this->requestbody, true);
		
		// Gets (from the request body) the repository ID
		if(isset($put_body['repositoryID']))
			$repositoryID = $put_body['repositoryID'];
		else return $this->_response("", 400);
		
		// Gets (from the request body) the status
		if(isset($put_body['status']))
			$status = $put_body['status'];
		else return $this->_response("", 400);
		
				
		// Case status SESSION END
		if ($status == "sessionEnd") {		
			// Update query: the session is ended
			$updateQuery = "UPDATE Generations SET SessionEnd = CURRENT_TIMESTAMP, RepositoryID = '$repositoryID' WHERE ID = $id";
		}
		
		// Case status GENERATION START
		else if ($status == "generationStart") {
			// Update query: the generation is started
			$updateQuery = "UPDATE Generations SET GenerationStart = CURRENT_TIMESTAMP, RepositoryID = '$repositoryID'  WHERE ID = $id";
		}
		
		// Case status GENERATION END
		else if ($status == "generationEnd") {
			// Update query: the generation is started
			$updateQuery = "UPDATE Generations SET GenerationEnd = CURRENT_TIMESTAMP, RepositoryID = '$repositoryID'  WHERE ID = $id";
				
		}
		
		else return $this->_response("", 400);
		
		// Open the connection to the database
		$db = MySqlConnector::sql_connect($this->sql_details);
		
		// Update the session in the database
		$data = MySqlConnector::sql_exec($db, $updateQuery);
			
		
		// Catch errors
		if ($data == "0") {
			return $this->_response("", 500);
		}
		
		// Return a JSON representation of the session
		return json_encode(array('status' => $status, 'repositoryID' =>$repositoryID));
	}
		
}