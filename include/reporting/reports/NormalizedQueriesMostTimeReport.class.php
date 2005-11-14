<?php

class NormalizedQueriesMostTimeReport extends Report {
	function NormalizedQueriesMostTimeReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Queries that took up the most time - normalized', array('NormalizedQueriesListener'));
	}
	
	function getText() {
		$listener = $this->reportAggregator->getListener('NormalizedQueriesListener');
		$text = '';
		
		$queries =& $listener->getQueriesMostTime();
		
		$count = count($queries);
		
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$text .= ($i+1).') '.$this->formatDuration($query['duration']).' - '.$query['count'].' - '.$query['normalizedText']."\n";
			$text .= "--\n";
		}
		return $text;
	}
	
	function getHtml() {
	}
}

?>