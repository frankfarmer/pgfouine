<?php

class HtmlReportAggregator extends ReportAggregator {
	var $geshi;
	
	function HtmlReportAggregator(& $logReader) {
		$this->ReportAggregator($logReader);
		
		$this->geshi = new GeSHi('', 'sql');
		$this->geshi->enable_classes();
		$geshi->set_case_keywords(GESHI_CAPS_UPPER);
		$geshi->set_header_type(GESHI_HEADER_PRE);
	}
	
	function highlightSql($sql) {
		$this->geshi->set_source($sql);
		return $this->geshi->parse_code();
	}
	
	function getHeader() {
	}
	
	function getFooter() {
	}
	
	function getStyles() {
		$styles = '';
		$styles .= $this->geshi->get_stylesheet();
	}
}

?>