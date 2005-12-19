<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005 Guillaume Smet
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

class GlobalCountersListener extends QueryListener {
	var $queryCount = 0;
	var $queryDuration = 0;
	var $selectCount = 0;
	var $updateCount = 0;
	var $insertCount = 0;
	var $deleteCount = 0;
	var $firstQueryTimestamp = MAX_TIMESTAMP;
	var $lastQueryTimestamp = MIN_TIMESTAMP;
	
	function fireEvent(& $query) {
		$this->queryCount++;
		$this->queryDuration += $query->getDuration();
		
		$this->firstQueryTimestamp = min($query->getTimestamp(), $this->firstQueryTimestamp);
		$this->lastQueryTimestamp = max($query->getTimestamp(), $this->lastQueryTimestamp);
		
		if($query->isSelect()) {
			$this->selectCount ++;
		} elseif($query->isUpdate()) {
			$this->updateCount ++;
		} elseif($query->isInsert()) {
			$this->insertCount ++;
		} elseif($query->isDelete()) {
			$this->deleteCount ++;
		}
	}
	
	function getQueryCount() {
		return $this->queryCount;
	}
	
	function getQueryDuration() {
		return $this->queryDuration;
	}
	
	function getSelectCount() {
		return $this->selectCount;
	}
	
	function getUpdateCount() {
		return $this->updateCount;
	}
	
	function getInsertCount() {
		return $this->insertCount;
	}
	
	function getDeleteCount() {
		return $this->deleteCount;
	}
	
	function getFirstQueryTimestamp() {
		return $this->firstQueryTimestamp;
	}
	
	function getLastQueryTimestamp() {
		return $this->lastQueryTimestamp;
	}
}

?>