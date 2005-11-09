<?php

class ParseError {
	var $exception;
	var $line;
	
	function ParseError($exception, $line) {
		$this->exception = $exception;
		$this->line = $line;
	}
	
	function getLine() {
		return $this->line;
	}
	
	function getException() {
		return $this->exception;
	}
}

?>