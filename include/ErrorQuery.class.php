<?php

class ErrorQuery extends Query {
	var $hint = '';
	var $detail = '';
	var $error = '';
	
	function ErrorQuery($text = 'No error message') {
		$this->error = $text;
		$this->Query($text);
	}
	
	function appendStatement($text) {
		if(DEBUG > 1 && empty($text)) stderr('Empty text for error statement', true);
		// the text may have been appended so we copy it in error before overwriting it
		$this->error = $this->text;
		
		$this->text = $text;
	}
	
	function appendHint($hint) {
		if(DEBUG > 1 && empty($hint)) stderr('Empty text for error hint', true);
		$this->hint = $hint;
	}
	
	function appendDetail($detail) {
		if(DEBUG > 1 && empty($detail)) stderr('Empty text for error detail', true);
		$this->detail = $detail;
	}
	
	function appendContext($context) {
		if(DEBUG > 1 && empty($context)) stderr('Empty text for error context', true);
		$this->context = $context;
	}
	
	function accumulateTo(& $accumulator) {
		$this->text = normalizeWhitespaces($this->text);
		$accumulator->appendError($this);
	}
	
	function getError() {
		return $this->error;
	}
	
	function getHint() {
		return $this->hint;
	}
	
	function getDetail() {
		return $this->detail;
	}
}

?>