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

class IndexSecurityLevels
{
	const L_OPEN = '1';
	const L_PRIVATE = '2';
	const L_SENSIBLE = '3';
	const L_CRITICAL = '4';
	
	static function toString($level)
	{
		if(is_numeric($level))
			$level = sprintf("%d",$level);
		switch($level){
			case self::L_OPEN:
			$ret =  "OPEN";
			break;
			case self::L_PRIVATE:
				$ret =  "PRIVATE";
			break;
			case self::L_SENSIBLE:
				$ret =  "SENSIBLE";
			break;
			case self::L_CRITICAL:
				$ret =  "CRITICAL";
			break;
			default:
				$ret = "UNKNOWN";
			break;
		}
		return $ret;
	}
}