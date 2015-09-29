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

class OWLIM_RestController extends APIRest_Controller
{
	/**
	 * @desc Create an OWLIM repository
	 *
	 * @url GET owlim/create/:name
	 *
	 */
	function create_repository($name=null){
		$owlim = new OWLIM();
		if($owlim->exist($name))
			$response['response']= new APIRest_Response('message','API Create Repository',"Repository Already Exists");
		else 
		{
			$ret = $owlim->create($name);
			$response['response']= new APIRest_Response('message','API Create Repository',$ret);
		}
		
		return $response;
	}
	
	/**
	 * @desc Delete an OWLIM repository
	 *
	 * @url DELETE owlim/delete/:name
	 *
	 */
	function delete_repository($name=null){
		$owlim = new OWLIM();
		if(!$owlim->exist($name))
			$response['response']= new APIRest_Response('message','API Delete Repository',"Repository Does Not Exist");
		else
		{
			$ret = $owlim->delete($name);
			$response['response']= new APIRest_Response('message','API Delete Repository',$ret);
		}
	
		return $response;
	}
	
	/**
	 * @desc Clear an OWLIM repository
	 *
	 * @url GET owlim/clear/:name
	 *
	 */

	function clear_repository($name=null)
	{
		$owlim = new OWLIM();
		if(!$owlim->exist($name))
			$response['response']= new APIRest_Response('message','API Clear Repository',"Repository Does Not Exist");
		else
		{
			$ret = $owlim->clear($name);
			$response['response']= new APIRest_Response('message','API Clear Repository',$ret);
		}
		
		return $response;
	}
	
	/**
	 * @desc Flush an OWLIM repository
	 *
	 * @url GET owlim/flush/:name
	 *
	 */
	function flush_repository($name=null){
		$owlim = new OWLIM();
		if($owlim->exist($name))
		{
			$ret = $owlim->flush($name);
			$response['response']= new APIRest_Response('message','API Flush Repository',$ret);
		}
		else 
			$response['response']= new APIRest_Response('message','API Flush Repository',"Repository does not exists");
		
		return $response;
	}
	
	/**
	 * @desc Create Geo Spatial Index in a OWLIM repository
	 *
	 * @url GET owlim/create/geospatial/:name
	 *
	 */
	function create_geospatial_index($name=null){
		$owlim = new OWLIM();
		if($owlim->exist($name))
		{
			$ret = $owlim->creategeospatial($name);
			$response['response']= new APIRest_Response('message','API Flush Repository',$ret);
		}
		else
			$response['response']= new APIRest_Response('message','API Flush Repository',"Repository does not exists");
	
		return $response;
	}
	
	/**
	 * @desc Check if an OWLIM repository exits
	 *
	 * @url GET owlim/exist/:name
	 *
	 */
	function exist_repository($name=null){
		$owlim = new OWLIM();
		$ret = $owlim->exist($name);
		$response['response']= new APIRest_Response('message','API Exist Repository',$ret);
		return $response;
	}
}