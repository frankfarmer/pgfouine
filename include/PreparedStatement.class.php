<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2006 Guillaume Smet
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

class PreparedStatement {
	var $name;
	var $portalName;
	var $text;
	var $parameters = array();
	var $numberOfBinds = 0;
	var $currentBindParameters = array();
	var $numberOfExecutes = 0;
	
	function PreparedStatement($name, $portalName, $text = '') {
		$this->name = $name;
		$this->portalName = $portalName;
		$this->text = $text;
	}
	
	function appendDetail($detail) {
		global $postgreSQLRegexps;
		
		$parametersValues =& $postgreSQLRegexps['BindDetail']->matchAll($detail);
		
		for($i = 0; $i < count($parametersValues); $i++) {
			$this->parameters[$parametersValues[$i][1]] = $parametersValues[$i][2];
		}
	}
	
	function getName() {
		return $this->name;
	}
	
	function bind() {
		$this->numberOfBinds ++;
	}
	
	function execute() {
		$this->numberOfExecutes ++;
	}
	
	function getQueryText($queryText) {
		if(count($this->parameters) > 0) {
			$replace = array();
			foreach($this->parameters as $key => $value) {
				if(is_numeric($value)) {
					$replace[$key] = $value;
				} elseif($value == '(null)') {
					$replace[$key] = 'NULL';
				} else {
					$replace[$key] = "'".str_replace("'", "''", $value)."'";
				}
			}
			$queryText = strtr($queryText, $replace);
		}
		return $queryText;
	}
	
	function isIgnored() {
		return false;
	}
}

?>