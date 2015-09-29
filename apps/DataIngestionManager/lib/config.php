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


return array(

    // Basic settings
    'hide_dot_files'     => true,
    'list_folders_first' => true,
    'list_sort_order'    => 'natcasesort',
    'theme_name'         => 'bootstrap',

    // Hidden files
    'hidden_files' => array(
        '.ht*',
        '*/.ht*',
        'resources',
        'resources/*',
        'analytics.inc',
        'header.php',
        'footer.php'
    ),
	'hidden_files' => array(),
    // Files that, if present in a directory, make the directory a direct link
    // rather than a browse link.
    'index_files' => array(
        'index.htm',
        'index.html',
        'index.php'
    ),

    // File hashing threshold
    'hash_size_limit' => 268435456, // 256 MB

    // Custom sort order
    'reverse_sort' => array(
        // 'path/to/folder'
    )

);
