<?php

class NormalizedReport extends Report {
	function NormalizedReport(& $reportAggregator, $title, $needs = array()) {
		$needs = array_merge(array('NormalizedQueriesListener'), $needs);
		$this->Report($reportAggregator, $title.' (N)', $needs);
	}
	
	function getNormalizedQueryWithExamplesHtml($counter, & $normalizedQuery) {
		$html = '';
		$html .= $this->highlightSql($normalizedQuery->getNormalizedText());
		
		$examples =& $normalizedQuery->getFilteredExamplesArray();
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