<?php

class NormalizedErrorsReport extends Report {
	function NormalizedErrorsReport(& $reportAggregator, $title, $needs = array()) {
		$needs = array_merge(array('NormalizedErrorsListener', 'GlobalErrorCountersListener'), $needs);
		$this->Report($reportAggregator, $title.' (N)', $needs);
	}
	
	function getNormalizedErrorWithExamplesHtml($counter, & $normalizedError) {
		$html = '';
		$html .= $this->highlightSql($normalizedError->getNormalizedText());
		
		$examples =& $normalizedError->getFilteredExamplesArray();
		$exampleCount = count($examples);
		
		if($exampleCount) {		
			$buttonId = 'button_'.$this->getReportClass().'_'.$counter;
			$divId = 'examples_'.$this->getReportClass().'_'.$counter;
			
			$html .= '<input type="button" class="examplesButton" id="'.$buttonId.'" name="'.$buttonId.'" value="Show examples" onclick="javascript:toggle(\''.$buttonId.'\', \''.$divId.'\', \'examples\');" />';
			$html .= '<div id="'.$divId.'" class="examples" style="display:none;">';
			
			
			for($i = 0; $i < $exampleCount; $i++) {
				$example =& $examples[$i];
				$html .= '<div class="example'.($i%2).'">';
				$html .= $this->highlightSql($example->getText());
				$html .= '</div>';
				unset($example);
			}
			$html .= '</div>';
		}
		
		return $html;
	}
} 

?>