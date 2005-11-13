<?php

class QueriesByTypeReport extends Report {
	function QueriesByTypeReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Queries by type', array('GlobalCountersListener'));
	}
	
	function getText() {
		$listener = $this->reportAggregator->getListener('GlobalCountersListener');
		$text = $this->getTextTitle($this->getTitle());
		
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
}

?>