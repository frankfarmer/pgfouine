<?php

class SyslogPostgreSQLParser extends PostgreSQLParser {
	var $regexpPostgresPid;
	
	function SyslogPostgreSQLParser($syslogString = 'postgres') {
		$this->regexpSyslogContext = new RegExp('/ '.$syslogString.'\[(\d{1,5})\]: \[(\d{1,10})(\-\d{1,5}){0,1}\] /');
	}

	function & parse($data) {
		if(PROFILE) $GLOBALS['profiler']->startStage('SyslogPostgreSQLParser::parse');
		
		$syslogContextMatch = $this->regexpSyslogContext->match($data);
		if($syslogContextMatch === false) {
			if(PROFILE) $GLOBALS['profiler']->endStage('SyslogPostgreSQLParser::parse');
			return false;
		}
		
		$connectionId = $syslogContextMatch->getMatch(1);
		$commandNumber = $syslogContextMatch->getMatch(2);
		$lineId = $syslogContextMatch->getMatch(3);
		$text = $syslogContextMatch->getPostMatch();
		
		if(!$connectionId || !$commandNumber || !$text) {
			if(PROFILE) $GLOBALS['profiler']->endStage('SyslogPostgreSQLParser::parse');
			return false;
		}
		
		if($lineId) {
			$lineNumber = substr($lineId, 1);
		} else {
			$lineNumber = 1;
		}
		
		$line =& parent::parse($text);
		
		if(!$line) {
			if(PROFILE) $GLOBALS['profiler']->endStage('SyslogPostgreSQLParser::parse');
			return false;
		}
		
		$line->setConnectionId($connectionId);
		$line->setCommandNumber($commandNumber);
		$line->setLineNumber($lineNumber);
		
		if(PROFILE) $GLOBALS['profiler']->endStage('SyslogPostgreSQLParser::parse');
		return $line;
	}
}

?>