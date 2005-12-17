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

class NormalizedQueriesListener extends QueryListener {
	var $queryList = array();
	var $queriesNumber = 10;
	
	function NormalizedQueriesListener() {
		$this->queriesNumber = getConfig('default_top_queries_number');
	}
	
	function fireEvent(& $query) {
		$normalizedText = $query->getNormalizedText();
		if(isset($this->queryList[$normalizedText])) {
			$this->queryList[$normalizedText]->addQuery($query);
		} else {
			$this->queryList[$normalizedText] = new NormalizedQuery($query);
		}
	}
	
	function & getQueriesMostTime() {
		$queryList = $this->queryList;
		usort($queryList, array($this, 'compareMostTime'));
		return array_slice($queryList, 0, $this->queriesNumber);
	}
	
	function compareMostTime(& $a, & $b) {
		if($a->getTotalDuration() == $b->getTotalDuration()) {
			return 0;
		} elseif($a->getTotalDuration() < $b->getTotalDuration()) {
			return 1;
		} else {
			return -1;
		}
	}
	
	function & getMostFrequentQueries() {
		$queryList = $this->queryList;
		usort($queryList, array($this, 'compareMostFrequent'));
		return array_slice($queryList, 0, $this->queriesNumber);
	}
	
	function compareMostFrequent(& $a, & $b) {
		if($a->getTimesExecuted() == $b->getTimesExecuted()) {
			return 0;
		} elseif($a->getTimesExecuted() < $b->getTimesExecuted()) {
			return 1;
		} else {
			return -1;
		}
	}
	
	function & getSlowestQueries() {
		$queryList = $this->queryList;
		usort($queryList, array($this, 'compareSlowest'));
		return array_slice($queryList, 0, $this->queriesNumber);
	}
	
	function compareSlowest(& $a, & $b) {
		if($a->getAverageDuration() == $b->getAverageDuration()) {
			return 0;
		} elseif($a->getAverageDuration() < $b->getAverageDuration()) {
			return 1;
		} else {
			return -1;
		}
	}
	
	function getUniqueQueryCount() {
		return count($this->queryList);
	}
}

?>