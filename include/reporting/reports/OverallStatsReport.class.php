<?php

class OverallStatsReport extends Report {
	function OverallStatsReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Overall statistics', array('GlobalCountersListener'));
	}
	
	function getText() {
		$statsListener =& $this->reportAggregator->getListener('GlobalCountersListener');
		$normalizedListener =& $this->reportAggregator->getListener('NormalizedQueriesListener');
		$errorCountersListener =& $this->reportAggregator->getListener('GlobalErrorCountersListener');
		$normalizedErrorsListener =& $this->reportAggregator->getListener('NormalizedErrorsListener');
		
		$text = '';
		
		if($normalizedListener) {
			$text .= 'Number of unique normalized queries: '.$this->formatInteger($normalizedListener->getUniqueQueryCount())."\n";
		}
		$text .= 
			'Number of queries:     '.$this->formatInteger($statsListener->getQueryCount())."\n".
			'Total query duration:  '.$this->formatLongDuration($statsListener->getQueryDuration())."\n"
		;
		if($errorCountersListener) {
			$text .= 'Number of errors:     '.$this->formatInteger($errorCountersListener->getErrorCount())."\n";
			if($normalizedErrorsListener) {
				$text .= 'Number of unique normalized errors: '.$this->formatInteger($normalizedErrorsListener->getUniqueErrorCount())."\n";
			}
		}
		
		return $text;
	}
	
	function getHtml() {
		$statsListener =& $this->reportAggregator->getListener('GlobalCountersListener');
		$normalizedListener =& $this->reportAggregator->getListener('NormalizedQueriesListener');
		$errorCountersListener =& $this->reportAggregator->getListener('GlobalErrorCountersListener');
		$normalizedErrorsListener =& $this->reportAggregator->getListener('NormalizedErrorsListener');
		
		$html = '';
		
		$html .= '<ul>';
		if($normalizedListener) {
			$html .= '<li>Number of unique normalized queries: '.$this->formatInteger($normalizedListener->getUniqueQueryCount()).'</li>';
		}
		$html .= '<li>Number of queries: '.$this->formatInteger($statsListener->getQueryCount()).'</li>';
		$html .= '<li>Total query duration: '.$this->formatLongDuration($statsListener->getQueryDuration()).'</li>';
		if($errorCountersListener) {
			$html .= '<li>Number of errors: '.$this->formatInteger($errorCountersListener->getErrorCount()).'</li>';
			if($normalizedErrorsListener) {
				$html .= '<li>Number of unique normalized errors: '.$this->formatInteger($normalizedErrorsListener->getUniqueErrorCount()).'</li>';
			}
		}
		$html .= '</ul>';
		
		return $html;
	}
}

?>