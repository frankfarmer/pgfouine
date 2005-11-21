<?php

class NormalizedQueriesListener extends QueryListener {
	var $queryList = array();
	var $queriesNumber = 10;
	
	function NormalizedQueriesListener() {
		$this->queriesNumber = getConfig('default_top_queries_number');
	}
	
	function fireEvent(& $query) {
		$normalizedText = $query->getNormalizedText();
		if(isset($this->queryList[$normalizedText])) {
			$this->queryList[$normalizedText]->addQuery($query);
		} else {
			$this->queryList[$normalizedText] = new NormalizedQuery($query);
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
	
	function & getSlowestQueries() {
		$queryList = $this->queryList;
		usort($queryList, array($this, 'compareSlowest'));
		return array_slice($queryList, 0, $this->queriesNumber);
	}
	
	function compareSlowest(& $a, & $b) {
		if($a->getAverageDuration() == $b->getAverageDuration()) {
			return 0;
		} elseif($a->getAverageDuration() < $b->getAverageDuration()) {
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