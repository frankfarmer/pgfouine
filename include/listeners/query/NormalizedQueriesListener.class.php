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
		usort($this->queryList, array($this, 'compareMostTime'));
		return array_slice($this->queryList, 0, $this->queriesNumber);
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
}

?>