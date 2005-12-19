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

require_once('lib/common.lib.php');
require_once('base.lib.php');

class GenericLogReader {
	var $fileName;
	var $lineParserName;
	var $accumulatorName;
	
	var $lineParsedCounter = 0;
	var $timeToParse;
	
	var $firstLineTimestamp;
	var $lastLineTimestamp;
	
	var $listeners = array();
	
	function GenericLogReader($fileName, $lineParserName, $accumulatorName) {
		$this->fileName = $fileName;
		$this->lineParserName = $lineParserName;
		$this->accumulatorName = $accumulatorName;
	}

	function parse() {
		global $lineParsedCounter;
		
		$startTimestamp = time();
		
		$accumulator = new $this->accumulatorName;
		$lineParser = new $this->lineParserName;
		
		foreach(array_keys($this->listeners) AS $listenerName) {
			$listener =& $this->listeners[$listenerName];
			if(is_a($listener, 'QueryListener')) {
				$accumulator->addQueryListener($listener);
			} else {
				$accumulator->addErrorListener($listener);
			}
		}
		
		if(DEBUG) {
			debug('Using parser: '.$this->lineParserName);
			debug('Using accumulator: '.$this->accumulatorName);
			debug('Using listeners: '.implode(', ', array_keys($this->listeners)));
		}
		
		$filePointer = @fopen($this->fileName, 'r');
		if(!$filePointer) {
			trigger_error('File '.$this->fileName.' is not readable.', E_USER_ERROR);
		}
		
		$lineParsedCounter = 0;
		
		if(DEBUG) debug(getMemoryUsage());
		if(PROFILE) {
			$GLOBALS['profiler'] = new Profiler();
			$GLOBALS['profiler']->start();
		}
		
		$currentTimestamp = time();
		while (!feof($filePointer)) {
			$lineParsedCounter ++;
			$text = fgets($filePointer);
			$line =& $lineParser->parse($text);
			if($line) {
				if(!isset($this->firstLineTimestamp)) {
					$this->firstLineTimestamp = $line->getTimestamp();
				}
				$this->lastLineTimestamp = $line->getTimestamp();
				$accumulator->append($line);
			}
			if($lineParsedCounter % 100000 == 0) {
				stderr('parsed '.$lineParsedCounter.' lines');
				if(DEBUG) {
					$currentTime = time() - $currentTimestamp;
					$currentTimestamp = time();
					debug('    '.getMemoryUsage());
					debug('    Time: '.$currentTime.' s');
				}
			}
		}
		DEBUG && debug('Before close - '.getMemoryUsage());
		$accumulator->close();
		DEBUG && debug('After close - '.getMemoryUsage());
		
		fclose($filePointer);
		
		$this->timeToParse = time() - $startTimestamp;
		$this->lineParsedCounter = $lineParsedCounter;
		
		DEBUG && debug("\nParsed ".$lineParsedCounter.' lines in '.$this->timeToParse.' s');
		
		if(PROFILE) {
			$GLOBALS['profiler']->end();
			$GLOBALS['profiler']->displayProfile();
		}
	}
	
	function getLineParsedCounter() {
		return $this->lineParsedCounter;
	}
	
	function addListener($listenerName) {
		$listener = new $listenerName();
		$this->listeners[$listenerName] =& $listener;
	}
	
	function & getListener($listenerName) {
		if(isset($this->listeners[$listenerName])) {
			return $this->listeners[$listenerName];
		} else {
			return false;
		}
	}
	
	function getFileName() {
		return $this->fileName;
	}
	
	function getTimeToParse() {
		return $this->timeToParse;
	}
	
	function getLineParsedCount() {
		return $this->lineParsedCounter;	
	}
	
	function getFirstLineTimestamp() {
		return $this->firstLineTimestamp;
	}
	
	function getLastLineTimestamp() {
		return $this->lastLineTimestamp;
	}
}

?>