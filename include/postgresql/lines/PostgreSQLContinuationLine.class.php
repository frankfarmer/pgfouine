<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005 Guillaume Smet
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

class PostgreSQLContinuationLine extends PostgreSQLLogLine {
	var $ignore = false;

	function PostgreSQLContinuationLine($text, $duration = false) {
		$this->PostgreSQLLogLine(str_replace('^I', "\t", $text));
	}

	function appendTo(& $queries) {
		$query =& $queries->last();
		if($query && ($query->getCommandNumber() == $this->commandNumber)) {
			if(substr(trim($this->text), 0, 2) != '--') {
				$query->append($this->text);
			}
		} else {
			stderr('Continuation for no previous query', true);
		}
		return false;
	}
}

?>