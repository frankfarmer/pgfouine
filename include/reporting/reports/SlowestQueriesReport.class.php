<?php

class SlowestQueriesReport extends Report {
	function SlowestQueriesReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Slowest queries', array('SlowestQueriesListener'));
	}
	
	function getText() {
		$listener = $this->reportAggregator->getListener('SlowestQueriesListener');
		$text = $this->getTextTitle($this->getTitle());
		
		$queries =& $listener->getSortedQueries();
		$count = count($queries);
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$text .= $this->formatDuration($query->getDuration()).' - '.$query->getText()."\n";
			$text .= "--\n";
		}
		return $text;
	}
	
	function getHtml() {
	}	
}

?>