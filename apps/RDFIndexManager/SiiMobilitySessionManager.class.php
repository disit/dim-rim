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

class SiiMobilitySessionManager extends MySqlConfig {

	protected $remoteIP;
	
	function __construct()
	{
		parent::__construct();
		$this->remoteIP = $_SERVER["REMOTE_ADDR"];
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
	 * @desc Create a new empty session 
	 * 
	 * @param string the response body
	 */
	public function new_session(){
				
		$gen = new Generation();
		$gen->insert();
		$data=array();
		$data['id'] = $gen->getID();
		$data['repositoryID']=$gen->getRepositoryID();
		// Return a JSON representation of the new session
		return $data;
	}
	
	
	/**
	 * 
	 * Edit an existing session
	 * 
	 * @param string $id the id of the session to edit
	 */
	public function update_session($id,$data){
		
		// Parse the JSON in the request body
		$put_body = $data; //json_decode($data, true);
		
		// Gets (from the request body) the repository ID
		if(isset($put_body['repositoryID']))
			$repositoryID = $put_body['repositoryID'];
		else {
			sm_send_error(400,"No repositoryID received"); //$this->_response("", 500);
			return;
		}
		
		// Gets (from the request body) the status
		if(isset($put_body['status']))
			$status = $put_body['status'];
		else {
			sm_send_error(400,"No status received"); //$this->_response("", 500);
			return;
		}
		
				
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
		
		else{
			sm_send_error(400,""); //$this->_response("", 500);
			return;
		}
		
		// Open the connection to the database
		$db = MySqlConnector::sql_connect($this->sql_details);
		
		// Update the session in the database
		$data = MySqlConnector::sql_exec($db, $updateQuery);
			
		
		// Catch errors
		if ($data == "0") {
			 sm_send_error(500,""); //$this->_response("", 500);
			 return;
		}
		
		// Return a representation of the session
		return array('status' => $status, 'repositoryID' =>$repositoryID);
	}
	
	
		
}