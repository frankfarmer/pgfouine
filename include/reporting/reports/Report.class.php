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

class Report {
	var $reportAggregator;
	var $title = '';
	var $needs = array();
	
	function Report(& $reportAggregator, $title, $needs) {
		$this->reportAggregator =& $reportAggregator;
		$this->title = $title;
		$this->needs = $needs;
	}
	
	function getTitle() {
		return $this->title;
	}
	
	function getNeeds() {
		return $this->needs;
	}
	
	function getTextTitle() {
		return "\n#####  ".$this->title."  #####\n\n";
	}
	
	function getHtmlTitle() {
		return '<h2 id="'.$this->getReportClass().'">'.$this->title.' <a href="#top" title="Back to top">^</a></h2>';
	}
	
	function pad($string, $length) {
		return $this->reportAggregator->pad($string, $length);
	}
	
	function getPercentage($number, $total) {
		return $this->reportAggregator->getPercentage($number, $total);
	}
	
	function formatInteger($integer) {
		return $this->reportAggregator->formatInteger($integer);
	}
	
	function formatTimestamp($timestamp) {
		return $this->reportAggregator->formatTimestamp($timestamp);
	}
	
	function formatDuration($duration, $decimals = 2) {
		return $this->reportAggregator->formatDuration($duration, $decimals);
	}
	
	function formatLongDuration($duration) {
		return $this->reportAggregator->formatLongDuration($duration);
	}
	
	function getReportClass() {
		return get_class($this);
	}
	
	function getRowStyle($i) {
		return 'row'.($i%2);
	}
	
	function highlightSql($sql, $prepend = '', $append = '') {
		return $this->reportAggregator->highlightSql($sql, $prepend, $append);
	}
} 

?>