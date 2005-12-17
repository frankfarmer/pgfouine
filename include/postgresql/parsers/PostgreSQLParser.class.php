<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005 Guillaume Smet
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
					return new PostgreSQLQueryStartLine($queryMatch->getPostMatch());
				}
				$durationMatch =& $postgreSQLRegexps['DurationPart']->match($postMatch);
				if($durationMatch) {
					$additionalInformation = trim($durationMatch->getPostMatch());
					if($additionalInformation == '') {
						return new PostgreSQLDurationLine(trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
					} else {
						return new PostgreSQLQueryStartWithDurationLine($additionalInformation, trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
					}
				}
				$statusMatch =& $postgreSQLRegexps['StatusPart']->match($postMatch);
				if($statusMatch) {
					return new PostgreSQLStatusLine($postMatch);
				}
				
				if(
					strpos($postMatch, 'recycled transaction log file') !== 0 &&
					strpos($postMatch, 'removing transaction log file "') !== 0 &&
					strpos($postMatch, 'removing file "') !== 0 &&
					strpos($postMatch, 'could not receive data from client') !== 0 &&
					strpos($postMatch, 'checkpoints are occurring too frequently (') !== 0
					) {
					stderr('Unrecognized LOG or DEBUG line: '.$text, true);
				}
				return false;
			} elseif($keyword == 'WARNING' || $keyword == 'ERROR' || $keyword == 'FATAL' || $keyword == 'PANIC') {
				return new PostgreSQLErrorLine($postMatch);
			} elseif($keyword == 'CONTEXT') {
				return new PostgreSQLContextLine($postMatch);
			} elseif($keyword == 'STATEMENT') {
				return new PostgreSQLStatementLine($postMatch);
			} elseif($keyword == 'HINT') {
				return new PostgreSQLHintLine($postMatch);
			} elseif($keyword == 'DETAIL') {
				return new PostgreSQLDetailLine($postMatch);
			}
		}

		$continuationMatch =& $postgreSQLRegexps['ContinuationLine']->match($text);
		if($continuationMatch) {
			return new PostgreSQLContinuationLine($continuationMatch->getPostMatch());
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