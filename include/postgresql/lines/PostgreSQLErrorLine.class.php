<?php

class PostgreSQLErrorLine extends PostgreSQLLogLine {
	var $ignore = false;

	function appendTo(& $errors) {
		$closedQuery = $errors->pop();
		$errorQuery = new ErrorQuery($this->text);
		$errorQuery->setCommandNumber($this->commandNumber);
		$errors->push($errorQuery);
		return $closedQuery;
	}
}

?>