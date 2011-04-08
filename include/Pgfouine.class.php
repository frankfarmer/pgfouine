<?php
/*
 * This file is part of pgFouine.
 *
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005-2008 Guillaume Smet
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

include('LogParsingCommand.class.php');

class Pgfouine extends LogParsingCommand {
    protected function getUsageInShort() {
        return '[-top <n>] [-format <format>] [-logtype <logtype>] [-report [outputfile=]<block1,block2>]';
    }

    protected function getUsageOptions() {
        return parent::getUsageOptions() . '
    -top <n>                               number of queries in lists. Default is 20.
    -format <format>                       output format: html, html-with-graphs or text. Default is html.
    -report [outputfile=]<block1,block2>   list of report blocks separated by a comma
                                         report blocks can be: overall, hourly, bytype, slowest, n-mosttime,
                                          n-mostfrequent, n-slowestaverage, history, n-mostfrequenterrors,
                                          tsung, csv-query
                                         you can add several -report options if you want to generate several reports at once
    -title <title>                         define the title of the reports
        ';
    }

    protected function assignOptions() {
        parent::assignOptions();

        if(isset($this->options['title'])) {
            define('CONFIG_REPORT_TITLE', $this->options['title']);
        } else {
            define('CONFIG_REPORT_TITLE', 'pgFouine: PostgreSQL log analysis report');
        }

        if(isset($this->options['top'])) {
            if((int) $this->options['top'] > 0) {
                $top = (int) $this->options['top'];
            } else {
                $this->usage('top option should be a valid integer');
            }
        } else {
            $top = 20;
        }
        define('CONFIG_TOP_QUERIES_NUMBER', $top);
    }

    protected function getSupportedReportBlocks() {
        return array(
            'overall' => 'OverallStatsReport',
            'bytype' => 'QueriesByTypeReport',
            'hourly' => 'HourlyStatsReport',
            'slowest' => 'SlowestQueriesReport',
            'n-mosttime' => 'NormalizedQueriesMostTimeReport',
            'n-mostfrequent' => 'NormalizedQueriesMostFrequentReport',
            'n-slowestaverage' => 'NormalizedQueriesSlowestAverageReport',
            'history' => 'QueriesHistoryReport',
            'historyperpid' => 'QueriesHistoryPerPidReport',
            'n-mostfrequenterrors' => 'NormalizedErrorsMostFrequentReport',
            'tsung' => 'TsungSessionsReport',
            'csv-query' => 'CsvQueriesHistoryReport'
        );
    }

    protected function getDefaultReportBlocks() {
        return array('overall', 'bytype', 'n-mosttime', 'slowest', 'n-mostfrequent', 'n-slowestaverage');
    }

    /**
     * @param  $filePath
     * @param  $value
     * @return ReportAggregator
     */
    protected function getAggregator() {
        $supportedFormats = array('text' => 'TextReportAggregator', 'html' => 'HtmlReportAggregator', 'html-with-graphs' => 'HtmlWithGraphsReportAggregator');
        if(isset($this->options['format'])) {
            if(array_key_exists($this->options['format'], $supportedFormats)) {
                if($this->options['format'] == 'html-with-graphs') {
                    if(!function_exists('imagegd2')) {
                        $this->usage('HTML with graphs format requires GD2 library and extension');
                    }
                    if(!function_exists('imagettfbbox')) {
                        $this->usage('HTML with graphs format requires Freetype support');
                    }
                    if(!$this->outputToFiles) {
                        $this->usage('you need to define an output file to use HTML with graphs format (use -report outputfile=block1,block2,...)');
                    }
                }
                $aggregator = $supportedFormats[$this->options['format']];
            } else {
                $this->usage('format not supported');
            }
        } else {
            $aggregator = $supportedFormats['html'];
        }
        return $aggregator;
    }
}
