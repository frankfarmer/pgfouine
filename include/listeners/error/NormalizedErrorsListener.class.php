<?php

class NormalizedErrorsListener extends ErrorListener {
	var $errorsList = array();
	var $errorsNumber = 10;
	
	function NormalizedErrorsListener() {
		$this->errorsNumber = getConfig('default_top_queries_number');
	}
	
	function fireEvent(& $error) {
		$normalizedText = $error->getNormalizedText();
		if(isset($this->errorsList[$normalizedText])) {
			$this->errorsList[$normalizedText]->addError($error);
		} else {
			$this->errorsList[$normalizedText] = new NormalizedError($error);
		}
	}
	
	function & getMostFrequentErrors() {
		$errorsList = $this->errorsList;
		usort($errorsList, array($this, 'compareMostFrequent'));
		return array_slice($errorsList, 0, $this->errorsNumber);
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
	
	function getUniqueErrorCount() {
		return count($this->errorsList);
	}
}

?>