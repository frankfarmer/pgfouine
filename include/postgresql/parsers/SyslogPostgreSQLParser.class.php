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