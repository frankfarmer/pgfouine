<?php

class SlowestQueriesListener extends QueryListener {
	var $queryList;
	
	function SlowestQueriesListener($queriesNumber = DEFAULT_TOP_QUERIES_NUMBER) {
		$this->queryList = new SlowestQueryList($queriesNumber);
	}
	
	function fireEvent(& $query) {
		$this->queryList->addQuery($query);
	}
	
	function & getSortedQueries() {
		return $this->queryList->getSortedQueries();
	}
}

?>