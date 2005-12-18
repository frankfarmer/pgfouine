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

class PostgreSQLLogLine {
	var $timestamp = false;
	var $connectionId = false;
	var $commandNumber = false;
	var $lineNumber = false;
	var $text;
	var $duration;
	var $ignore;
	
	function PostgreSQLLogLine($text = 'NO TEXT', $duration = false) {
		$this->text = rtrim($text);
		$this->duration = $duration;
		
		if(DEBUG > 1 && !$text) stderr('Empty text for line', true);
	}

	function toString() {
		return $this->text;
	}

	function parseDuration($timeString, $unit) {
		if($unit == 'ms') {
			$duration = (floatval($timeString) / 1000);
		} else {
			$duration = floatval($timeString);
		}
		return $duration;
	}
	
	function dump() {
		return get_class($this).' ('.$this->connectionId.'): '.$this->text;
	}
	
	function setContextInformation($timestamp, $connectionId, $commandNumber, $lineNumber) {
		$this->timestamp = $timestamp;
		$this->connectionId = $connectionId;
		$this->commandNumber = $commandNumber;
		$this->lineNumber = $lineNumber;
	}
	
	function getTimestamp() {
		return $this->timestamp;
	}
	
	function getConnectionId() {
		return $this->connectionId;
	}
	
	function getCommandNumber() {
		return $this->commandNumber;
	}
	
	function getLineNumber() {
		return $this->lineNumber;
	}
}

?>