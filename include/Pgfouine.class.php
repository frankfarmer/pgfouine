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

include('Command.class.php');

class Pgfouine extends Command {
    protected function getUsageInShort() {
        return '[-top <n>] [-format <format>] [-logtype <logtype>] [-report [outputfile=]<block1,block2>]';
    }
    protected function getUsageOptions() {
        return '
    -top <n>                               number of queries in lists. Default is 20.
    -format <format>                       output format: html, html-with-graphs or text. Default is html.
    -logtype <logtype>                     log type: syslog, stderr or csvlog. Default is syslog.
                                          for stderr, you have to use the following log_line_prefix: \'%t [%p]: [%l-1] \'
    -report [outputfile=]<block1,block2>   list of report blocks separated by a comma
                                         report blocks can be: overall, hourly, bytype, slowest, n-mosttime,
                                          n-mostfrequent, n-slowestaverage, history, n-mostfrequenterrors,
                                          tsung, csv-query
                                         you can add several -report options if you want to generate several reports at once
    -examples <n>                          maximum number of examples for a normalized query
    -onlyselect                            ignore all queries but SELECT
    -from "<date>"                         ignore lines logged before this date (uses strtotime)
    -to "<date>"                           ignore lines logged after this date (uses strtotime)
    -database <database>                   consider only queries on this database
                                         (supports db1,db2 and /regexp/)
    -user <user>                           consider only queries executed by this user
                                         (supports user1,user2 and /regexp/)
    -keepformatting                        keep the formatting of the query
    -maxquerylength <length>               maximum length of a query: we cut it if it exceeds this length
    -durationunit <s|ms>                   unit used to display the durations. Default is s(econds).
    -title <title>                         define the title of the reports
    -syslogident <ident>                   PostgreSQL syslog identity. Default is postgres.
        ';
    }

    protected function getMemoryLimitDefault() {
        return 512;
    }

