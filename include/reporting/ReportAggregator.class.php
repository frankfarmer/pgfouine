<?php

class ReportAggregator {
	var $reports = array();
	var $logReader;
	
	function ReportAggregator(& $logReader) {
		$this->logReader =& $logReader;
	}
	
	function addReport($reportName) {
		$report = new $reportName($this);
		$this->reports[] =& $report;
	}
	
	function & getListener($listenerName) {
		return $this->logReader->getListener($listenerName);
	}
	
	function getOutput() {
		$needs = $this->getNeeds();
		foreach($needs AS $need) {
			$this->logReader->addListener($need);
		}
		$this->logReader->parse();
		
		$output = '';
		$output .= $this->getHeader();
		$output .= $this->getBody();
		$output .= $this->getFooter();
		
		return $output;
	}
	
	function getNeeds() {
		$needs = array();
		$count = count($this->reports);
		for($i = 0; $i < $count; $i++) {
			$needs = array_merge($needs, $this->reports[$i]->getNeeds());
		}
		$needs = array_unique($needs);
		return $needs;
	}
	
	function getFileName() {
		return $this->logReader->getFileName();
	}
	
	function getTimeToParse() {
		return $this->logReader->getTimeToParse();
	}
	
	function getLineParsedCount() {
		return $this->logReader->getLineParsedCount();
	}
}

?>