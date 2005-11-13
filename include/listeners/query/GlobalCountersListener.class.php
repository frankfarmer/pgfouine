<?php

class GlobalCountersListener extends QueryListener {
	var $queryCount = 0;
	var $queryDuration = 0;
	var $selectCount = 0;
	var $updateCount = 0;
	var $insertCount = 0;
	var $deleteCount = 0;
	
	function fireEvent(& $query) {
		$this->queryCount++;
		$this->queryDuration += $query->getDuration();
		
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
}

?>