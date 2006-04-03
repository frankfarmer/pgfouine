<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005-2006 Guillaume Smet
 *
 * pgFouine is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * pgFouine is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with pgFouine; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

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
				$title = $example->getDetailedInformation();
				
				$html .= '<div class="example'.($i%2).'" title="'.$title.'">';
				$html .= $this->highlightSql($example->getText(), $this->formatDuration($example->getDuration()).'s | ');
				$html .= '</div>';
				unset($example);
			}
			$html .= '</div>';
		}
		
		return $html;
	}
} 

?>