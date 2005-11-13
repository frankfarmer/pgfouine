<?php

class SlowestQueryList {
	var $size;
	var $queries = array();
	var $queriesCount = 0;
	var $shortestDuration = 100000000;
	
	function SlowestQueryList($size) {
		$this->size = $size;
	}
	
	function addQuery(&$query) {
		$duration = (string) $query->getDuration();
		$queriesCount = $this->queriesCount;
		$shortestDuration = (string) $this->shortestDuration;
		
		if($queriesCount < $this->size) {
			if(!array_key_exists($duration, $this->queries)) {
				$this->queries[$duration] = array();
			}
			$this->queries[$duration][] =& $query;
			$this->shortestDuration = min($shortestDuration, $duration);
			$this->queriesCount++;
		} else {
			if($shortestDuration < $duration) {
				$shortestDurationQueriesCount = count($this->queries[$shortestDuration]);
				if($shortestDurationQueriesCount == 1) {
					unset($this->queries[$shortestDuration]);
					$shortestDuration = min(array_keys($this->queries));
				} else {
					unset($this->queries[$shortestDuration][$shortestDurationQueriesCount - 1]);
				}
				if(!array_key_exists($duration, $this->queries)) {
					$this->queries[$duration] = array();
				}
				$this->queries[$duration][] =& $query;
				$this->shortestDuration = min($shortestDuration, $duration);
			}
		}
	}
	
	function & getQueries() {
		return $this->queries;
	}
	
	function & getSortedQueries() {
		$queryList = array();
		krsort($this->queries, SORT_NUMERIC);
		$keys = array_keys($this->queries);
		foreach($keys AS $key) {
			$queryArrayCount = count($this->queries[$key]);
			for($i = 0; $i < $queryArrayCount; $i++) {
				$queryList[] =& $this->queries[$key][$i];
			}
		}
		return $queryList;
	}
}

?>