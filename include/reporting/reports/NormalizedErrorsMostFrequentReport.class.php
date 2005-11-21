<?php

class NormalizedErrorsMostFrequentReport extends NormalizedErrorsReport {
	function NormalizedErrorsMostFrequentReport(& $reportAggregator) {
		$this->NormalizedErrorsReport($reportAggregator, 'Most frequent errors');
	}
	
	function getText() {
		$listener = $this->reportAggregator->getListener('NormalizedQueriesListener');
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
		$listener = $this->reportAggregator->getListener('NormalizedErrorsListener');
		$errors =& $listener->getMostFrequentErrors();
		$count = count($errors);
		
		if($count == 0) {
			$html .= '<p>No error found</p>';
		}
		
		$html = '
<table class="queryList">
	<tr>
		<th>Rank</th>
		<th>Times executed</th>
		<th>Error</th>
	</tr>';
		
		for($i = 0; $i < $count; $i++) {
			$error =& $errors[$i];
			$html .= '<tr class="'.$this->getRowStyle($i).'">
				<td class="center top">'.($i+1).'</td>
				<td class="relevantInformation top center">'.$this->formatInteger($error->getTimesExecuted()).'</td>
				<td><div class=error>'.$error->getError().'</div>
					'.$this->getNormalizedErrorWithExamplesHtml($i, $error).'</td>
			</tr>';
			$html .= "\n";
		}
		$html .= '</table>';
		return $html;
	}
}

?>