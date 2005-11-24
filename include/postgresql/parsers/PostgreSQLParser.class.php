<?php

class PostgreSQLParser {

	function & parse($text) {
		global $postgreSQLRegexps;

		if(PROFILE) $GLOBALS['profiler']->startStage('PostgreSQLParser::parse');
		
		$logOrDebugMatch =& $postgreSQLRegexps['LogOrDebugLine']->match($text);
		if($logOrDebugMatch) {
			$queryMatch =& $postgreSQLRegexps['QueryStartPart']->match($logOrDebugMatch->getPostMatch());
			if($queryMatch) {
				if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectQuery');
				return new PostgreSQLQueryStartLine($queryMatch->getPostMatch());
			}
			$durationMatch =& $postgreSQLRegexps['DurationPart']->match($logOrDebugMatch->getPostMatch());
			if($durationMatch) {
				$additionalInformation = trim($durationMatch->getPostMatch());
				if($additionalInformation == '') {
					if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectDuration');
					return new PostgreSQLDurationLine(trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
				} else {
					if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectQueryWithDuration');
					return new PostgreSQLQueryStartWithDurationLine($additionalInformation, trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
				}
			}
			$statusMatch =& $postgreSQLRegexps['StatusPart']->match($logOrDebugMatch->getPostMatch());
			if($statusMatch) {
				if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectStatus');
				return new PostgreSQLStatusLine($logOrDebugMatch->getPostMatch());
			}
			
			if(
				strpos($logOrDebugMatch->getPostMatch(), 'recycled transaction log file') !== 0 &&
				strpos($logOrDebugMatch->getPostMatch(), 'removing transaction log file "') !== 0 &&
				strpos($logOrDebugMatch->getPostMatch(), 'removing file "') !== 0 &&
				strpos($logOrDebugMatch->getPostMatch(), 'could not receive data from client') !== 0
				) {
				stderr('Unrecognized LOG or DEBUG line: '.$text, true);
			}
			if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectUnrecognizedLogLine');
			return false;
		}
		
		$errorMatch =& $postgreSQLRegexps['ErrorLine']->match($text);
		if($errorMatch) {
			if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectError');
			return new PostgreSQLErrorLine($errorMatch->getPostMatch());
		}

		$contextMatch =& $postgreSQLRegexps['ContextLine']->match($text);
		if($contextMatch) {
			if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectContext');
			return new PostgreSQLContextLine($contextMatch->getPostMatch());
		}

		$continuationMatch =& $postgreSQLRegexps['ContinuationLine']->match($text);
		if($continuationMatch) {
			if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectContinuation');
			return new PostgreSQLContinuationLine($continuationMatch->getPostMatch());
		}

		$statementMatch =& $postgreSQLRegexps['StatementLine']->match($text);
		if($statementMatch) {
			if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectStatement');
			return new PostgreSQLStatementLine($statementMatch->getPostMatch());
		}

		$hintMatch =& $postgreSQLRegexps['HintLine']->match($text);
		if($hintMatch) {
			if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectHint');
			return new PostgreSQLHintLine($hintMatch->getPostMatch());
		}

		$detailMatch =& $postgreSQLRegexps['DetailLine']->match($text);
		if($detailMatch) {
			if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectDetail');
			return new PostgreSQLDetailLine($detailMatch->getPostMatch());
		}

		if(trim($text) == '') {
			if(PROFILE) $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectEmptyContinuation');
			return new PostgreSQLContinuationLine('');
		}

		// PostgreSQL cuts lines if they are too long so an unrecognized log line can be in fact
		// a continuation line. So we add it as a continuation line and we let the ContinuationLine
		// object decide if it is one or not based on command number.
		if(PROFILE)  $GLOBALS['profiler']->endStage('PostgreSQLParser::parse', 'detectDefault');
		return new PostgreSQLContinuationLine($text);
	}
}

?>