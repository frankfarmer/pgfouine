<?php

class PostgreSQLHintLine extends PostgreSQLLogLine {
	var $ignore = false;

	function appendTo(& $errors) {
		$error =& $errors->last();
		if($error) {
			$error->appendHint($this->text);
		} else {
			stderr('Hint for no previous error');
		}
		return false;
	}
}

?>