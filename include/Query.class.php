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

class Query {
	var $text;
	var $db;
	var $user;
	var $duration = false;
	var $ignored;
	var $timestamp = 0;
	var $commandNumber = 0;
	var $parsingSubQueries = false;
	var $subQueries = array();

	function Query($text = '', $ignored = false) {
		if(DEBUG > 1 && !$text) stderr('Empty text for Query', true);
		$this->text = $text;
		$this->ignored = $ignored;
	}
	
	function setContextInformation($timestamp, $commandNumber) {
		$this->timestamp = $timestamp;
		$this->commandNumber = $commandNumber; 
	}
	
	function getCommandNumber() {
		return $this->commandNumber;
	}
	
	function getTimestamp() {
		return $this->timestamp;
	}

	function append($text) {
		if(DEBUG > 1 && !$text) stderr('Empty text for append', true);
		if($this->parsingSubQueries) {
			$subQuery =& last($this->subQueries);
			$subQuery .= ' '.normalizeWhitespaces($text);
		} else {
			$this->text .= ' '.$text;
		}
	}
	
	function setSubQuery($text) {
		if(DEBUG > 1 && !$text) stderr('Empty text for setSubQuery', true);
		$this->parsingSubQueries = true;
		$this->subQueries[] = normalizeWhitespaces($text);
	}
	
	function setDb($db) {
		$this->db = $db;
	}
	
	function setUser($user) {
		$this->user = $user;
	}
	
	function getNormalizedText() {
		$regexpRemoveText = "/'[^']*'/";
		$regexpRemoveNumbers = '/([^a-zA-Z_\$])([0-9]{1,10})/';

		$text = '';
		if($this->text) {
			$text = normalizeWhitespaces($this->text);
			$text = str_replace("\\'", '', $text);
			$text = preg_replace($regexpRemoveText, "''", $text);
			$text = preg_replace($regexpRemoveNumbers, '${1}0', $text);
		}
		return $text;
	}
	
	function accumulateTo(& $accumulator) {
		if(!$this->isIgnored()) {
			$this->text = normalizeWhitespaces($this->text);
			$accumulator->appendQuery($this);
		}
	}

	function isSelect() {
		return $this->check('select');
	}
	
	function isDelete() {
		return $this->check('delete');
	}
	
	function isInsert() {
		return $this->check('insert');
	}
	
	function isUpdate() {
		return $this->check('update');
	}
	
	function check($start) {
		$queryStart = strtolower(substr(trim($this->text), 0, 10));
		return (strpos($queryStart, $start) === 0);
	}
	
	function isIgnored() {
		return ($this->ignored || (getConfig('only_select') && !$this->isSelect()));
	}
	
	function setDuration($duration) {
		$this->duration = $duration;
	}
	
	function getDuration() {
		return $this->duration;
	}
	
	function getDb() {
		return $this->db;
	}
	
	function getUser() {
		return $this->user;
	}
	
	function getText() {
		return $this->text;
	}
	
	function isParsingSubQueries() {
		return $this->parsingSubQueries;
	}
	
	function getSubQueries() {
		return $this->subQueries;
	}
}

?>