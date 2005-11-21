<?php

class NormalizedError {
	var $normalizedText;
	var $error = '';
	var $hint = '';
	var $detail = '';
	var $examples = array();
	var $count = 0;
	
	function NormalizedError(& $error) {
		$this->normalizedText = $error->getNormalizedText();
		$this->error = $error->getError();
		
		$this->addError($error);
	}
	
	function addError(& $error) {
		$this->count ++;
		if(count($this->examples) < 3) {
			$this->examples[] =& $error;
		}
	}
	
	function getNormalizedText() {
		return $this->normalizedText;
	}
	
	function getError() {
		return $this->error;
	}
	
	function getTimesExecuted() {
		return $this->count;
	}
	
	function & getFilteredExamplesArray() {
		$returnExamples = false;
		
		$exampleCount = count($this->examples);
		for($i = 0; $i < $exampleCount; $i++) {
			$example =& $this->examples[$i];
			if($example->getText() != $this->getNormalizedText()) {
				return $this->examples;
			}
			unset($example);
		}
		return array();
	}
}

?>