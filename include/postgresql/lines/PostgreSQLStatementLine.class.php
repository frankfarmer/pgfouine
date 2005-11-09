<?php

class PostgreSQLStatementLine extends PostgreSQLQueryStartLine {
	var $ignore = false;

	function appendTo(& $errors) {
		$error =& $errors->last();
		if($error) {
			$error->appendStatement($this->text);
		} else {
			stderr('Statement for no previous error');
		}
		return false;
	}
}

?>