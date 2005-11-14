<?php

function debug($string, $displayLineNumber = false) {
	stderr($string, $displayLineNumber);
}

function stderr($string, $displayLineNumber = true) {
	global $stderr, $lineParsedCounter;
	if($displayLineNumber && $lineParsedCounter) {
		$string .= ' - log line '.$lineParsedCounter;
	}
	if($stderr) {
		fwrite($stderr, $string."\n");
	}
}

function getMemoryUsage() {
	$memoryUsage = memory_get_usage();
	$output = 'Memory usage: ';
	if($memoryUsage < 1024) {
		$output .= intval($memoryUsage).' o';
	} elseif($memoryUsage < 1024*1024) {
		$output .= intval($memoryUsage/1024).' ko';
	} else {
		$output .= number_format(($memoryUsage/(1024*1024)), 2, '.', ' ').' mo';
	}
	return $output;
}

function normalizeWhitespaces($text) {
	$text = trim($text);
	$text = preg_replace('/\s+/', ' ', $text);
	return $text;
}

function &last(& $array) {
	if(empty($array)) {
		return false;
	}
	end($array);
	return $array[key($array)];
}

function &pop(& $array) {
	if(empty($array)) {
		return false;
	}
	$object =& last($array);
	array_pop($array);
	return $object;
}

class RegExp {
	var $pattern;
	
	function RegExp($pattern) {
		$this->pattern = $pattern;
	}
	
	function & match($text) {
		$found = preg_match($this->pattern, $text, $matches, PREG_OFFSET_CAPTURE);
		if($found) {
			return new RegExpMatch($text, $matches);
		}
		return false;
	}
	
	function getPattern() {
		return $this->pattern;
	}
}

class RegExpMatch {
	var $text;
	var $matches = array();
	
	function RegExpMatch($text, & $matches) {
		$this->text = $text;
		$this->matches =& $matches;
	}
	
	function getMatch($position) {
		if(isset($this->matches[$position])) {
			return $this->matches[$position][0];
		} else {
			return false;
		}
	}
	
	function getPostMatch() {
		$postMatch = substr($this->text, $this->matches[0][1] + strlen($this->matches[0][0]));
		return $postMatch;
	}
}

?>