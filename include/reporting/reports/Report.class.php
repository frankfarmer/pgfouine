<?php

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