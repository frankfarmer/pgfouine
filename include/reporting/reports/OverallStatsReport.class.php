<?php

class OverallStatsReport extends Report {
	function OverallStatsReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Overall statistics', array('GlobalCountersListener'));
	}
	
	function getText() {
		$listener = $this->reportAggregator->getListener('GlobalCountersListener');
		$normalizedListener = $this->reportAggregator->getListener('NormalizedQueriesListener');
		
		$text = '';
		
		if($normalizedListener) {
			$text .= 'Number of unique normalized queries: '.$normalizedListener->getUniqueQueryCount()."\n";
		}
		$text .= 
			'Number of queries:     '.$listener->getQueryCount()."\n".
			'Total query duration:  '.$this->formatDuration($listener->getQueryDuration(), 1)."\n"
		;
		
		return $text;
	}
	
	function getHtml() {
		$listener = $this->reportAggregator->getListener('GlobalCountersListener');
		$normalizedListener = $this->reportAggregator->getListener('NormalizedQueriesListener');
		
		$html = '';
		
		$html .= '<ul>';
		if($normalizedListener) {
			$html .= '<li>Number of unique normalized queries: '.$normalizedListener->getUniqueQueryCount().'</li>';
		}
		$html .= '<li>Number of queries: '.$listener->getQueryCount().'</li>';
		$html .= '<li>Total query duration: '.$this->formatDuration($listener->getQueryDuration(), 1).' s</li>';
		$html .= '</ul>';
		
		return $html;
	}
}

?>