<?php

class Accumulator {
	var $queryListeners = array();
	var $errorListeners = array();
	
	function append(& $line) {
	}
	
	function addQueryListener(& $queryListener) {
		$this->queryListeners[] =& $queryListener;
	}
	
	function addErrorListener(& $errorListener) {
		$this->errorListeners[] =& $errorListener;
	}

	function appendQuery(& $query) {
		$this->fireQueryEvent($query);
	}
	
	function appendError(& $error) {
		$this->fireErrorEvent($error);
	}
	
	function fireQueryEvent(& $query) {
		$countListeners = count($this->queryListeners);
		for($i = 0; $i < $countListeners; $i++) {
			$listener =& $this->queryListeners[$i];
			$listener->fireEvent($query);
		}
	}
	
	function fireErrorEvent(& $error) {
		$countListeners = count($this->errorListeners);
		for($i = 0; $i < $countListeners; $i++) {
			$listener =& $this->errorListeners[$i];
			$listener->fireEvent($error);
		}
	}
	
	function close() {
		$this->flushLogStreams();
		$this->closeListeners();
	}
	
	function flushLogStreams() {
	}
	
	function closeListeners() {
		$listenerCount = count($this->queryListeners);
		for($i = 0; $i < $listenerCount; $i++) {
			$listener =& $this->queryListeners[$i];
			$listener->close();
		}
		$listenerCount = count($this->errorListeners);
		for($i = 0; $i < $listenerCount; $i++) {
			$listener =& $this->errorListeners[$i];
			$listener->close();
		}
	}
}

?>