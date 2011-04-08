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

abstract class LogParsingCommand extends Command {
    protected function getUsageOptions() {
        return '
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
    -syslogident <ident>                   PostgreSQL syslog identity. Default is postgres.
    -durationunit <s|ms>                   unit used to display the durations. Default is s(econds).
        ';
    }

    protected function getMemoryLimitDefault() {
        return 512;
    }

    protected function assignOptions() {
        define('CONFIG_FILTER', false);
    }

    /**
     * @return GenericLogReader
     */
    protected function getLogReader($filePath, $value) {
        $supportedLogTypes = array(
            'syslog' => 'SyslogPostgreSQLParser',
            'stderr' => 'StderrPostgreSQLParser',
            'csvlog' => 'CsvlogPostgreSQLParser',
        );
        $logtype = '';
        if(isset($this->options['logtype'])) {
            if(array_key_exists($this->options['logtype'], $supportedLogTypes)) {
                $parser = $supportedLogTypes[$this->options['logtype']];
                $logtype = $this->options['logtype'];
            } else {
                $this->usage('log type not supported');
            }
        } else {
            $parser = $supportedLogTypes['syslog'];
            $logtype = 'syslog';
        }

        if(isset($this->options['examples'])) {
            $maxExamples = (int) $this->options['examples'];
        } else {
            $maxExamples = 3;
        }
        define('CONFIG_MAX_NUMBER_OF_EXAMPLES', $maxExamples);

        if(isset($this->options['onlyselect'])) {
            define('CONFIG_ONLY_SELECT', true);
        } else {
            define('CONFIG_ONLY_SELECT', false);
        }

        if(isset($this->options['database']) && !empty($this->options['database'])) {
            $this->options['database'] = trim($this->options['database']);
            if(substr($this->options['database'], 0, 1) == '/' && substr($this->options['database'], -1, 1) == '/') {
                // the filter is probably a regexp
                if(@preg_match($this->options['database'], $value) === false) {
                    $this->usage('database filter regexp is not valid');
                } else {
                    define('CONFIG_DATABASE_REGEXP', $this->options['database']);
                }
            } elseif(strpos($this->options['database'], ',') !== false) {
                // the filter is a list
                $databases = explode(',', $this->options['database']);
                $databases = array_map('trim', $databases);
                define('CONFIG_DATABASE_LIST', implode(',', $databases));
            } else {
                define('CONFIG_DATABASE', $this->options['database']);
            }
        }
        if(!defined('CONFIG_DATABASE')) define('CONFIG_DATABASE', false);
        if(!defined('CONFIG_DATABASE_LIST')) define('CONFIG_DATABASE_LIST', false);
        if(!defined('CONFIG_DATABASE_REGEXP')) define('CONFIG_DATABASE_REGEXP', false);

        if(isset($this->options['user']) && !empty($this->options['user'])) {
            $this->options['user'] = trim($this->options['user']);
            if(substr($this->options['user'], 0, 1) == '/' && substr($this->options['user'], -1, 1) == '/') {
                // the filter is probably a regexp
                if(@preg_match($this->options['user'], $value) === false) {
                    $this->usage('user filter regexp is not valid');
                } else {
                    define('CONFIG_USER_REGEXP', $this->options['user']);
                }
            } elseif(strpos($this->options['user'], ',') !== false) {
                // the filter is a list
                $users = explode(',', $this->options['user']);
                $users = array_map('trim', $users);
                define('CONFIG_USER_LIST', implode(',', $users));
            } else {
                define('CONFIG_USER', $this->options['user']);
            }
        }
        if(!defined('CONFIG_USER')) define('CONFIG_USER', false);
        if(!defined('CONFIG_USER_LIST')) define('CONFIG_USER_LIST', false);
        if(!defined('CONFIG_USER_REGEXP')) define('CONFIG_USER_REGEXP', false);

        if(isset($this->options['keepformatting'])) {
            define('CONFIG_KEEP_FORMATTING', true);
        } else {
            define('CONFIG_KEEP_FORMATTING', false);
        }

        if(isset($this->options['maxquerylength']) && is_numeric($this->options['maxquerylength'])) {
            define('CONFIG_MAX_QUERY_LENGTH', $this->options['maxquerylength']);
        } else {
            define('CONFIG_MAX_QUERY_LENGTH', 0);
        }

        if(isset($this->options['durationunit']) && $this->options['durationunit'] == 'ms') {
            define('CONFIG_DURATION_UNIT', 'ms');
        } else {
            define('CONFIG_DURATION_UNIT', 's');
        }

        if(isset($this->options['from']) && !empty($this->options['from'])) {
            $fromTimestamp = strtotime($this->options['from']);
            if($fromTimestamp <= 0) {
                $fromTimestamp = false;
            }
        } else {
            $fromTimestamp = false;
        }

        if(isset($this->options['to']) && !empty($this->options['to'])) {
            $toTimestamp = strtotime($this->options['to']);
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

        if(isset($this->options['syslogident'])) {
            define('CONFIG_SYSLOG_IDENTITY', $this->options['syslogident']);
        } else {
            define('CONFIG_SYSLOG_IDENTITY', 'postgres');
        }

        if(isset($this->options['quiet'])) {
            define('CONFIG_QUIET', true);
        } else {
            define('CONFIG_QUIET', false);
        }

        if($logtype == 'csvlog') {
            $logReader = new CsvlogLogReader($filePath, $parser, 'PostgreSQLAccumulator');
        } else {
            $logReader = new GenericLogReader($filePath, $parser, 'PostgreSQLAccumulator');
        }

        return $logReader;
    }
}
