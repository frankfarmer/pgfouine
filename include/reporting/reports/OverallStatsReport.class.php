<?php

class OverallStatsReport extends Report {
	function OverallStatsReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Overall statistics', array('GlobalCountersListener'));
	}
	
	function getText() {
		$listener = $this->reportAggregator->getListener('GlobalCountersListener');
		$text = '';
		
		$text .= 
			'Number of queries:     '.$listener->getQueryCount()."\n".
			'Total query duration:  '.$this->formatDuration($listener->getQueryDuration(), 1)."\n"
		;
		
		return $text;
	}
	
	function getHtml() {
		$listener = $this->reportAggregator->getListener('GlobalCountersListener');
		$html = '';
		
		$html .= '<ul>';
		$html .= '<li>Number of queries: '.$listener->getQueryCount().'</li>';
		$html .= '<li>Total query duration: '.$this->formatDuration($listener->getQueryDuration(), 1).' s</li>';
		$html .= '</ul>';
		
		return $html;
	}
}

?>