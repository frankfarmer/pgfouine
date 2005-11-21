<?php

class NormalizedQueriesMostFrequentReport extends NormalizedReport {
	function NormalizedQueriesMostFrequentReport(& $reportAggregator) {
		$this->NormalizedReport($reportAggregator, 'Most frequent queries');
	}
	
	function getText() {
		$listener =& $this->reportAggregator->getListener('NormalizedQueriesListener');
		$text = '';
		
		$queries =& $listener->getMostFrequentQueries();
		
		$count = count($queries);
		
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$text .= ($i+1).') '.$this->formatInteger($query->getTimesExecuted()).' - '.$this->formatLongDuration($query->getTotalDuration()).' - '.$query->getNormalizedText()."\n";
			$text .= "--\n";
		}
		return $text;
	}
	
	function getHtml() {
		$listener =& $this->reportAggregator->getListener('NormalizedQueriesListener');
		$html = '
<table class="queryList">
	<tr>
		<th>Rank</th>
		<th>Times executed</th>
		<th>Total time</th>
		<th>Average time&nbsp;(s)</th>
		<th>Query</th>
	</tr>';
		$queries =& $listener->getMostFrequentQueries();
		$count = count($queries);
		
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$html .= '<tr class="'.$this->getRowStyle($i).'">
				<td class="center top">'.($i+1).'</td>
				<td class="relevantInformation top center">'.$this->formatInteger($query->getTimesExecuted()).'</td>
				<td class="top center">'.$this->formatLongDuration($query->getTotalDuration()).'</td>
				<td class="top center">'.$this->formatDuration($query->getAverageDuration()).'</td>
				<td>'.$this->getNormalizedQueryWithExamplesHtml($i, $query).'</td>
			</tr>';
			$html .= "\n";
		}
		$html .= '</table>';
		return $html;
	}
}

?>