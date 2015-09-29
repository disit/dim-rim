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
 * The main REST PHP API class
 * 
 */
abstract class API {
	
	// The HTTP method of this request, either GET, POST, PUT or DELETE
	protected $method = '';
	
	// The requested endpoint
	protected $endpoint = '';
	
	// The given arguments
	protected $args = Array ();
	
	// The SQL details
	protected $sql_details = Array ();
	
	// The IP of the remote client
	protected $remoteIP = '';
	
	// The body of the request
	protected $requestbody = Null;
	
	
	/**
	 * The API class constructor
	 * 
	 */
	public function __construct($request, $sql_details, $remoteIP) {

		// Set the response headers
		header("Content-Type: application/json" );
		
		// Gets the request arguments
		$this->args = explode ('/', rtrim($request['request'], '/'));
		
		// Gets the request
		$this->request = $request;
		
		// Gets the endpoint
		$this->endpoint = $request['endpoint'];
		
		// Gets the request method
		$this->method = $_SERVER['REQUEST_METHOD'];
		
		// Gets the SQL details
		$this->sql_details = $sql_details;
		
		// Sets the IP of the remote client
		$this->remoteIP = $remoteIP;

		// Switch according to the required method
		switch ($this->method) {
			case 'DELETE' :
				break;
			case 'POST' :
				$this->requestbody = file_get_contents("php://input");
				break;
			case 'GET' :
				$this->requestbody = file_get_contents("php://input");
				break;
			case 'PUT' :
				$this->requestbody = file_get_contents("php://input");
				break;
			default :
				$this->_response("", 405);
				break;
		}
	
	}
	
	
	/**
	 * Request redirection
	 * 
	 * @return string the HTTP response
	 */
    public function processAPI() {  	
    	
        if ((int)method_exists($this, $this->endpoint) > 0) {
            return $this->_response($this->{$this->endpoint}($this->args));
        }
        return $this->_response("No Endpoint: $this->endpoint", 404);
    }
    
    
    /**
	 * 
	 * Prepare an HTTP response, setting up the response status and the response body as a JSON message
	 * 
	 * @param blob $data the response body
	 * @param number $status the response status
	 * @return string a JSON representation of the response body
	 */
	protected function _response($data, $status = 200) {
		
		// Set the response status
		header("HTTP/1.1 " . $status . " " . $this->_responseStatus($status));
		
		// Set the response body
		return json_encode($data);
	}
	
	
	/**
	 * Returns the proper string for the required response status code
	 * 
	 * @param int $code the required response status
	 * @return string the proper string for the required response status code
	 */
	private function _responseStatus($code) {
		
		$status = array(
				200 => 'OK',
				400 => 'Bad Request',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				500 => 'Internal Server Error',
		);
		
		return ($status[$code])?$status[$code]:$status[500];
	}
	
}