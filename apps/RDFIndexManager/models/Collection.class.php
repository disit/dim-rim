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

class CollectionIterator implements Iterator {
	/**
	 * This is our collection class, defined later in article.
	 */
	private $Collection = null;
	/**
	 * Current index
	 */
	private $currentIndex = 0;
	/**
	 * Keys in collection
	 */
	private $keys = null;

	/**
	 * Collection iterator constructor
	 *
	 */
	public function __construct(Collection $Collection){
		// assign collection
		$this->Collection = $Collection;
		// assign keys from collection
		$this->keys = $Collection->keys();
	}

	/**
	 * Implementation of method current
	 *
	 * This method returns current item in collection based on currentIndex.
	 */
	public function current(){
		return $this->Collection->get($this->key());
	}

	/**
	 * Get current key
	 *
	 * This method returns current items' key in collection based on currentIndex.
	 */
	public function key(){
		return $this->keys[$this->currentIndex];
	}

	/**
	 * Move to next idex
	 *
	 * This method increases currentIndex by one.
	 */
	public function next(){
		++$this->currentIndex;
	}

	/**
	 * Rewind
	 *
	 * This method resets currentIndex by setting it to 0
	 */
	public function rewind(){
		$this->currentIndex = 0;
	}

	/**
	 * Check if current index is valid
	 *
	 * This method checks if current index is valid by checking the keys array.
	 */
	public function valid(){
		return isset($this->keys[$this->currentIndex]);
	}
}

class Collection implements IteratorAggregate {

  /**
   * This is our array with data (collection)
   */
  private $data = array();

  /**
   * Get iterator for this collection
   *
   * This method will return <b>CollectionIterator</b> object we wrote before.
   */
  public function getIterator(){
    return new CollectionIterator($this);
  }

  /**
   * Add item to collection
   *
   * This method will add item to collection.
   *
   * If you do now provide the key, key will be next available.
   */
  public function add($item, $key = null){
    if ($key === null){
      // key is null, simply insert new data
      $this->data[] = $item;
    }
    else {
      // key was specified, check if key exists
      if (isset($this->data[$key]))
        throw new ECollectionKeyInUse($key);
      else
        $this->data[$key] = $item;
    }
  }

  /**
   * Get item from collection by key
   *
   */
  public function get($key){
    if (isset($this->data[$key]))
      return $this->data[$key];
    else
      throw new ECollectionKeyInvalid($key);
  }

  /**
   * Remove item from collection
   *
   * This method will remove item from collection.
   */
  public function remove($key){
    // check if key exists
    if (!isset($this->data[$key]))
      throw new ECollectionKeyInvalid($key);
    else
      unset($this->data[$key]);
  }

  /**
   * Get all items in collection
   */
  public function getAll(){
    return $this->data;
  }

  /**
   * Get all the keys in collection
   *
   */
  public function keys(){
    return array_keys($this->data);
  }

  /**
   * Get number of entries in collection
   */
  public function length(){
    return count($this->data);
  }

  /**
   * Clear the collection
   *
   * This method removes all the item from the collection
   */
  public function clear(){
    $this->data = array();
  }

  /**
   * Check if key exists in collection
   */
  public function exists($key){
    return isset($this->data[$key]);
  }
}