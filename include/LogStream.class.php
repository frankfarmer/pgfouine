<?php

class LogStream {
	var $queries = array();
	var $hasDurationInfo = false;
	var $host = '';
	var $port = '';
	var $user = '';
	var $db = '';
	
	function & getQueries() {
		return $this->queries;
	}

	function append(& $line) {
		return $line->appendTo($this);
	}
	
	function push(& $query) {
		$query->setDb($this->db);
		$query->setUser($this->user);
		$this->queries[] =& $query;
	}

	function & pop() {
		return pop($this->queries);
	}

	function & last() {
		return last($this->queries);
	}

	function setHostConnection($host, $port) {
		$this->host = $host;
		$this->port = $port;
	}

	function setUserDb($user, $db) {
		$this->user = $user;
		$this->db = $db;
	}

	function gotDuration() {
		$this->hasDurationInfo = true;		
	}
	
	function getHost() {
		return $this->host;
	}
	
	function getPort() {
		return $this->port;
	}
	
	function getUser() {
		return $this->user;
	}
	
	function getDb() {
		return $this->db;
	}
	
	function hasDurationInfo() {
		return $this->hasDurationInfo;
	}
	
	function flush(& $accumulator) {
		while($query =& $this->pop()) {
			$query->accumulateTo($accumulator);
			unset($query);
		}
	}
}

?>