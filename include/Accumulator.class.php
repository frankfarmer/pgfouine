<?php

class Accumulator {
	function append(& $line) {
	}

	function appendQuery(& $query) {
	}
	
	function appendError(& $error) {
	}

	function closeOutAll() {
	}
	
	function getQueries() {
		return array();
	}
	
	function getErrors() {
		return array();
	}
	
	function hasDurationInfo() {
		return false;
	}
}

?>