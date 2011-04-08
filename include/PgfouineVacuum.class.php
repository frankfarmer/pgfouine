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

require_once('Command.class.php');
require_once('postgresql/vacuum/vacuum.lib.php');

class PgfouineVacuum extends Command {
    protected function usage($error = false) {
        if($error) {
            stderr('Error: '.$error);
            echo "\n";
        }
        echo 'Usage: '.$this->executable.' -file <file> [-report [outputfile=]<block1,block2>] [-filter <filter>]
    -file <file>                           log file to analyze
    -                                      read the log from stdin instead of -file
    -report [outputfile=]<block1,block2>   list of report blocks separated by a comma
                                         report blocks can be: overall, fsm, vacuumedtables, details
                                         you can add several -report options if you want to generate several reports at once
    -filter <filter>                       filter of the form: database or database.schema
                                         filter is applied on output only
    -title <title>                         define the title of the reports
    -memorylimit <n>                       PHP memory limit in MB. Default is 128.
    -debug                                 debug mode
    -profile                               profile mode
    -help                                  this help
        ';
        if($error) {
            exit(1);
        } else {
            exit(0);
        }
    }

    protected function getMemoryLimitDefault() {
        return 128;
    }

    protected function assignOptions() {
        define('CONFIG_ONLY_SELECT', false);
        define('CONFIG_KEEP_FORMATTING', false);
        define('CONFIG_DURATION_UNIT', 's');
        define('CONFIG_TIMESTAMP_FILTER', false);
        define('CONFIG_DATABASE', false);
        define('CONFIG_USER', false);

        if(isset($options['filter']) && !empty($options['filter'])) {
            define('CONFIG_FILTER', $options['filter']);
        } else {
            define('CONFIG_FILTER', false);
        }

        if(isset($options['title'])) {
            define('CONFIG_REPORT_TITLE', $options['title']);
        } else {
            define('CONFIG_REPORT_TITLE', 'pgFouine: PostgreSQL VACUUM log analysis report');
        }
    }

    protected function setupAggregation($filePath, $value) {
        return array(
            'HtmlReportAggregator',
            new GenericLogReader($filePath, 'PostgreSQLVacuumParser', 'PostgreSQLVacuumAccumulator')
        );
    }

    protected function getSupportedReportBlocks() {
        return array(
            'overall' => 'VacuumOverallReport',
            'vacuumedtables' => 'VacuumedTablesReport',
            'details' => 'VacuumedTablesDetailsReport',
            'fsm' => 'FSMInformationReport'
        );
    }

    protected function getDefaultReportBlocks() {
        return array('fsm', 'overall', 'vacuumedtables', 'details');
    }
}
