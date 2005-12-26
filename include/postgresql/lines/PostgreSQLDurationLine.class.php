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

class PostgreSQLDurationLine extends PostgreSQLLogLine {
	var $ignore = false;
	
	function PostgreSQLDurationLine($timeString, $unit) {
		$this->PostgreSQLLogLine('', $this->parseDuration($timeString, $unit));
	}
	
	function & getLogObject(& $logStream) {
		if($this->lineNumber == 1) {
			$durationLogObject = new DurationLogObject($logStream->getUser(), $logStream->getDb(), $this->duration);
			$durationLogObject->setContextInformation($this->timestamp, $this->commandNumber);
			return $durationLogObject;
		} else {
			stderr('Duration for no previous query', true);
			return false;
		}
	}
	
	function appendTo(& $logObject) {
		$logObject->setDuration($this->duration);
	}
	
	function complete() {
		return true;
	}
}

?>