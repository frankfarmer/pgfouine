<?php

class NormalizedQuery {
	var $normalizedText;
	var $duration = 0;
	var $count = 0;
	var $examples = false;
	
	function NormalizedQuery(& $query) {
		$this->normalizedText = $query->getNormalizedText();
		$maxExamples = getConfig('max_number_of_examples');
		if($maxExamples) {
			$this->examples = new SlowestQueryList($maxExamples);
		}
		
		$this->addQuery($query);
	}
	
	function addQuery(& $query) {
		$this->count ++;
		$this->duration += $query->getDuration();
		if($this->examples) {
			if($this->count == 1) {
				$this->examples->addQuery($query);
			} else {
				if(intval(rand(1, 100)) == 50) {
					$this->examples->addQuery($query);
				}
			}
		}
	}
	
	function getNormalizedText() {
		return $this->normalizedText;
	}
	
	function getTotalDuration() {
		return $this->duration;
	}
	
	function getTimesExecuted() {
		return $this->count;
	}
	
	function getAverageDuration() {
		$average = 0;
		if($this->count > 0) {
			$average = ($this->duration/$this->count);
		}
		return $average;
	}
	
	function & getFilteredExamplesArray() {
		$returnExamples = false;
		
		$examples =& $this->examples->getSortedQueries();
		$exampleCount = count($examples);
		for($i = 0; $i < $exampleCount; $i++) {
			$example =& $examples[$i];
			if($example->getText() != $this->getNormalizedText()) {
				return $examples;
			}
			unset($example);
		}
		return array();
	}
}

?>