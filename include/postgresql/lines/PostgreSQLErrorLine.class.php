<?php

class PostgreSQLErrorLine extends PostgreSQLLogLine {
	var $ignore = false;

	function appendTo(& $queries) {
		$closedQuery = $queries->pop();
		$errorQuery = new ErrorQuery($this->text);
		$errorQuery->setCommandNumber($this->commandNumber);
		$queries->push($errorQuery);
		return $closedQuery;
	}
}

?>