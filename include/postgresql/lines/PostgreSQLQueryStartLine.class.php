<?php

class PostgreSQLQueryStartLine extends PostgreSQLLogLine {
	function PostgreSQLQueryStartLine($text, $duration = false) {
		$this->PostgreSQLLogLine($this->filterQuery($text), $duration);
	}

	function filterQuery($text) {
		$loweredText = strtolower(trim($text));
		$this->ignore = (strpos($loweredText, 'begin') !== false) || (strpos($loweredText, 'vacuum') !== false) || ($loweredText == 'select 1');
		return $text;
	}
	
	function appendTo(& $queries) {
		$query = new Query($this->text, $this->ignore);
		$query->setCommandNumber($this->commandNumber);
		$queries->push($query);
		return false;
	}
}

?>