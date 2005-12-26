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

class LogStream {
	var $currentBlock = false;
	var $logObjects = array();
	var $host = '';
	var $port = '';
	var $user = '';
	var $db = '';
	
	function & getLogObjects() {
		return $this->logObjects;
	}

	function append(& $line) {
		$logObject = false;
		$lineCommandNumber = $line->getCommandNumber();
		
		if(!$this->currentBlock || (($lineCommandNumber != $this->currentBlock->getCommandNumber()) && $this->currentBlock->isComplete())) {
			if($this->currentBlock) {
				$logObject =& $this->currentBlock->close();
			}
			if($line->getLineNumber() == 1) {
				$this->currentBlock = new LogBlock($this, $lineCommandNumber, $line);
			} else {
				stderr('we just close a LogBlock, line number should be 1 and is not', true);
				stderr('line command number: '.$lineCommandNumber);
				stderr('block command number: '.$this->currentBlock->getCommandNumber());
				$this->currentBlock = false;
			}
		} else {
			if(is_a($line, 'PostgreSQLContinuationLine') && $line->getText()) {
				$lastLine =& last($this->currentBlock->getLines());
				$lastLine->appendText($line->getText());
			} else {
				$this->currentBlock->addLine($line);
			}
		}
		return $logObject;
	}

	function setHostConnection($host, $port) {
		$this->host = $host;
		$this->port = $port;
	}

	function setUserDb($user, $db) {
		$this->user = $user;
		$this->db = $db;
	}
	
	function getHost() {
		return $this->host;
	}
	
	function getPort() {
		return $this->port;
	}
	
	function getUser() {
		return $this->user;
	}
	
	function getDb() {
		return $this->db;
	}
	
	function flush(& $accumulator) {
		if($this->currentBlock && $this->currentBlock->isComplete()) {
			$logObject =& $this->currentBlock->close();
			if($logObject) {
				$logObject->accumulateTo($accumulator);
			}
		}
	}
}

?>