<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2006 Open Wide
 * Copyright (c) 2006 Guillaume Smet
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

class PostgreSQLVacuumDetailLine extends PostgreSQLVacuumLogLine {

	function PostgreSQLVacuumDetailLine($text) {
		$this->PostgreSQLVacuumLogLine($text);
	}
	
	function appendTo(& $logObject) {
		global $postgreSQLVacuumRegexps;
		
		// TODO : CPU usage
		
		$detailMatch =& $postgreSQLVacuumRegexps['VacuumDetailLine']->match($this->text);
		
		if($detailMatch) {
			$nonRemovableDeadRows = $detailMatch->getMatch(1);
			$nonRemovableRowMinSize = $detailMatch->getMatch(2);
			$nonRemovableRowMaxSize = $detailMatch->getMatch(3);
			$unusedItemPointers = $detailMatch->getMatch(4);
			$totalFreeSpace = $detailMatch->getMatch(5);
			$numberOfPagesToEmpty = $detailMatch->getMatch(6);
			$numberOfPagesToEmptyAtTheEndOfTheTable = $detailMatch->getMatch(7);
			$numberOfPagesWithFreeSpace = $detailMatch->getMatch(8);
			$freeSpace = $detailMatch->getMatch(9);
			
			$logObject->setDetailedInformation($nonRemovableDeadRows,
				$nonRemovableRowMinSize, $nonRemovableRowMaxSize,
				$unusedItemPointers,
				$totalFreeSpace,
				$numberOfPagesToEmpty, $numberOfPagesToEmptyAtTheEndOfTheTable,
				$numberOfPagesWithFreeSpace, $freeSpace);
		}
	}
}

?>