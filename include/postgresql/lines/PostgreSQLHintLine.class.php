<?php

class PostgreSQLHintLine extends PostgreSQLLogLine {
	var $ignore = false;

	function appendTo(& $queries) {
		$error =& $queries->last();
		if($error) {
			$error->appendHint($this->text);
		} else {
			stderr('Hint for no previous error', true);
		}
		return false;
	}
}

?>