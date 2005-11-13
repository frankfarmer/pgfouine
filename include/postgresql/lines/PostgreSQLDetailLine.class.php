<?php

class PostgreSQLDetailLine extends PostgreSQLLogLine {
	var $ignore = false;

	function appendTo(& $queries) {
		$error =& $queries->last();
		if($error) {
			$error->appendDetail($this->text);
		} else {
			stderr('Detail for no previous error');
		}
		return false;
	}
}

?>