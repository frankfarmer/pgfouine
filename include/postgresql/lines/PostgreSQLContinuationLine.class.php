<?php

class PostgreSQLContinuationLine extends PostgreSQLLogLine {
	var $ignore = false;

	function PostgreSQLContinuationLine($text, $duration = false) {
		$this->PostgreSQLLogLine(str_replace('^I', "\t", $text));
	}

	function appendTo(& $queries) {
		$query =& $queries->last();
		if($query) {
			$query->append($this->text);
		} else {
			stderr('Continuation for no previous query');
		}
		return false;
	}
}

?>