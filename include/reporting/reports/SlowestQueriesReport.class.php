<?php

class SlowestQueriesReport extends Report {
	function SlowestQueriesReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Slowest queries', array('SlowestQueriesListener'));
	}
	
	function getText() {
		$listener =& $this->reportAggregator->getListener('SlowestQueriesListener');
		$text = '';
		
		$queries =& $listener->getSortedQueries();
		$count = count($queries);
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$text .= ($i+1).') '.$this->formatDuration($query->getDuration()).' - '.$query->getText()."\n";
			$text .= "--\n";
		}
		return $text;
	}
	
	function getHtml() {
		$listener =& $this->reportAggregator->getListener('SlowestQueriesListener');
		$html = '
<table class="queryList">
	<tr>
		<th>Rank</th>
		<th>Time&nbsp;(s)</th>
		<th>Query</th>
	</tr>';
		$queries =& $listener->getSortedQueries();
		$count = count($queries);
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$html .= '<tr class="'.$this->getRowStyle($i).'">
				<td class="center top">'.($i+1).'</td>
				<td class="relevantInformation top center">'.$this->formatDuration($query->getDuration()).'</td>
				<td>'.$this->highlightSql($query->getText()).'</td>
			</tr>';
			$html .= "\n";
		}
		$html .= '</table>';
		return $html;
	}
}

?>