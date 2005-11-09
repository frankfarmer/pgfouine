<?php

class PostgreSQLQueryStartLine extends PostgreSQLLogLine {
	function PostgreSQLQueryStartLine($text, $duration = false) {
		$this->PostgreSQLLogLine($this->filterQuery($text), $duration);
	}

	function filterQuery($text) {
		$loweredText = strtolower(trim($text));
		// TODO : vérifier la pertinence des tests
		$this->ignore = (strpos($loweredText, 'begin') !== false) || (strpos($loweredText, 'vacuum') !== false) || ($loweredText == 'select 1');
		return $text;
	}
	
	function appendTo(& $queries) {
		$queries->push(new Query($this->text, $this->ignore));
		return false;
	}
}

?>