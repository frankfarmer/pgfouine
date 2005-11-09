<?php

class SyslogPostgreSQLParser extends PostgreSQLParser {
	var $regexpPostgresPid;
	
	function SyslogPostgreSQLParser($syslogString = 'postgres') {
		$this->regexpPostgresPid = new RegExp('/ '.$syslogString.'\[(\d{1,5})\]: /');
	}

	function & parse($data) {
		$pidMatch = $this->regexpPostgresPid->match($data);
		if($pidMatch === false) {
			return false;
		}
		
		$connectionId = $pidMatch->getMatch(1);
		$text = $pidMatch->getPostMatch();
		if(!$text) return false;
		
		$regexpCommandLine =  new RegExp('/\[(\d{1,10})(\-\d{1,5}){0,1}\] /');
		
		$lineIdMatch = $regexpCommandLine->match($text);
		if(!$lineIdMatch) return false;
		
		$text = $lineIdMatch->getPostMatch();
		$commandNumber = $lineIdMatch->getMatch(1);
		if($lineIdMatch->getMatch(2)) {
			$lineNumber = substr($lineIdMatch->getMatch(2), 1);
		} else {
			$lineNumber = 1;
		}
		
		$line =& parent::parse($text);
		if(!$line) return false;
		
		$line->setConnectionId($connectionId);
		$line->setCommandNumber($commandNumber);
		$line->setLineNumber($lineNumber);
		
		return $line;
	}
}

?>