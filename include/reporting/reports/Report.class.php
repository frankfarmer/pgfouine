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
	
	function getTextTitle($title) {
		return "\n#####  ".$title."  #####\n\n";
	}
	
	function getHtmlTitle($title) {
		return '<h2>'.$title.'</h2>';
	}
	
	function pad($string, $length) {
		return str_pad($string, $length, ' ', STR_PAD_LEFT);
	}
	
	function getPercentage($number, $total) {
		return $this->pad(number_format($number*100/$total, 1), 5);
	}
	
	function formatDuration($duration, $decimals = 2) {
		return number_format($duration, $decimals).' s';
	}
} 

?>