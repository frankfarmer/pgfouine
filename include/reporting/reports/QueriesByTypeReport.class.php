<?php

class QueriesByTypeReport extends Report {
	function QueriesByTypeReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Queries by type', array('GlobalCountersListener'));
	}
	
	function getText() {
		$listener =& $this->reportAggregator->getListener('GlobalCountersListener');
		$text = '';
		
		$queriesCount = $listener->getQueryCount();
		$selectCount = $listener->getSelectCount();
		$insertCount = $listener->getInsertCount();
		$updateCount = $listener->getUpdateCount();
		$deleteCount = $listener->getDeleteCount();
		
		$pad = strlen($queriesCount);
		
		if($selectCount > 0) {
			$text .= 'SELECT:    '.$this->pad($selectCount, $pad).'    '.$this->getPercentage($selectCount, $queriesCount).'%';
			$text .= "\n";
		}
		if($insertCount > 0) {
			$text .= 'INSERT:    '.$this->pad($insertCount, $pad).'    '.$this->getPercentage($insertCount, $queriesCount).'%';
			$text .= "\n";
		}
		if($updateCount > 0) {
			$text .= 'UPDATE:    '.$this->pad($updateCount, $pad).'    '.$this->getPercentage($updateCount, $queriesCount).'%';
			$text .= "\n";
		}
		if($deleteCount > 0) {
			$text .= 'DELETE:    '.$this->pad($deleteCount, $pad).'    '.$this->getPercentage($deleteCount, $queriesCount).'%';
			$text .= "\n";
		}
		return $text;
	}
	
	function getHtml() {
		$listener =& $this->reportAggregator->getListener('GlobalCountersListener');
		
		$queriesCount = $listener->getQueryCount();
		
		$typeCount = array();
		if($listener->getSelectCount()) {
			$typeCount['SELECT'] = $listener->getSelectCount();
		}
		if($listener->getInsertCount()) {
			$typeCount['INSERT'] = $listener->getInsertCount();
		}
		if($listener->getUpdateCount()) {
			$typeCount['UPDATE'] = $listener->getUpdateCount();
		}
		if($listener->getDeleteCount()) {
			$typeCount['DELETE'] = $listener->getDeleteCount();
		}
		
		$html = '
<table class="queryList">
	<tr>
		<th>Type</th>
		<th>Count</th>
		<th>Percentage</th>
	</tr>';
		$i = 0;
		foreach($typeCount AS $type => $count) {
			$html .= '<tr class="'.$this->getRowStyle($i).'">
				<td>'.$type.'</td>
				<td class="right">'.$this->formatInteger($count).'</td>
				<td class="right">'.$this->getPercentage($count, $queriesCount).'</td>
			</tr>';
			$html .= "\n";
			$i++;
		}
		$html .= '</table>';
		return $html;
	}
}

?>