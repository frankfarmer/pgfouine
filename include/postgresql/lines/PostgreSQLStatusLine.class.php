<?php

class PostgreSQLStatusLine extends PostgreSQLLogLine {
	var $ignore = true;
	
	function appendTo(& $stream) {
		$regexpConnectionReceived = new RegExp('/connection received: host=([^\s]+) port=([\d]+)/');
		$regexpConnectionAuthorized = new RegExp('/connection authorized: user=([^\s]+) database=([^\s]+)/');
		
		$connectionReceived =& $regexpConnectionReceived->match($this->text);
		if($connectionReceived) {
			$stream->setHostConnection($connectionReceived->getMatch(1), $connectionReceived->getMatch(2));
		}
		
		$connectionAuthorized =& $regexpConnectionAuthorized->match($this->text);
		if($connectionAuthorized) {
			$stream->setUserDb($connectionAuthorized->getMatch(1), $connectionAuthorized->getMatch(2));
		}
		
		return false;
	}
}

?>