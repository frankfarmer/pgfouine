<?php

class HtmlReportAggregator extends ReportAggregator {
	var $geshi;
	
	function HtmlReportAggregator(& $logReader) {
		$this->ReportAggregator($logReader);
		
		$this->geshi = new GeSHi('', 'sql');
		$this->geshi->enable_classes();
		$this->geshi->set_case_keywords(GESHI_CAPS_UPPER);
		$this->geshi->set_header_type(GESHI_HEADER_DIV);
	}
	
	function highlightSql($sql) {
		$this->geshi->set_source($sql);
		return $this->geshi->parse_code();
	}
	
	function getHeader() {
		$header = '
<html>
	<head>
		<title>pgFouine: PostgreSQL log analysis</title>
		<style type="text/css">
			'.$this->getStyles().'
		</style>
	</head>
	<body>
		<div id="content">
			<h1 id="top">PostgreSQL log analysis</h1>
		';
		return $header;
	}
	
	function getBody() {
		$count = count($this->reports);
		
		$reportsOutput = '';
		$menu = '<div class="menu">';
		
		for($i = 0; $i < $count; $i++) {
			$report =& $this->reports[$i];
			if($i > 0) {
				$menu .= ' | ';
			}
			$menu .= '<a href="#'.$report->getReportClass().'">'.$report->getTitle().'</a>';
			
			$reportsOutput .= $report->getHtmlTitle($report->getTitle());
			$reportsOutput .= $report->getHtml();
			$reportsOutput .= "\n";
		}
		$menu .= '</div>';
		
		$output = $menu."\n".$reportsOutput;
		
		return $output;
	}
	
	function getFooter() {
		$footer = '
		</div>
	</body>
</html>
		';
		return $footer;
	}
	
	function getStyles() {
		$styles = '
			body { background-color: #FFFFFF; }
			* { font-family: Verdana, Arial, Helvetica; }
			div, p, th, td { font-size:12px; }
			table.queryList td, table.queryList th { padding: 2px; }
			table.queryList th { background-color: #DDDDDD; border:1px solid #CCCCCC; }
			table.queryList tr.row0 td { background-color: #FFFFFF; border: 1px solid #EEEEEE; }
			table.queryList tr.row1 td { background-color: #EEEEEE; }
			table.queryList td.top { vertical-align:top; }
			table.queryList td.rank { text-align:center; }
			table.queryList td.relevantInformation { font-weight:bold; }
		';
		$styles .= $this->geshi->get_stylesheet();
		return $styles;
	}
}

?>