<?php

class SlowestQueriesListener extends QueryListener {
	var $queryList;
	
	function SlowestQueriesListener() {
		$this->queryList = new SlowestQueryList(10);
	}
	
	function fireEvent(& $query) {
		$this->queryList->addQuery($query);
	}
	
	function & getSortedQueries() {
		return $this->queryList->getSortedQueries();
	}
}

?>