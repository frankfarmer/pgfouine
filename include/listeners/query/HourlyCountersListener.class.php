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

class HourlyCountersListener extends QueryListener {
	var $hourlyStatistics = array();
	
	function fireEvent(& $query) {
		$formattedTimestamp = date('Y-m-d H:00:00', $query->getTimestamp());
		
		if(!isset($this->hourlyStatistics[$formattedTimestamp])) {
			$this->hourlyStatistics[$formattedTimestamp] = new QueryCounter();
		}
		$queryCounter =& $this->hourlyStatistics[$formattedTimestamp];
		
		$queryCounter->incrementQueryCount();
		$queryCounter->incrementQueryDuration($query->getDuration());
		
		if($query->isSelect()) {
			$queryCounter->incrementSelectCount();
		} elseif($query->isUpdate()) {
			$queryCounter->incrementUpdateCount();
		} elseif($query->isInsert()) {
			$queryCounter->incrementInsertCount();
		} elseif($query->isDelete()) {
			$queryCounter->incrementDeleteCount();
		}
	}
	
	function & getHourlyStatistics() {
		return $this->hourlyStatistics;
	}
}

?>