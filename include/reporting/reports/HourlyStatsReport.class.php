<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005-2006 Guillaume Smet
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

class HourlyStatsReport extends Report {
	function HourlyStatsReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Hourly statistics', array('HourlyCountersListener'));
	}
	
	function getText() {
		$statsListener =& $this->reportAggregator->getListener('HourlyCountersListener');
		
		$text = '';
		
		// TODO

		return $text;
	}
	
	function getHtml() {
		$statsListener =& $this->reportAggregator->getListener('HourlyCountersListener');
				
		$html = '';
		
		// TODO
		
		stderrArray($statsListener->getQueryPeaksStatistics());
		
		$html .= '<pre>';
		$html .= getFormattedArray($statsListener->getQueryPeaksStatistics());
		$html .= '</pre>';
		
		return $html;
	}

	function getHtmlWithGraphs() {
		$statsListener =& $this->reportAggregator->getListener('HourlyCountersListener');

		$this->generateGraphs($statsListener);

		$html = '';
		$html .= '<p><img src="'.$this->reportAggregator->getImageBaseName('hourly_queries_per_second').'" alt="Queries per second" /></p>';
		$html .= '<p><img src="'.$this->reportAggregator->getImageBaseName('hourly_all_queries').'" alt="Hourly queries" /></p>';
		$html .= '<p><img src="'.$this->reportAggregator->getImageBaseName('hourly_select_queries').'" alt="Hourly SELECT queries" /></p>';
		$html .= '<p><img src="'.$this->reportAggregator->getImageBaseName('hourly_write_queries').'" alt="Hourly write queries" /></p>';

		return $html;
	}
	
	function generateGraphs(& $statsListener) {
		$hourlyStatistics =& $statsListener->getHourlyStatistics();
		$hours = array_keys($hourlyStatistics);
		$hourCount = count($hours);
		
		$hoursAxis = array();
		$queryCountValues = array();
		$queryDurationValues = array();
		$selectCountValues = array();
		$selectDurationValues = array();
		$insertCountValues = array();
		$deleteCountValues = array();
		$updateCountValues = array();
		$globalCountValues = array();
		$writeDurationValues = array();
		
		$currentDay = 0;
		for($i = 0; $i < $hourCount; $i++) {
			$hour = $hours[$i];
			$hourTimestamp = strtotime($hour);
			
			$counter =& $hourlyStatistics[$hour];
			if($hourCount <= 25 || (date('G', $hourTimestamp) % 6 == 0)) {
				if($i == 0 || date('G', $hourTimestamp) == 0) {
					$hoursAxis[] = date("ga\nM j", $hourTimestamp);
				} else {
					$hoursAxis[] = date("ga", $hourTimestamp);
				}
			} else {
				$hoursAxis[] = '';
			}
			$queryCountValues[] = $counter->getQueryCount();
			if($counter->getQueryCount() > 0) {
				$queryDurationValues[] = $counter->getQueryDuration() / $counter->getQueryCount();
			} else {
				$queryDurationValues[] = NULL;
			}
			$selectCountValues[] = $counter->getSelectCount();
			if($counter->getSelectCount() > 0) {
				$selectDurationValues[] = $counter->getSelectDuration() / $counter->getSelectCount();
			} else {
				$selectDurationValues[] = NULL;
			}
			$insertCountValues[] = $counter->getInsertCount();
			$deleteCountValues[] = $counter->getDeleteCount();
			$updateCountValues[] = $counter->getUpdateCount();
			
			$writeCount = $counter->getInsertCount() + $counter->getDeleteCount() + $counter->getUpdateCount();
			
			if($writeCount > 0) {
				$writeDurationValues[] = ($counter->getInsertDuration() + $counter->getDeleteDuration() + $counter->getUpdateDuration()) / $writeCount;
			} else {
				$writeDurationValues[] = NULL;
			}
			unset($counter);
		}
		
		$peaksStatistics =& $statsListener->getQueryPeaksStatistics();
		$currentTimestamp = key($peaksStatistics) - (key($peaksStatistics) % 3600);
		$lastTimestamp = max(array_keys($peaksStatistics));
		$firstData = false;
		
		$xAxis = array();
		$average = array();
		$min = array();
		$max = array();

		while($currentTimestamp <= $lastTimestamp) {
			if($currentTimestamp % 3600 == 0) {
				$xAxis[] = date('ga', $currentTimestamp);
			} else {
				$xAxis[] = '';
			}
			if(isset($peaksStatistics[$currentTimestamp])) {
				$firstData = true;
				$average[] = array_sum($peaksStatistics[$currentTimestamp])/(5*60);
				$min[] = min($peaksStatistics[$currentTimestamp]);
				$max[] = max($peaksStatistics[$currentTimestamp]);
			} elseif($firstData) {
				$average[] = 0;
				$min[] = 0;
				$max[] = 0;
			} else {
				$average[] = null;
				$min[] = null;
				$max[] = null;
			}
			$currentTimestamp += 5 * 60;
		}
		/*
		$currentTimestamp = time();
		$currentTimestamp = $currentTimestamp - ($currentTimestamp % 3600);
		$lastTimestamp = $currentTimestamp + 24 * 3600;
		$firstData = false;
		
		while($currentTimestamp <= $lastTimestamp) {
			if($currentTimestamp % 3600 == 0) {
				$xAxis[] = date('ga', $currentTimestamp);
			} else {
				$xAxis[] = '';
			}
			if(true) {
				$firstData = true;
				$average[] = rand(1,100);
				$min[] = rand(1,100);
				$max[] = rand(1,100);
			} elseif($firstData) {
				$average[] = 0;
				$min[] = 0;
				$max[] = 0;
			} else {
				$average[] = null;
				$min[] = null;
				$max[] = null;
			}
			$currentTimestamp += 5 * 60;
		}
		*/
		// Queries per second
		$graph = new Graph(840, 250);
		$graph->setAntiAliasing(true);
		$graph->setBackgroundColor(new Color(0xFF, 0xFF, 0xFF));
		
		$graph->title->set('Queries per second (5 minutes average)');
		$graph->title->setPadding(30, 30, 2, 2);
		$graph->title->setFont(new Vera(8));
		$graph->title->setColor(new Color(0x00, 0x00, 0x00));
		$graph->title->setBackgroundColor(new Color(0xFE, 0xE3, 0xC4));
		$graph->title->border->show();
		$graph->title->border->setColor(new Color(0xFF, 0xB4, 0x62));
		
		$group = new PlotGroup();
		$group->setSize(0.82, 1);
		$group->setCenter(0.41, 0.5);
		$group->setPadding(40, 40, 30, 27);
		$group->setSpace(1, 1);
		
		$group->grid->setColor(new Color(0xC4, 0xC4, 0xC4));
		$group->grid->setType(LINE_DASHED);
		$group->grid->setInterval(1, 12);
		$group->grid->setBackgroundColor(new White);
		
		$group->axis->left->label->setFont(new Font2);
		$group->axis->left->label->setCallbackFormatFunction(array(&$this, 'formatQueryNumber'));

		$group->axis->bottom->setLabelText($xAxis);
		$group->axis->bottom->label->setFont(new Font1);
		$group->axis->bottom->setTickInterval(12);
		
		$group->legend->setAlign(LEGEND_RIGHT, LEGEND_BOTTOM);
		$group->legend->setPosition(1.21, 0.88);
		$group->legend->setTextFont(new Vera(8));
		$group->legend->setSpace(10);

		$plot = new LinePlot($max);
		$plot->setColor(new MidRed);
		$plot->setFillColor(new LightRed(80));
		$group->legend->add($plot, 'Maximum', LEGEND_MARK);
		$group->add($plot);
		
		$plot = new LinePlot($average);
		$plot->setColor(new Orange());
		$plot->setFillColor(new LightOrange(80));
		$group->legend->add($plot, 'Average', LEGEND_MARK);
		$group->add($plot);
		
		$plot = new LinePlot($min);
		$plot->setColor(new DarkGreen);
		$plot->setFillColor(new LightGreen(80));
		$group->legend->add($plot, 'Minimum', LEGEND_MARK);
		$group->add($plot);
		
		$graph->add($group);
		$graph->draw($this->reportAggregator->getImagePath('hourly_queries_per_second'));
		
		// All queries
		$graph = new Graph(840, 250);
		$graph->setAntiAliasing(true);
		$graph->setBackgroundColor(new Color(0xFF, 0xFF, 0xFF));
		
		$graph->title->set('All queries');
		$graph->title->setPadding(30, 30, 2, 2);
		$graph->title->setFont(new Vera(8));
		$graph->title->setColor(new Color(0x00, 0x00, 0x00));
		$graph->title->setBackgroundColor(new Color(0xFE, 0xE3, 0xC4));
		$graph->title->border->show();
		$graph->title->border->setColor(new Color(0xFF, 0xB4, 0x62));
		
		$group = new PlotGroup();
		$group->setSize(0.82, 1);
		$group->setCenter(0.41, 0.5);
		$group->setPadding(40, 40, 30, 27);
		$group->setSpace(1, 1);
		
		$group->grid->setColor(new Color(0xC4, 0xC4, 0xC4));
		$group->grid->setType(LINE_DASHED);
		$group->grid->setBackgroundColor(new White);
		
		$group->axis->left->setColor(new MidRed);
		$group->axis->left->label->setFont(new Font2);
		$group->axis->left->label->setCallbackFormatFunction(array(&$this, 'formatQueryNumber'));
		
		$group->axis->right->setColor(new DarkGreen);
		$group->axis->right->label->setFont(new Font2);

		$group->axis->bottom->setLabelText($hoursAxis);
		$group->axis->bottom->label->setFont(new Font1);
		
		$group->legend->setAlign(LEGEND_RIGHT, LEGEND_BOTTOM);
		$group->legend->setPosition(1.21, 0.88);
		$group->legend->setTextFont(new Vera(8));
		$group->legend->setSpace(10);
		
		$plot = new LinePlot($queryCountValues, LINEPLOT_MIDDLE);
		$plot->setColor(new Orange());
		$plot->setFillColor(new LightOrange(80));

		$plot->mark->setType(MARK_CIRCLE);
		$plot->mark->setFill(new MidRed);
		if($hourCount <= 24) {
			$plot->mark->setSize(6);
		} else {
			$plot->mark->setSize(2);
		}

		$group->legend->add($plot, 'Number of queries', LEGEND_MARK);
		$group->add($plot);
		
		$plot = new LinePlot($queryDurationValues, LINEPLOT_MIDDLE);
		$plot->setColor(new Color(120, 120, 30, 10));
		$plot->setFillColor(new Color(120, 120, 60, 90));
		
		$plot->mark->setType(MARK_SQUARE);
		$plot->mark->setFill(new DarkGreen);
		if($hourCount <= 24) {
			$plot->mark->setSize(5);
		} else {
			$plot->mark->setSize(2);
		}
		
		$plot->setYAxis(PLOT_RIGHT);
		$plot->setYMax(max($selectDurationValues));
		
		$group->legend->add($plot, 'Average duration (s)', LEGEND_MARK);
		$group->add($plot);
		
		$graph->add($group);
		$graph->draw($this->reportAggregator->getImagePath('hourly_all_queries'));
		
		// SELECT queries
		$graph = new Graph(840, 250);
		$graph->setAntiAliasing(true);
		$graph->setBackgroundColor(new Color(0xFF, 0xFF, 0xFF));
		
		$graph->title->set('SELECT queries');
		$graph->title->setPadding(30, 30, 2, 2);
		$graph->title->setFont(new Vera(8));
		$graph->title->setColor(new Color(0x00, 0x00, 0x00));
		$graph->title->setBackgroundColor(new Color(0xFE, 0xE3, 0xC4));
		$graph->title->border->show();
		$graph->title->border->setColor(new Color(0xFF, 0xB4, 0x62));
		
		$group = new PlotGroup();
		$group->setSize(0.82, 1);
		$group->setCenter(0.41, 0.5);
		$group->setPadding(40, 40, 30, 27);
		$group->setSpace(1, 1);
		
		$group->grid->setColor(new Color(0xC4, 0xC4, 0xC4));
		$group->grid->setType(LINE_DASHED);
		$group->grid->setBackgroundColor(new White);
		
		$group->axis->left->setColor(new MidRed);
		$group->axis->left->label->setFont(new Font2);
		
		$group->axis->right->setColor(new DarkGreen);
		$group->axis->right->label->setFont(new Font2);

		$group->axis->bottom->setLabelText($hoursAxis);
		$group->axis->bottom->label->setFont(new Font1);
		
		$group->legend->setAlign(LEGEND_RIGHT, LEGEND_BOTTOM);
		$group->legend->setPosition(1.21, 0.88);
		$group->legend->setTextFont(new Vera(8));
		$group->legend->setSpace(10);
		
		$plot = new LinePlot($selectCountValues, LINEPLOT_MIDDLE);
		$plot->setColor(new Orange());
		$plot->setFillColor(new LightOrange(80));

		$plot->mark->setType(MARK_CIRCLE);
		$plot->mark->setFill(new MidRed);
		if($hourCount <= 24) {
			$plot->mark->setSize(6);
		} else {
			$plot->mark->setSize(2);
		}

		$group->legend->add($plot, 'Number of queries', LEGEND_MARK);
		$group->add($plot);
		
		$plot = new LinePlot($selectDurationValues, LINEPLOT_MIDDLE);
		$plot->setColor(new Color(120, 120, 30, 10));
		$plot->setFillColor(new Color(120, 120, 60, 90));
		
		$plot->mark->setType(MARK_SQUARE);
		$plot->mark->setFill(new DarkGreen);
		if($hourCount <= 24) {
			$plot->mark->setSize(5);
		} else {
			$plot->mark->setSize(2);
		}
		
		$plot->setYAxis(PLOT_RIGHT);
		$plot->setYMax(max($selectDurationValues));
		
		$group->legend->add($plot, 'Average duration (s)', LEGEND_MARK);
		$group->add($plot);
		
		$graph->add($group);
		$graph->draw($this->reportAggregator->getImagePath('hourly_select_queries'));
		
		// write queries
		$graph = new Graph(840, 250);
		$graph->setAntiAliasing(true);
		$graph->setBackgroundColor(new Color(0xFF, 0xFF, 0xFF));
		
		$graph->title->set('Write queries');
		$graph->title->setPadding(30, 30, 2, 2);
		$graph->title->setFont(new Vera(8));
		$graph->title->setColor(new Color(0x00, 0x00, 0x00));
		$graph->title->setBackgroundColor(new Color(0xFE, 0xE3, 0xC4));
		$graph->title->border->show();
		$graph->title->border->setColor(new Color(0xFF, 0xB4, 0x62));
		
		$group = new PlotGroup();
		$group->setSize(0.82, 1);
		$group->setCenter(0.41, 0.5);
		$group->setPadding(40, 40, 30, 27);
		$group->setSpace(1, 1);
		
		$group->grid->setColor(new Color(0xC4, 0xC4, 0xC4));
		$group->grid->setType(LINE_DASHED);
		$group->grid->setBackgroundColor(new White);
		
		$group->axis->left->label->setFont(new Font2);
		
		$group->axis->right->setColor(new DarkGreen);
		$group->axis->right->label->setFont(new Font2);
		
		$group->axis->bottom->setLabelText($hoursAxis);
		$group->axis->bottom->label->setFont(new Font1);
		
		$group->legend->setAlign(LEGEND_RIGHT, LEGEND_BOTTOM);
		$group->legend->setPosition(1.21, 0.88);
		$group->legend->setTextFont(new Vera(8));
		$group->legend->setSpace(10);
		
		$plot1 = $updateCountValues;
		$plot2 = arrayAdd($updateCountValues, $insertCountValues);
		$plot3 = arrayAdd($plot2, $deleteCountValues);
		
		$plot = new BarPlot($plot3);
		$plot->setBarColor(new Color(180, 80, 80));
		$plot->setBarPadding(0.10, 0.10);
		
		$group->legend->add($plot, 'DELETE queries', LEGEND_BACKGROUND);
		$group->add($plot);
		
		$plot = new BarPlot($plot2);
		$plot->setBarColor(new Color(0xEB, 0xF0, 0xFC));
		$plot->setBarPadding(0.10, 0.10);

		$group->legend->add($plot, 'INSERT queries', LEGEND_BACKGROUND);
		$group->add($plot);
		
		$plot = new BarPlot($plot1);
		$plot->setBarColor(new Color(0xFE, 0xE3, 0xC4));
		$plot->setBarPadding(0.10, 0.10);
		
		$group->legend->add($plot, 'UPDATE queries', LEGEND_BACKGROUND);
		$group->add($plot);
		
		$plot = new LinePlot($writeDurationValues, LINEPLOT_MIDDLE);
		$plot->setColor(new Color(120, 120, 30, 10));
		$plot->setFillColor(new Color(120, 120, 60, 90));
		
		$plot->mark->setType(MARK_SQUARE);
		$plot->mark->setFill(new DarkGreen);
		if($hourCount <= 24) {
			$plot->mark->setSize(5);
		} else {
			$plot->mark->setSize(2);
		}
		
		$plot->setYAxis(PLOT_RIGHT);
		
		$group->legend->add($plot, 'Average duration (s)', LEGEND_MARK);
		$group->add($plot);
		
		$graph->add($group);
		$graph->draw($this->reportAggregator->getImagePath('hourly_write_queries'));
	}
	
	function formatQueryNumber($queryNumber) {
		if($queryNumber % 1000 == 0 && $queryNumber > 9999) {
			$queryNumber = ($queryNumber / 1000).'k';
		}
		return $queryNumber;
	}
}

?>