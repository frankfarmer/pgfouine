<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005-2006 Guillaume Smet
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

class NormalizedError {
	var $normalizedText;
	var $error = '';
	var $hint = '';
	var $detail = '';
	var $examples = array();
	var $count = 0;
	
	function NormalizedError(& $error) {
		$this->normalizedText = $error->getNormalizedText();
		$this->error = $error->getError();
		$this->hint = $error->getHint();
		$this->detail = $error->getDetail();
		
		$this->addError($error);
	}
	
	function addError(& $error) {
		$this->count ++;
		if(count($this->examples) < 3) {
			$this->examples[] =& $error;
		}
	}
	
	function getNormalizedText() {
		return $this->normalizedText;
	}
	
	function getError() {
		return $this->error;
	}
	
	function getTimesExecuted() {
		return $this->count;
	}
	
	function & getFilteredExamplesArray() {
		$returnExamples = false;
		
		$exampleCount = count($this->examples);
		for($i = 0; $i < $exampleCount; $i++) {
			$example =& $this->examples[$i];
			if($example->getText() != $this->getNormalizedText()) {
				return $this->examples;
			}
			unset($example);
		}
		return array();
	}
	
	function getDetail() {
		return $this->detail;
	}
	
	function getHint() {
		return $this->hint;
	}
}

?>