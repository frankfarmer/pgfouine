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
			$this->queryList[$normalizedText]['count'] ++;
			$this->queryList[$normalizedText]['duration'] += $query->getDuration();
			if($this->queryList[$normalizedText]['count'] > 1000) {
				$this->queryList[$normalizedText]['examples']->setSize(3);
			}
			if((intval(rand(1, 9)) % 5) == 0) {
				$this->queryList[$normalizedText]['examples']->addQuery($query);
			}
		} else {
			$this->queryList[$normalizedText] = array();
			$this->queryList[$normalizedText]['normalizedText'] = $normalizedText;
			$this->queryList[$normalizedText]['count'] = 1;
			$this->queryList[$normalizedText]['duration'] = $query->getDuration();
			$this->queryList[$normalizedText]['examples'] = new SlowestQueryList(1);
			$this->queryList[$normalizedText]['examples']->addQuery($query);
		}
	}
	
	function & getQueriesMostTime() {
		usort($this->queryList, array($this, 'compareMostTime'));
		return array_slice($this->queryList, 0, $this->queriesNumber);
	}
	
	function compareMostTime(& $a, & $b) {
		if($a['duration'] == $b['duration']) {
			return 0;
		} elseif($a['duration'] < $b['duration']) {
			return 1;
		} else {
			return -1;
		}
	}
}

?>