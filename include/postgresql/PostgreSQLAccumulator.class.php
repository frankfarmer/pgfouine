<?php

class PostgreSQLAccumulator extends Accumulator {
	var $working = array();
	var $stream;
	
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
	
	function flushLogStreams() {
		// flush default stream
		$this->stream->flush($this);
		$hasDurationInfo = $this->stream->hasDurationInfo();
		
		// flush streams with connection id
		$logStreamsKeys = array_keys($this->working);
		foreach($logStreamsKeys AS $key) {
			$logStream =& $this->working[$key];
			$logStream->flush($this);
			$hasDurationInfo = $hasDurationInfo || $logStream->hasDurationInfo();
			unset($logStream);
		}
		
		$this->hasDurationInfo = $hasDurationInfo;
	}
}

?>