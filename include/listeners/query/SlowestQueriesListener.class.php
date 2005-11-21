<?php

class SlowestQueriesListener extends QueryListener {
	var $queryList;
	
	function SlowestQueriesListener() {
		$this->queryList = new SlowestQueryList(getConfig('default_top_queries_number'));
	}
	
	function fireEvent(& $query) {
		$this->queryList->addQuery($query);
	}
	
	function & getSortedQueries() {
		return $this->queryList->getSortedQueries();
	}
}

?>