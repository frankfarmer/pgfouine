<?php

class PostgreSQLDurationLine extends PostgreSQLLogLine {
	var $ignore = false;
	
	function PostgreSQLDurationLine($timeString, $unit) {
		$this->PostgreSQLLogLine('NO TEXT', $this->parseDuration($timeString, $unit));
	}
	
	function appendTo(& $queries) {
		$query =& $queries->last();
		if($query) {
			$queries->gotDuration();
			$query->setDuration($this->duration);
			return $queries->pop();
		} else {
			stderr('Duration for no previous query');
			return false;
		}
	}
}

?>