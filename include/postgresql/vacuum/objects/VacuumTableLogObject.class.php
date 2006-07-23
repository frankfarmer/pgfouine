<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2006 Open Wide
 * Copyright (c) 2006 Guillaume Smet
 *
 * pgFouine is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * pgFouine is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with pgFouine; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

class VacuumTableLogObject extends VacuumLogObject {
	var $numberOfRemovableRows = 0;
	var $numberOfNonRemovableRows = 0;
	var $numberOfPages = 0;
	
	var $nonRemovableDeadRows = 0;
	var $nonRemovableRowMinSize = 0;
	var $nonRemovableRowMaxSize = 0;
	var $unusedItemPointers = 0;
	var $totalFreeSpace = 0;
	var $numberOfPagesToEmpty = 0;
	var $numberOfPagesToEmptyAtTheEndOfTheTable = 0;
	var $numberOfPagesWithFreeSpace = 0;
	var $freeSpace = 0;
	
	var $numberOfRowVersionsMoved = 0;
	var $numberOfPagesRemoved = 0;
	
	var $cpuUsageTime = 0;
	var $duration = 0;
	
	var $indexesInformation = array();
	
	function VacuumTableLogObject($schema, $table, $ignored = false) {
		$this->VacuumLogObject($schema, $table, $ignored);
	}
	
	function getEventType() {
		return EVENT_VACUUM_TABLE;
	}
	
	function setNumberOfRemovableRows($numberOfRemovableRows) {
		$this->numberOfRemovableRows = $numberOfRemovableRows;
	}
	
	function setNumberOfNonRemovableRows($numberOfNonRemovableRows) {
		$this->numberOfNonRemovableRows = $numberOfNonRemovableRows;
	}
	
	function setNumberOfPages($numberOfPages) {
		$this->numberOfPages = $numberOfPages;
	}
	
	function setNumberOfRowVersionsMoved($numberOfRowVersionsMoved) {
		$this->numberOfRowVersionsMoved = $numberOfRowVersionsMoved;
	}
	
	function setNumberOfPagesRemoved($numberOfPagesRemoved) {
		$this->numberOfPagesRemoved = $numberOfPagesRemoved;
	}
	
	function setCpuUsageTime($cpuUsageTime) {
		$this->cpuUsageTime = $cpuUsageTime;
	}
	
	function setDuration($duration) {
		$this->duration = $duration;
	}
	
	function setDetailedInformation($nonRemovableDeadRows,
		$nonRemovableRowMinSize, $nonRemovableRowMaxSize,
		$unusedItemPointers,
		$totalFreeSpace,
		$numberOfPagesToEmpty, $numberOfPagesToEmptyAtTheEndOfTheTable,
		$numberOfPagesWithFreeSpace, $freeSpace) {
		
		$this->nonRemovableDeadRows = $nonRemovableDeadRows;
		$this->nonRemovableRowMinSize = $nonRemovableRowMinSize;
		$this->nonRemovableRowMaxSize = $nonRemovableRowMaxSize;
		$this->unusedItemPointers = $unusedItemPointers;
		$this->totalFreeSpace = $totalFreeSpace;
		$this->numberOfPagesToEmpty = $numberOfPagesToEmpty;
		$this->numberOfPagesToEmptyAtTheEndOfTheTable = $numberOfPagesToEmptyAtTheEndOfTheTable;
		$this->numberOfPagesWithFreeSpace = $numberOfPagesWithFreeSpace;
		$this->freeSpace = $freeSpace;
	}
	
	function getTablePath() {
		return $this->database.' - '.$this->schema.'.'.$this->table;
	}
	
	function getNumberOfPages() {
		return $this->numberOfPages;
	}
	
	function getNumberOfPagesRemoved() {
		return $this->numberOfPagesRemoved;
	}
	
	function getTotalNumberOfRows() {
		return $this->numberOfRemovableRows + $this->numberOfNonRemovableRows;
	}
	
	function getNumberOfRemovableRows() {
		return $this->numberOfRemovableRows;
	}
	
	function addIndexInformation(& $indexInformation) {
		$this->indexesInformation[] =& $indexInformation;
	}
	
	function & getLastIndexInformation() {
		return last($this->indexesInformation);
	}
	
	function & getIndexesInformation() {
		return $this->indexesInformation;
	}
}

?>