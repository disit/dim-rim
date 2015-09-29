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

class GraphDB_RestController extends APIRest_Controller
{
	/**
	 * @desc Create an GraphDB repository
	 *
	 * @url GET GraphDB/create/:name
	 *
	 */
	function create_repository($name=null){
		$GraphDB = new GraphDB();
		if($GraphDB->exist($name))
			$response['response']= new APIRest_Response('message','API Create Repository',"Repository Already Exists");
		else 
		{
			$ret = $GraphDB->create($name);
			$response['response']= new APIRest_Response('message','API Create Repository',$ret);
		}
		
		return $response;
	}
	
	/**
	 * @desc Delete an GraphDB repository
	 *
	 * @url DELETE GraphDB/delete/:name
	 *
	 */
	function delete_repository($name=null){
		$GraphDB = new GraphDB();
		if(!$GraphDB->exist($name))
			$response['response']= new APIRest_Response('message','API Delete Repository',"Repository Does Not Exist");
		else
		{
			$ret = $GraphDB->delete($name);
			$response['response']= new APIRest_Response('message','API Delete Repository',$ret);
		}
	
		return $response;
	}
	
	/**
	 * @desc Clear an GraphDB repository
	 *
	 * @url GET GraphDB/clear/:name
	 *
	 */

	function clear_repository($name=null)
	{
		$GraphDB = new GraphDB();
		if(!$GraphDB->exist($name))
			$response['response']= new APIRest_Response('message','API Clear Repository',"Repository Does Not Exist");
		else
		{
			$ret = $GraphDB->clear($name);
			$response['response']= new APIRest_Response('message','API Clear Repository',$ret);
		}
		
		return $response;
	}
	
	/**
	 * @desc Flush an GraphDB repository
	 *
	 * @url GET GraphDB/flush/:name
	 *
	 */
	function flush_repository($name=null){
		$GraphDB = new GraphDB();
		if($GraphDB->exist($name))
		{
			$ret = $GraphDB->flush($name);
			$response['response']= new APIRest_Response('message','API Flush Repository',$ret);
		}
		else 
			$response['response']= new APIRest_Response('message','API Flush Repository',"Repository does not exists");
		
		return $response;
	}
	
	/**
	 * @desc Create Geo Spatial Index in a GraphDB repository
	 *
	 * @url GET GraphDB/create/geospatial/:name
	 *
	 */
	function create_geospatial_index($name=null){
		$GraphDB = new GraphDB();
		if($GraphDB->exist($name))
		{
			$ret = $GraphDB->creategeospatial($name);
			$response['response']= new APIRest_Response('message','API Create Geo Spatial Repository',$ret);
		}
		else
			$response['response']= new APIRest_Response('message','API Create Geo Spatial Repository',"Repository does not exists");
	
		return $response;
	}
	
	/**
	 * @desc Check if an GraphDB repository exits
	 *
	 * @url GET GraphDB/exist/:name
	 *
	 */
	function exist_repository($name=null){
		$GraphDB = new GraphDB();
		$ret = $GraphDB->exist($name);
		$response['response']= new APIRest_Response('message','API Exist Repository',$ret);
		return $response;
	}
}