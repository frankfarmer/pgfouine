<?php

class PostgreSQLErrorLine extends PostgreSQLLogLine {
	var $ignore = false;

	function appendTo(& $errors) {
		$closedQuery = $errors->pop();
		$errors->push(new ErrorQuery($this->text));
		return $closedQuery;
	}
}

?>