<?php

require_once('lib/common.lib.php');
require_once('base.lib.php');

class GenericLogReader {
	var $fileName;
	var $lineParserName;
	var $accumulatorName;
	
	var $lineParsedCounter = 0;
	var $timeToParse;
	
	var $listeners = array();
	
	var $includesDuration = false;
	
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
		
		$currentTimestamp = time();
		while (!feof($filePointer)) {
			$lineParsedCounter ++;
			$text = fgets($filePointer);
			$line =& $lineParser->parse($text);
			if($line) {
				$accumulator->append($line);
			} else {
				// TODO
				# text.gsub!(/\n/, '\n').gsub!(/\t/, '\t')
				# $stderr.puts "Unrecognized text: '#{text}'"
			}
			// TODO
			//rescue StandardError => e
			//@parse_errors << ParseError.new(e,line)
			if(DEBUG && $lineParsedCounter % 100000 == 0) {
				$currentTime = time() - $currentTimestamp;
				$currentTimestamp = time();
				debug('parsed '.$lineParsedCounter.' lines');
				debug('    '.getMemoryUsage());
				debug('    Time: '.$currentTime.' s');
			}
		}
		DEBUG && debug('Before close - '.getMemoryUsage());
		$accumulator->close();
		DEBUG && debug('After close - '.getMemoryUsage());
		
		fclose($filePointer);
		
		$this->timeToParse = time() - $startTimestamp;
		$this->lineParsedCounter = $lineParsedCounter;
		
		DEBUG && debug("\nParsed ".$lineParsedCounter.' lines in '.$this->timeToParse.' s');
		
		$this->includesDuration = $accumulator->hasDurationInfo();
	}
	
	function getLineParsedCounter() {
		return $this->lineParsedCounter;
	}
	
	function addListener($listenerName) {
		$listener = new $listenerName();
		$this->listeners[$listenerName] =& $listener;
	}
	
	function & getListener($listenerName) {
		return $this->listeners[$listenerName];
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
}

?>