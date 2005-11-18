<?php

class PostgreSQLContinuationLine extends PostgreSQLLogLine {
	var $ignore = false;

	function PostgreSQLContinuationLine($text, $duration = false) {
		$this->PostgreSQLLogLine(str_replace('^I', "\t", $text));
	}

	function appendTo(& $queries) {
		$query =& $queries->last();
		if($query && ($query->getCommandNumber() == $this->commandNumber)) {
			if(substr(trim($this->text), 0, 2) != '--') {
				$query->append($this->text);
			}
		} else {
			stderr('Continuation for no previous query', true);
		}
		return false;
	}
}

?>