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

class OD_DirectoryLister extends DirectoryLister
{
	protected $_home = null;
	
	function __construct()
	{
		parent::__construct();
	}
	
	function setAppUrl($url)
	{
		$this->_appURL.=$url;
	}
	
	function setRelativeHome($home)
	{
		$this->_home=$home;
	}
	
	function download($file)
	{
		if (file_exists($file)) {
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.basename($file));
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file));
		    readfile($file);
		    exit;
		}
		return false;
	}
	
	protected function _setDirectoryPath($dir) {
	
		// Check for an empty variable
		if (empty($dir) || $dir == '.') {
			return null; //'.';
		}
	
		// Eliminate double slashes
		while (strpos($dir, '//')) {
			$dir = str_replace('//', '/', $dir);
		}
	
		// Remove trailing slash if present
		if(substr($dir, -1, 1) == '/') {
			$dir = substr($dir, 0, -1);
		}
	
		// Verify file path exists and is a directory
		if (!file_exists($dir) || !is_dir($dir)) {
			// Set the error message
			$this->setSystemMessage('danger', '<b>ERROR:</b> File path does not exist');
	
			// Return the web root
			return null; //'.';
		}
	
		// Prevent access to hidden files
		if ($this->_isHidden($dir)) {
			// Set the error message
			$this->setSystemMessage('danger', '<b>ERROR:</b> Access denied');
	
			// Set the directory to web root
			return null; //'.';
		}
	
		// Prevent access to parent folders
		if (strpos($dir, '<') !== false || strpos($dir, '>') !== false
				|| strpos($dir, '..') !== false ){ //|| strpos($dir, '/') === 0) {
			// Set the error message
			$this->setSystemMessage('danger', '<b>ERROR:</b> An invalid path string was detected');
	
			// Set the directory to web root
			return null; //'.';
		} else {
			// Should stop all URL wrappers (Thanks to Hexatex)
			$directoryPath = $dir;
		}
	
		// Return
		return $directoryPath;
	}
	
	
	protected function _readDirectory($directory, $sort = 'natcase') {
	
		// Initialize array
		$directoryArray = array();
		$files= array();
		// Get directory contents
		if($directory)
			$files = scandir($directory);
	
		// Read files/folders from the directory
		foreach ($files as $file) {
	
			if ($file != '.') {
	
				// Get files relative path
				$relativePath = $directory . '/' . $file;
	
				if (substr($relativePath, 0, 2) == './') {
					$relativePath = substr($relativePath, 2);
				}
	
				// Don't check parent dir if we're in the root dir
				if ($this->_directory == '.' && $file == '..'){
	
					continue;
	
				} else {
	
					// Get files absolute path
					$realPath = realpath($relativePath);
	
					// Determine file type by extension
					if (is_dir($realPath)) {
						$iconClass = 'fa-folder';
						$sort = 1;
					} else {
						// Get file extension
						$fileExt = strtolower(pathinfo($realPath, PATHINFO_EXTENSION));
	
						if (isset($this->_fileTypes[$fileExt])) {
							$iconClass = $this->_fileTypes[$fileExt];
						} else {
							$iconClass = $this->_fileTypes['blank'];
						}
	
						$sort = 2;
					}
	
				}
	
				if ($file == '..') {
	
					if ($this->_directory != '.' && $this->_directory!=$this->_home) {
						// Get parent directory path
						$pathArray = explode('/', $relativePath);
						unset($pathArray[count($pathArray)-1]);
						unset($pathArray[count($pathArray)-1]);
						$directoryPath = implode('/', $pathArray);
	
						if (!empty($directoryPath)) {
							$directoryPath = '?dir=' . rawurlencode($directoryPath);
						}
	
						// Add file info to the array
						$directoryArray['..'] = array(
								'file_path'  => $this->_appURL . $directoryPath,
								'url_path'   => $this->_appURL . $directoryPath,
								'file_size'  => '-',
								'mod_time'   => date('Y-m-d H:i:s', filemtime($realPath)),
								'icon_class' => 'fa-level-up',
								'sort'       => 0
						);
					}
	
				} elseif (!$this->_isHidden($relativePath)) {
	
					// Add all non-hidden files to the array
					if ($this->_directory != '.' || $file != 'index.php') {
	
						// Build the file path
						$urlPath = implode('/', array_map('rawurlencode', explode('/', $relativePath)));
	
						if (is_dir($relativePath)) {
							$urlPath = '?dir=' . $urlPath;
						} else {
							$urlPath = "?get=".$urlPath;
						}
	
						// Add the info to the main array
						$directoryArray[pathinfo($relativePath, PATHINFO_BASENAME)] = array(
								'file_path'  => $relativePath,
								'url_path'   => $this->_appURL .$urlPath,
								'file_size'  => is_dir($realPath) ? '-' : $this->getFileSize($realPath),
								'mod_time'   => date('Y-m-d H:i:s', filemtime($realPath)),
								'icon_class' => $iconClass,
								'sort'       => $sort
						);
					}
	
				}
			}
	
		}
	
		// Sort the array
		$reverseSort = in_array($this->_directory, $this->_config['reverse_sort']);
		$sortedArray = $this->_arraySort($directoryArray, $this->_config['list_sort_order'], $reverseSort);
	
		// Return the array
		return $sortedArray;
	
	}
	
}