<?php

class NormalizedQueriesListener extends QueryListener {
	var $queryList = array();
	var $queriesNumber = 10;
	
	function NormalizedQueriesListener($queriesNumber = DEFAULT_TOP_QUERIES_NUMBER) {
		$this->queriesNumber = $queriesNumber;
	}
	
	function fireEvent(& $query) {
		$normalizedText = $query->getNormalizedText();
		if(isset($this->queryList[$normalizedText])) {
			$this->queryList[$normalizedText]->addQuery($query);
		} else {
			$this->queryList[$normalizedText] = new NormalizedQuery($normalizedText);
			$this->queryList[$normalizedText]->addQuery($query);
		}
	}
	
	function & getQueriesMostTime() {
		$queryList = $this->queryList;
		usort($queryList, array($this, 'compareMostTime'));
		return array_slice($queryList, 0, $this->queriesNumber);
	}
	
	function compareMostTime(& $a, & $b) {
		if($a->getTotalDuration() == $b->getTotalDuration()) {
			return 0;
		} elseif($a->getTotalDuration() < $b->getTotalDuration()) {
			return 1;
		} else {
			return -1;
		}
	}
	
	function & getMostFrequentQueries() {
		$queryList = $this->queryList;
		usort($queryList, array($this, 'compareMostFrequent'));
		return array_slice($queryList, 0, $this->queriesNumber);
	}
	
	function compareMostFrequent(& $a, & $b) {
		if($a->getTimesExecuted() == $b->getTimesExecuted()) {
			return 0;
		} elseif($a->getTimesExecuted() < $b->getTimesExecuted()) {
			return 1;
		} else {
			return -1;
		}
	}
	
	function getUniqueQueryCount() {
		return count($this->queryList);
	}
}

?>