<?php

class PostgreSQLStatementLine extends PostgreSQLQueryStartLine {
	var $ignore = false;

	function appendTo(& $queries) {
		$error =& $queries->last();
		if($error) {
			$error->appendStatement($this->text);
		} else {
			stderr('Statement for no previous error');
		}
		return false;
	}
}

?>