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

/*
 * Helper functions for get yyyy-mm-dd hh:mm:ss information from file system
 * 
 */

class Versioner {
	
	
	/**
	 * 
	 * Get the last version of a specific resource as yyyy-mm-dd hh:mm:ss string
	 * 
	 * @param string $directory the directory of the resource
	 * @return string a yyyy-mm-dd hh:mm:ss strings for a specific resource
	 */
	static function getResourceLastVersion($directory) {
		
		$out = self::getResourceVersions($directory, true);
		
		if (isset($out[0]))
			return $out[0]["Data"];
		else return null;
	}
	
	
	/**
	 * 
	 * Create an array of yyyy-mm-dd hh:mm:ss strings for a specific resource
	 * 
	 * @param string $directory the directory of the resource
	 * @param boolean $getLast set if only the most recent version should returned
	 * @return array an array of yyyy-mm-dd hh:mm:ss strings for a specific resource
	 */
	static function getResourceVersions($directory, $getLast=false) {
	
		// Output array
		$out = array ();
	
		$i=1;
		// For each element in the directory listing
		foreach(self::getResourceVersionsArray($directory, $getLast) as $version) {
	
			//TODO Mi va bene come formattazione?
			//TODO Va gestita l'ora legale/solare?
			// Transform into a yyyy-mm-dd hh:mm:ss string
			$out[] = array (
					"ID" => $i,
					"Data" => $version["year"] . "-" .
					$version["month"] . "-" .
					$version["day"] . " " .
					$version["hour"] . ":" .
					$version["minute"]. ":" .
					$version["second"]);
			$i++;
		}
	
		return $out;
	
	}
	
	
	/**
	 *
	 * Scan a directory looking for the various years, months, days, hours, minutes and seconds of a resource
	 *
	 * @param string $directory The directory to scan
	 * @param boolean $getLast set if only the most recent version should returned
	 * @return array an array of year, date, day, hour, minute and second
	 */
	static function getResourceVersionsArray($directory, $getLast=false) {
	
		// Output array
		$out = array ();
	
		// Check if the directory exist
		if(!file_exists($directory)) {
			return $out;
		}
		
		// Get the directory listing
		$dirList = scandir($directory, 1);
	
		// For each element in the directory listing
		foreach($dirList as $dirElement) {
	
			// Gets the year and month
			$year = substr($dirElement, 0, 4);
			$month = substr($dirElement, 5, 2);
	
			// Check if year and month are numeric
			if (is_numeric($year) & is_numeric($month)) {
	
				// Check if month is lower than or equal to 12
				if ($month <= 12) {
	
					// Get the array of day, hour, minutes and seconds for this month
					$DayHMSList = self::getMonthInfo($directory . "/" . $dirElement, $getLast);
	
					// For each day-hour-minute-second element, add the year and month
					foreach ($DayHMSList as &$dHMS) {
	
						$dHMS["year"] = $year;
						$dHMS["month"] = $month;
	
						// Add the element to the output array
						$out[] = $dHMS;
					}
				}
			}
			
			// If only the most recent version should returned, break the loop
			if ($getLast) {
				break;
			}
		}
	
		return $out;
	}
	
	
	/**
	 *
	 * Scan a directory looking for the various days, hours, minutes and seconds of a resource
	 *
	 * @param string $directory The directory to scan
	 * @param boolean $getLast set if only the most recent version should returned
	 * @return array an array of day, hour, minute and second
	 */
	static function getMonthInfo($directory, $getLast=false) {
	
		// Output array
		$out = array ();
	
		// Get the directory listing
		$dirList = scandir($directory, 1);
	
		// For each element in the directory listing
		foreach($dirList as $dirElement) {
	
			// Check if the element is numeric
			if (is_numeric($dirElement)) {
	
				// Check if day is lower than or equal to 31
				//TODO Controllo sul mese ???
				if ($dirElement <= 31) {
	
					// Get the array of hour, minutes and seconds for this day
					$hMSList = self::getDayInfo($directory . "/" . $dirElement, $getLast);
	
					// For each hour-minute-second element, add the day
					foreach ($hMSList as &$hMS) {
	
						$hMS["day"] = $dirElement;
	
						// Add the element to the output array
						$out[] = $hMS;
					}
				}
			}
					
			// If only the most recent version should returned, break the loop
			if ($getLast) {
				break;
			}
			
		}
	
		return $out;
	}
	
	
	/**
	 *
	 * Scan a directory looking for the various hours, minutes and seconds of a resource
	 *
	 * @param string $directory The directory to scan
	 * @param boolean $getLast set if only the most recent version should returned
	 * @return array an array of hour, minute and second
	 */
	static function getDayInfo($directory, $getLast=false) {
	
		// Output array
		$out = array ();
	
		// Get the directory listing
		$dirList = scandir($directory, 1);
	
		// For each element in the directory listing
		foreach($dirList as $dirElement) {
	
			// Check if the element is numeric
			//TODO Qui va cambiato per il discorso dell'ora solare
			if (is_numeric($dirElement)) {
	
				// Check if hour is lower than 24
				if ($dirElement < 24) {
	
					// Get the array of minutes and seconds for this hour
					$minuteSecondList = self::getHourInfos($directory . "/" . $dirElement, $getLast);
	
					// For each minute-second element, add the hour
					foreach ($minuteSecondList as &$minuteSecond) {
							
						$minuteSecond["hour"] = $dirElement;
							
						// Add the element to the output array
						$out[] = $minuteSecond;
					}
				}
			}
			
			// If only the most recent version should returned, break the loop
			if ($getLast) {
				break;
			}
		}
	
		return $out;
	}
	
	
	/**
	 *
	 * Scan a directory looking for the various minutes and seconds of a resource
	 *
	 * @param string $directory The directory to scan
	 * @param boolean $getLast set if only the most recent version should returned
	 * @return array an array of minute and second
	 */
	static function getHourInfos($directory, $getLast=false) {
	
		// Output array
		$out = array ();
	
		// Get the directory listing
		$dirList = scandir($directory, 1);
	
		// For each element in the directory listing
		foreach($dirList as $dirElement) {
	
			// Check if the element is numeric
			if (is_numeric($dirElement)) {
					
				// Gets the minute and second of the element
				$minute = substr($dirElement, 0 , 2);
				$second = substr($dirElement, 2);
					
				// Check if minute is lower than 60
				// and second is lower than or equal to 60 (for the leap second)
				if ($minute < 60 & $second <= 60) {
	
					$element = array();
					$element["minute"] = $minute;
					$element["second"] = $second;
	
					// Add the element to the output array
					$out[] = $element;
	
				}
			}
			
			// If only the most recent version should returned, break the loop
			if ($getLast) {
				break;
			}
		}
	
		return $out;
	}

	
	/**
	 *
	 * Returns a string with the path obtained from the specified datetime
	 *
	 * @param string $dateTime The datetime to analyze
	 * @return string a string with the path obtained from the specified datetime
	 */
	static function getPathFromDateTime($dateTime) {
		
		//TODO Qui sto supponendo che ricevo una stringa del tipo yyyy-mm-dd hh:mm:ss+0x
		
		$year = substr($dateTime, 0, 4);
		$month = substr($dateTime, 5, 2);
		$day = substr($dateTime, 8, 2);
		$hour = substr($dateTime, 11, 2);
		$minute = substr($dateTime, 14, 2);
		$second = substr($dateTime, 17, 2);
		$timeZone = substr($dateTime, 20, 2);
		
		return $year . "_" . $month . "/" . $day . "/" . $hour . "/" . $minute . $second;
		
		//TODO Non sto analizzando il caso dell'ora legale
		
	}
}


