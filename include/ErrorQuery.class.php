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
		if(DEBUG && empty($text)) stderr('Empty text for error statement');
		$this->text = $text;
	}
	
	function appendHint($hint) {
		if(DEBUG && empty($hint)) stderr('Empty text for error hint');
		$this->hint = $hint;
	}
	
	function appendDetail($detail) {
		if(DEBUG && empty($detail)) stderr('Empty text for error detail');
		$this->detail = $detail;
	}
	
	function accumulateTo(& $accumulator) {
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