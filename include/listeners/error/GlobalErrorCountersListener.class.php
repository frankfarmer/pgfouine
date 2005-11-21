<?php

class GlobalErrorCountersListener extends ErrorListener {
	var $errorCount = 0;
	
	function fireEvent(& $error) {
		$this->errorCount++;
	}
	
	function getErrorCount() {
		return $this->errorCount;
	}
}

?>