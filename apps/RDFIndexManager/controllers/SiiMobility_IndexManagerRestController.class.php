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

class SiiMobility_IndexManagerRestController extends APIRest_Controller
{
	/**
	 * @desc Update a existing Editing Session
	 *
	 * @url PUT IndexGenerator/session/:id
	 *
	 */
	function update_session($id=null,$data=null)
	{
		if($id && $data)
		{
			$man = new SiiMobilitySessionManager();
			sm_Logger::write($data);
			$ret = $man->update_session($id,json_decode($data,true));
			$response['response']= new APIRest_Response('message','API Update Session',$ret);
		}
		else
			throw new SM_RestException(400, "Invalid Parameters");
		return $response;
	}
	
	/**
	 * @desc Get an XML of a repository
	 *
	 * @url GET IndexGenerator/xport/:id
	 *
	 */
	function export($id=null)
	{
		if($id)
		{
			$rep = new SiiMobilityRepository();
			$rep->load($id);
			$xml = $rep->saveXML(); 
			//$response['response']= $xml;
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: 0");
			header('Content-Type: application/xml');
			echo $xml;
		}
		else
			throw new SM_RestException(400, "Invalid Parameters");
		exit();
	}
	
	/**
	 * @desc POST an lock for some data of a repository
	 *
	 * @url POST IndexGenerator/lock/:id
	 * @url POST IndexGenerator/lock/:id/:data
	 * @url POST IndexGenerator/lock/:id/:data/:key
	 *
	 */
	function lock($id=null,$data=null,$key=null)
	{
		if($id)
		{  
			
			$data=$data==null?"all":$data;
			$rep = new SiiMobilityRepository();
			$rep->load($id);
			$rep->lock($data,$key);
			$response['response']= new APIRest_Response('message','API Lock Data',"true");
		}
		else
			throw new SM_RestException(400, "Invalid Parameters");
		return $response;
	}
	
	/**
	 * @desc POST an un lock for some data of a repository
	 *
	 * @url POST IndexGenerator/unlock/:id
	 * @url POST IndexGenerator/unlock/:id/:data
	 * @url POST IndexGenerator/unlock/:id/:data/:key
	 * 
	 */
	function unlock($id=null,$data=null,$key=null)
	{
		if($id)
		{
			$data=$data==null?"all":$data;
			$rep = new SiiMobilityRepository();
			$rep->load($id);
			$rep->unlock($data,$key);
			$response['response']= new APIRest_Response('message','API UnLock Data',"true");
		}
		else
			throw new SM_RestException(400, "Invalid Parameters");
		return $response;
	}
	
	/**
	 * @desc POST a commit for some data of a repository
	 *
	 * @url POST IndexGenerator/commit/:id
	 * @url POST IndexGenerator/commit/:id/:data
	 * @url POST IndexGenerator/commit/:id/:data/:key
	 *
	 */
	function commit($id=null,$data=null,$key=null)
	{
		if($id)
		{  
			
			$data=$data==null?"all":$data;
			$rep = new SiiMobilityRepository();
			$rep->load($id);
			$rep->commit($data,$key);
			$response['response']= new APIRest_Response('message','API Commit Data',"true");
		}
		else
			throw new SM_RestException(400, "Invalid Parameters");
		return $response;
	}
}