<?php

class PostgreSQLContextLine extends PostgreSQLLogLine {
	var $ignore = false;

	function PostgreSQLContextLine($text) {
		$regexpSqlStatement = new RegExp('/^SQL statement "/');
		$regexpSqlFunction = new RegExp('/([^\s]+)[\s]+function[\s]+"([^"]+)"(.*)$/');
		
		$statementMatch =& $regexpSqlStatement->match($text);
		if($statementMatch) {
			$this->PostgreSQLLogLine(substr($statementMatch->getPostMatch(), -1, 1));
		} else {
			$functionMatch =& $regexpSqlFunction->match($text);
			if($functionMatch) {
				$this->PostgreSQLLogLine($statementMatch->getMatch(2));
			} else {
				if(DEBUG) stderr('Unrecognized Context');
				$this->PostgreSQLLogLine($text);
			}
		}
	}

	function appendTo(& $queries) {
		$subQuery =& $queries->pop();
		$query =& $queries->last();
		
		if(!$subQuery) {
			stderr('Missing Query for Context');
		} elseif($query) {
			$query->setSubQuery($subQuery->getText());
		} else {
			stderr('Context for no previous Query');
		}
		return false;
	}
}

?>