<?php

class TextReportAggregator extends ReportAggregator {
	
	function TextReportAggregator(& $logReader) {
		$this->ReportAggregator($logReader);
	}
	
	function getHeader() {
	}
	
	function getFooter() {
	}
	
	function getBody() {
		$count = count($this->reports);
		$output = '';
		for($i = 0; $i < $count; $i++) {
			$output .= $this->reports[$i]->getTextTitle($this->reports[$i]->getTitle());
			$output .= $this->reports[$i]->getText();
			$output .= "\n";
		}
		return $output;
	}
}

?>