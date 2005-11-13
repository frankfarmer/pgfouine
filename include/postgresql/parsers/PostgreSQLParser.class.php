<?php

class PostgreSQLParser {

	function & parse($text) {
		$regexpLogOrDebugLine = new RegExp("/^(LOG|DEBUG):[\s]*/");
		$regexpContinuationLine = new RegExp("/^(\^I|\s|\t)/");
		$regexpContextLine = new RegExp("/^CONTEXT:[\s]*/");
		$regexpErrorLine = new RegExp("/^(WARNING|ERROR|FATAL|PANIC):[\s]*/");
		$regexpHintLine = new RegExp("/^HINT:[\s]*/");
		$regexpDetailLine = new RegExp("/^DETAIL:[\s]*/");
		$regexpStatementLine = new RegExp("/^STATEMENT:[\s]*/");
		
		$regexpQueryStart = new RegExp("/^(query|statement):[\s]*/");
		$regexpStatus = new RegExp("/^(connection|received|unexpected EOF)/");
		$regexpDuration = new RegExp("/^duration:([\s\d\.]*)(sec|ms)/"); 
		
		$logOrDebugMatch =& $regexpLogOrDebugLine->match($text);
		if($logOrDebugMatch) {
			$queryMatch =& $regexpQueryStart->match($logOrDebugMatch->getPostMatch());
			if($queryMatch) {
				return new PostgreSQLQueryStartLine($queryMatch->getPostMatch());
			}
			$durationMatch =& $regexpDuration->match($logOrDebugMatch->getPostMatch());
			if($durationMatch) {
				$additionalInformation = trim($durationMatch->getPostMatch());
				if($additionalInformation == '') {
					return new PostgreSQLDurationLine(trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
				} else {
					return new PostgreSQLQueryStartWithDurationLine($additionalInformation, trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
				}
			}
			$statusMatch =& $regexpStatus->match($logOrDebugMatch->getPostMatch());
			if($statusMatch) {
				return new PostgreSQLStatusLine($logOrDebugMatch->getPostMatch());
			}
			
			stderr('Unrecognized LOG or DEBUG line: '.$text);
			return false;
		}
		
		$errorMatch =& $regexpErrorLine->match($text);
		if($errorMatch) {
			return new PostgreSQLErrorLine($errorMatch->getPostMatch());
		}

		$contextMatch =& $regexpContextLine->match($text);
		if($contextMatch) {
			return new PostgreSQLContextLine($contextMatch->getPostMatch());
		}

		$continuationMatch =& $regexpContinuationLine->match($text);
		if($continuationMatch) {
			return new PostgreSQLContinuationLine($continuationMatch->getPostMatch());
		}

		$statementMatch =& $regexpStatementLine->match($text);
		if($statementMatch) {
			return new PostgreSQLStatementLine($statementMatch->getPostMatch());
		}

		$hintMatch =& $regexpHintLine->match($text);
		if($hintMatch) {
			return new PostgreSQLHintLine($hintMatch->getPostMatch());
		}

		$detailMatch =& $regexpDetailLine->match($text);
		if($detailMatch) {
			return new PostgreSQLDetailLine($detailMatch->getPostMatch());
		}

		if(trim($text) == '') {
			return new PostgreSQLContinuationLine('');
		}

		// PostgreSQL cuts lines if they are too long so an unrecognized log line can be in fact
		// a continuation line. So we add it as a continuation line and we let the ContinuationLine
		// object decide if it is one or not based on command number.
		return new PostgreSQLContinuationLine($text);
	}
}

?>