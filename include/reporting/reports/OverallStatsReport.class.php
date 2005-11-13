<?php

class OverallStatsReport extends Report {
	function OverallStatsReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Overall statistics', array('GlobalCountersListener'));
	}
	
	function getText() {
		$listener = $this->reportAggregator->getListener('GlobalCountersListener');
		$text = $this->getTextTitle($this->getTitle());
		
		$text .= 
			'Number of queries:     '.$listener->getQueryCount()."\n".
			'Total query duration:  '.$this->formatDuration($listener->getQueryDuration(), 1)."\n"
		;
		
		return $text;
	}
}

?>