<?php

class SyslogPostgreSQLParser extends PostgreSQLParser {
	var $regexpPostgresPid;
	
	function SyslogPostgreSQLParser($syslogString = 'postgres') {
		$this->regexpSyslogContext = new RegExp('/ '.$syslogString.'\[(\d{1,5})\]: \[(\d{1,10})(?:\-(\d{1,5}))?\] /');
	}

	function & parse($data) {
		$syslogContextMatch =& $this->regexpSyslogContext->match($data);
		if($syslogContextMatch === false) {
			return false;
		}
		
		$matches = $syslogContextMatch->getMatches();
		$text = $syslogContextMatch->getPostMatch();
		
		if(count($matches) < 3 || !$text) {
			return false;
		}
		
		$connectionId = $matches[1][0];
		$commandNumber = $matches[2][0];
		
		if(isset($matches[3][0])) {
			$lineNumber = $matches[3][0];
		} else {
			$lineNumber = 1;
		}
		
		$line =& parent::parse($text);
		
		if(!$line) {
			return false;
		}
		
		$line->setContextInformation($connectionId, $commandNumber, $lineNumber);
		
		return $line;
	}
}

?>