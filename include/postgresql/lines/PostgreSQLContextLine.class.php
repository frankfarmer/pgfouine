<?php

class PostgreSQLContextLine extends PostgreSQLLogLine {
	var $ignore = false;
	var $recognized = true;

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
				$this->recognized = false;
				$this->PostgreSQLLogLine($text);
			}
		}
	}

	function appendTo(& $queries) {
		$lastQuery =& $queries->last();
		if(is_a($lastQuery, 'ErrorQuery')) {
			// we have an error query so we put the context in a subquery
			$lastQuery->setSubQuery($this->text);
		} else {
			if(DEBUG && !$this->recognized) stderr('Unrecognized context or context for an error', true);
			
			$subQuery =& $queries->pop();
			$query =& $queries->last();
			
			if(!$subQuery) {
				stderr('Missing query for context', true);
			} elseif($query) {
				$query->setSubQuery($subQuery->getText());
			} else {
				stderr('Context for no previous query', true);
			}
		}
		return false;
	}
}

?>