    protected function assignOptions() {
        define('CONFIG_FILTER', false);

        if(isset($options['title'])) {
            define('CONFIG_REPORT_TITLE', $options['title']);
        } else {
            define('CONFIG_REPORT_TITLE', 'pgFouine: PostgreSQL log analysis report');
        }

        if(isset($options['top'])) {
            if((int) $options['top'] > 0) {
                $top = (int) $options['top'];
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

    protected function setupAggregation($filePath, $value) {
        $supportedFormats = array('text' => 'TextReportAggregator', 'html' => 'HtmlReportAggregator', 'html-with-graphs' => 'HtmlWithGraphsReportAggregator');
        if(isset($options['format'])) {
            if(array_key_exists($options['format'], $supportedFormats)) {
                if($options['format'] == 'html-with-graphs') {
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
                $aggregator = $supportedFormats[$options['format']];
            } else {
                $this->usage('format not supported');
            }
        } else {
            $aggregator = $supportedFormats['html'];
        }

        $supportedLogTypes = array(
            'syslog' => 'SyslogPostgreSQLParser',
            'stderr' => 'StderrPostgreSQLParser',
            'csvlog' => 'CsvlogPostgreSQLParser',
        );
        $logtype = '';
        if(isset($options['logtype'])) {
            if(array_key_exists($options['logtype'], $supportedLogTypes)) {
                $parser = $supportedLogTypes[$options['logtype']];
                $logtype = $options['logtype'];
            } else {
                $this->usage('log type not supported');
            }
        } else {
            $parser = $supportedLogTypes['syslog'];
            $logtype = 'syslog';
        }

        if(isset($options['examples'])) {
            $maxExamples = (int) $options['examples'];
        } else {
            $maxExamples = 3;
        }
        define('CONFIG_MAX_NUMBER_OF_EXAMPLES', $maxExamples);

        if(isset($options['onlyselect'])) {
            define('CONFIG_ONLY_SELECT', true);
        } else {
            define('CONFIG_ONLY_SELECT', false);
        }

        if(isset($options['database']) && !empty($options['database'])) {
            $options['database'] = trim($options['database']);
            if(substr($options['database'], 0, 1) == '/' && substr($options['database'], -1, 1) == '/') {
                // the filter is probably a regexp
                if(@preg_match($options['database'], $value) === false) {
                    $this->usage('database filter regexp is not valid');
                } else {
                    define('CONFIG_DATABASE_REGEXP', $options['database']);
                }
            } elseif(strpos($options['database'], ',') !== false) {
                // the filter is a list
                $databases = explode(',', $options['database']);
                $databases = array_map('trim', $databases);
                define('CONFIG_DATABASE_LIST', implode(',', $databases));
            } else {
                define('CONFIG_DATABASE', $options['database']);
            }
        }
        if(!defined('CONFIG_DATABASE')) define('CONFIG_DATABASE', false);
        if(!defined('CONFIG_DATABASE_LIST')) define('CONFIG_DATABASE_LIST', false);
        if(!defined('CONFIG_DATABASE_REGEXP')) define('CONFIG_DATABASE_REGEXP', false);

        if(isset($options['user']) && !empty($options['user'])) {
            $options['user'] = trim($options['user']);
            if(substr($options['user'], 0, 1) == '/' && substr($options['user'], -1, 1) == '/') {
                // the filter is probably a regexp
                if(@preg_match($options['user'], $value) === false) {
                    $this->usage('user filter regexp is not valid');
                } else {
                    define('CONFIG_USER_REGEXP', $options['user']);
                }
            } elseif(strpos($options['user'], ',') !== false) {
                // the filter is a list
                $users = explode(',', $options['user']);
                $users = array_map('trim', $users);
                define('CONFIG_USER_LIST', implode(',', $users));
            } else {
                define('CONFIG_USER', $options['user']);
            }
        }
        if(!defined('CONFIG_USER')) define('CONFIG_USER', false);
        if(!defined('CONFIG_USER_LIST')) define('CONFIG_USER_LIST', false);
        if(!defined('CONFIG_USER_REGEXP')) define('CONFIG_USER_REGEXP', false);

        if(isset($options['keepformatting'])) {
            define('CONFIG_KEEP_FORMATTING', true);
        } else {
            define('CONFIG_KEEP_FORMATTING', false);
        }

        if(isset($options['maxquerylength']) && is_numeric($options['maxquerylength'])) {
            define('CONFIG_MAX_QUERY_LENGTH', $options['maxquerylength']);
        } else {
            define('CONFIG_MAX_QUERY_LENGTH', 0);
        }

        if(isset($options['durationunit']) && $options['durationunit'] == 'ms') {
            define('CONFIG_DURATION_UNIT', 'ms');
        } else {
            define('CONFIG_DURATION_UNIT', 's');
        }

        if(isset($options['from']) && !empty($options['from'])) {
            $fromTimestamp = strtotime($options['from']);
            if($fromTimestamp <= 0) {
                $fromTimestamp = false;
            }
        } else {
            $fromTimestamp = false;
        }

        if(isset($options['to']) && !empty($options['to'])) {
            $toTimestamp = strtotime($options['to']);
            if($toTimestamp <= 0) {
                $toTimestamp = false;
            }
        } else {
            $toTimestamp = false;
        }

        if($fromTimestamp || $toTimestamp) {
            define('CONFIG_TIMESTAMP_FILTER', true);
            if($fromTimestamp) {
                define('CONFIG_FROM_TIMESTAMP', $fromTimestamp);
            } else {
                define('CONFIG_FROM_TIMESTAMP', MIN_TIMESTAMP);
            }
            if($toTimestamp) {
                define('CONFIG_TO_TIMESTAMP', $toTimestamp);
            } else {
                define('CONFIG_TO_TIMESTAMP', MAX_TIMESTAMP);
            }
        } else {
            define('CONFIG_TIMESTAMP_FILTER', false);
        }

        if(isset($options['syslogident'])) {
            define('CONFIG_SYSLOG_IDENTITY', $options['syslogident']);
        } else {
            define('CONFIG_SYSLOG_IDENTITY', 'postgres');
        }

        if(isset($options['quiet'])) {
            define('CONFIG_QUIET', true);
        } else {
            define('CONFIG_QUIET', false);
        }

        if($logtype == 'csvlog') {
            $logReader = new CsvlogLogReader($filePath, $parser, 'PostgreSQLAccumulator');
        } else {
            $logReader = new GenericLogReader($filePath, $parser, 'PostgreSQLAccumulator');
        }

        return array($aggregator, $logReader);
    }
}
