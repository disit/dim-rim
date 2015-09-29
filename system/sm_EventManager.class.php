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
 *
 */

class sm_EventManager {

    protected static $_handlers = array();

    /**
     * Add an event handler
     *
     * Handlers can also abort processing by throwing an exception;
     * 
     * @param string   $name    Name of the event
     * @param callable $handler Code/Function to run
     *
     * @return void
     */

    public static function addHandler($name, $handler) {
    	if(!sm_EventManager::hasHandler($name,$handler[0]))
    	{	
	        if (array_key_exists($name, sm_EventManager::$_handlers)) {
	            sm_EventManager::$_handlers[$name][] = $handler;
	        } else {
	            sm_EventManager::$_handlers[$name] = array($handler);
	        }
    	}
    }

    /**
     * Handle an event
     *
     *
     * @param string $name Name of the event that's happening
     * @param array  $args Arguments for handlers
     *
     * @return boolean flag to continue processing
     */

       public static function handle(sm_Event &$event) {
        $result = null;
        $name=$event->getType();
        if (array_key_exists($name, sm_EventManager::$_handlers)) {
            foreach (sm_EventManager::$_handlers[$name] as $handler) {
            	$obj = new $handler[0];
            	$result = call_user_func_array(array($obj,$handler[1]), array(&$event));
            	unset($obj);
                if ($result === false || $event->hasToStop()) {
                    break;
                }
            }
        }
        return ($result !== false);
    }

    /**
     * Check to see if an event handler exists
     *
     * @param string $name Name of the event to look for
     * @param string $plugin Optional name of the plugin class to look for
     *
     * @return boolean flag 
     *
     */

    public static function hasHandler($name, $class=null) {
        if (array_key_exists($name, sm_EventManager::$_handlers)) {
            if (isset($class)) {
                foreach (sm_EventManager::$_handlers[$name] as $handler) {
                    if ($handler[0] == $class) {
                        return true;
                    }
                }
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * Disables any and all handlers.
     */
    public static function clearHandlers() {
        sm_EventManager::$_handlers = array();
    }


    /**
     * Add Event Handler class
     * Searching for method starting with "on"
     * 
     * @param unknown $class
     */
    static public function addEventHandler($class)
    {   
    	$reflection = new ReflectionClass($class);
    	$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    
    	foreach ($methods as $method) {
    		if (mb_substr($method->getName(), 0, 2) == 'on') {
    			sm_EventManager::addHandler(mb_substr($method->getName(), 2), array($class, $method->getName()));
    		}
    	}
    }
}

