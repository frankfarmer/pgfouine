<?php

class PostgreSQLStatusLine extends PostgreSQLLogLine {
	var $ignore = true;
	
	function appendTo(& $stream) {
		global $postgreSQLRegexps;
		
		$connectionReceived =& $postgreSQLRegexps['ConnectionReceived']->match($this->text);
		if($connectionReceived) {
			$stream->setHostConnection($connectionReceived->getMatch(1), $connectionReceived->getMatch(2));
		}
		
		$connectionAuthorized =& $postgreSQLRegexps['ConnectionAuthorized']->match($this->text);
		if($connectionAuthorized) {
			$stream->setUserDb($connectionAuthorized->getMatch(1), $connectionAuthorized->getMatch(2));
		}
		
		return false;
	}
}

?>