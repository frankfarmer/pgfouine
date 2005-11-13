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
				if(DEBUG) stderr('Unrecognized context or context for an error');
				$this->PostgreSQLLogLine($text);
			}
		}
	}

	function appendTo(& $queries) {
		$lastQuery =& $queries->last();
		if(is_a($lastQuery, 'ErrorQuery')) {
			// we have an error query
			$lastQuery->appendContext($this->text);
		} else {
			$subQuery =& $queries->pop();
			$query =& $queries->last();
			
			if(!$subQuery) {
				stderr('Missing query for context');
			} elseif($query) {
				$query->setSubQuery($subQuery->getText());
			} else {
				stderr('Context for no previous query');
			}
		}
		return false;
	}
}

?>