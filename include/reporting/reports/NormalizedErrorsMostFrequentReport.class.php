<?php

class NormalizedErrorsMostFrequentReport extends NormalizedErrorsReport {
	function NormalizedErrorsMostFrequentReport(& $reportAggregator) {
		$this->NormalizedErrorsReport($reportAggregator, 'Most frequent errors');
	}
	
	function getText() {
		$listener =& $this->reportAggregator->getListener('NormalizedErrorsListener');
		$text = '';
		
		$errors =& $listener->getMostFrequentErrors();
		
		$count = count($errors);
		
		for($i = 0; $i < $count; $i++) {
			$error =& $errors[$i];
			$text .= ($i+1).') '.$this->formatInteger($error->getTimesExecuted()).' - '.$error->getNormalizedText()."\n";
			$text .= 'Error: '.$error->getError()."\n";
			if($error->getDetail()) {
				$text .= 'Detail: '.$error->getDetail()."\n";
			}
			if($error->getHint()) {
				$text .= 'Hint: '.$error->getHint()."\n";
			}
			$text .= "--\n";
		}
		return $text;
	}
	
	function getHtml() {
		$listener =& $this->reportAggregator->getListener('NormalizedErrorsListener');
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
				<td><div class=error>Error: '.$error->getError().'</div>';
			if($error->getDetail() || $error->getHint()) {
				$html .= '<div class="errorInformation">';
				if($error->getDetail()) {
					$html .= 'Detail: '.$error->getDetail();
					$html .= '<br />';
				}
				if($error->getHint()) {
					$html .= 'Hint: '.$error->getHint();
					$html .= '<br />';
				}
				$html .= '</div>';
			}
			$html .= $this->getNormalizedErrorWithExamplesHtml($i, $error).'</td>
			</tr>';
			$html .= "\n";
		}
		$html .= '</table>';
		return $html;
	}
}

?>