<?php

class PostgreSQLLogLine {
	var $connectionId = false;
	var $commandNumber = false;
	var $lineNumber = false;
	var $text;
	var $duration;
	var $ignore;
	
	function PostgreSQLLogLine($text = 'NO TEXT', $duration = false) {
		$this->text = rtrim($text);
		$this->duration = $duration;
		
		if(DEBUG > 1 && !$text) stderr('Empty text for line', true);
	}

	function toString() {
		return $this->text;
	}

	function parseDuration($timeString, $unit) {
		if($unit == 'ms') {
			$duration = (floatval($timeString) / 1000);
		} else {
			$duration = floatval($timeString);
		}
		return $duration;
	}
	
	function dump() {
		return get_class($this).' ('.$this->connectionId.'): '.$this->text;
	}
	
	function setContextInformation($connectionId, $commandNumber, $lineNumber) {
		$this->connectionId = $connectionId;
		$this->commandNumber = $commandNumber;
		$this->lineNumber = $lineNumber;
	}
	
	function getConnectionId() {
		return $this->connectionId;
	}
	
	function getCommandNumber() {
		return $this->commandNumber;
	}
	
	function getLineNumber() {
		return $this->lineNumber;
	}
}

?>