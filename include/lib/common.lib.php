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

define('MIN_TIMESTAMP', 0);
define('MAX_TIMESTAMP', 9999999999);

function setConfig($key, $value) {
	if(!isset($GLOBALS['config'])) {
		$GLOBALS['config'] = array();
	}
	$GLOBALS['config'][$key] = $value;
}

function getConfig($key) {
	if(isset($GLOBALS['config'][$key])) {
		return $GLOBALS['config'][$key];
	} else {
		return false;
	}
}

function debug($string, $displayLineNumber = false) {
	stderr($string, $displayLineNumber);
}

function stderr($string, $displayLineNumber = false) {
	global $stderr, $lineParsedCounter;
	if($displayLineNumber && $lineParsedCounter) {
		$string .= ' - log line '.$lineParsedCounter;
	}
	if($stderr) {
		fwrite($stderr, $string."\n");
	}
}

function stderrArray($array) {
	ob_start();
	print_r($array);
	$content = ob_get_contents();
	ob_end_clean();

	stderr($content);
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
	
	function & getMatches() {
		return $this->matches;
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