<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005-2006 Guillaume Smet
 *
 * pgFouine is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * pgFouine is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with pgFouine; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

class PostgreSQLParser {

	function & parse($text) {
		global $postgreSQLRegexps;
		
		$logLineMatch =& $postgreSQLRegexps['LogLine']->match($text);
		if($logLineMatch) {
			$keyword = $logLineMatch->getMatch(1);
			$postMatch = $logLineMatch->getPostMatch();
			
			if($keyword == 'LOG' || $keyword == 'DEBUG') {
				$queryMatch =& $postgreSQLRegexps['QueryStartPart']->match($postMatch);
				if($queryMatch) {
					$line = new PostgreSQLQueryStartLine($queryMatch->getPostMatch());
					return $line;
				}
				$durationMatch =& $postgreSQLRegexps['DurationPart']->match($postMatch);
				if($durationMatch) {
					$additionalInformation = trim($durationMatch->getPostMatch());
					if($additionalInformation == '') {
						$line = new PostgreSQLDurationLine(trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
					} else {
						$line = new PostgreSQLQueryStartWithDurationLine($additionalInformation, trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
					}
					return $line;
				}
				$statusMatch =& $postgreSQLRegexps['StatusPart']->match($postMatch);
				if($statusMatch) {
					$line = new PostgreSQLStatusLine($postMatch);
					return $line;
				}
				
				if(
					strpos($postMatch, 'disconnection: session time: ') !== 0 &&
					strpos($postMatch, 'autovacuum: processing database') !== 0 &&
					strpos($postMatch, 'recycled transaction log file') !== 0 &&
					strpos($postMatch, 'removing transaction log file "') !== 0 &&
					strpos($postMatch, 'removing file "') !== 0 &&
					strpos($postMatch, 'could not receive data from client') !== 0 &&
					strpos($postMatch, 'checkpoints are occurring too frequently (') !== 0
					) {
					stderr('Unrecognized LOG or DEBUG line: '.$text, true);
				}
				$line = false;
				return $line;
			} elseif($keyword == 'WARNING' || $keyword == 'ERROR' || $keyword == 'FATAL' || $keyword == 'PANIC') {
				$line = new PostgreSQLErrorLine($postMatch);
				return $line;
			} elseif($keyword == 'CONTEXT') {
				$line = new PostgreSQLContextLine($postMatch);
				return $line;
			} elseif($keyword == 'STATEMENT') {
				$line = new PostgreSQLStatementLine($postMatch);
				return $line;
			} elseif($keyword == 'HINT') {
				$line = new PostgreSQLHintLine($postMatch);
				return $line;
			} elseif($keyword == 'DETAIL') {
				$line = new PostgreSQLDetailLine($postMatch);
				return $line;
			}
		}
		
		// probably a continuation line. We let the PostgreSQLContinuationLine decide if it is one or not
		$line = new PostgreSQLContinuationLine($text);
		return $line;
	}
}

?>