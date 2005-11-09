<?php

class PostgreSQLAccumulator extends Accumulator {
	var $queries = array();
	var $errors = array();
	
	var $queryListeners = array();
	var $errorListeners = array();
	
	var $working = array();
	var $stream;
	var $hasDurationInfo = false;
	
	function PostgreSQLAccumulator() {
		$this->stream = new LogStream();
	}

	function append(& $line) {
		if($connectionId = $line->getConnectionId()) {
			if(!isset($this->working[$connectionId])) {
				$this->working[$connectionId] = new LogStream();
			}
			$query =& $this->working[$connectionId]->append($line);
		} else {
			$query =& $this->stream->append($line);
		}
		if($query && !$query->isIgnored()) {
			$query->accumulateTo($this);
		}
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
	/* TODO : on conserve ou on vire ?
	function closeOutAll() {
		$this->hasDurationInfo = $this->stream->hasDurationInfo();
		
		$queries =& $this->stream->getQueries();
		$queriesCount = count($this->stream->getQueries());
		
		@stream.queries.each { |q| q.accumulate_to(self) }
		@has_duration_info = @stream.has_duration_info
		@working.each {|k, stream|
			stream.queries.each { |q| q.accumulate_to(self) }
			@has_duration_info = @has_duration_info || stream.has_duration_info
		}
	}*/

}

?>