<?php

class NormalizedQueriesSlowestAverageReport extends NormalizedReport {
	function NormalizedQueriesSlowestAverageReport(& $reportAggregator) {
		$this->NormalizedReport($reportAggregator, 'Slowest queries');
	}
	
	function getText() {
		$listener = $this->reportAggregator->getListener('NormalizedQueriesListener');
		$text = '';
		
		$queries =& $listener->getSlowestQueries();
		
		$count = count($queries);
		
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$text .= ($i+1).') '.$this->formatDuration($query->getAverageDuration()).' - '.$query->getTimesExecuted().' - '.$query->getNormalizedText()."\n";
			$text .= "--\n";
		}
		return $text;
	}
	
	function getHtml() {
		$listener = $this->reportAggregator->getListener('NormalizedQueriesListener');
		$html = '
<table class="queryList">
	<tr>
		<th>Rank</th>
		<th>Average time&nbsp;(s)</th>
		<th>Times executed</th>
		<th>Total time&nbsp;(s)</th>
		<th>Query</th>
	</tr>';
		$queries =& $listener->getSlowestQueries();
		$count = count($queries);
		
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$html .= '<tr class="'.$this->getRowStyle($i).'">
				<td class="center top">'.($i+1).'</td>
				<td class="relevantInformation top center">'.$this->formatDuration($query->getAverageDuration()).'</td>
				<td class="top center">'.$query->getTimesExecuted().'</td>
				<td class="top center">'.$this->formatDuration($query->getTotalDuration()).'</td>
				<td>'.$this->getNormalizedQueryWithExamplesHtml($i, $query).'</td>
			</tr>';
			$html .= "\n";
		}
		$html .= '</table>';
		return $html;
	}
}

?>