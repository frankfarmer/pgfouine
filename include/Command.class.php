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

include('../version.php');
require_once('lib/common.lib.php');
require_once('base.lib.php');
require_once('listeners/listeners.lib.php');
require_once('postgresql/postgresql.lib.php');
require_once('reporting/reports.lib.php');

abstract class Command {
    abstract protected function usage($error = false);
    abstract protected function getMemoryLimitDefault();
    abstract protected function assignOptions();
    abstract protected function setupAggregation($filePath, $value);
    abstract protected function getDefaultReportBlocks();
    abstract protected function getSupportedReportBlocks();

    protected $outputToFiles = false;
    protected $executable;

    public function run() {
        global $argv;

        ini_set('max_execution_time', 18000);
        ini_set('log_errors', true);
        ini_set('display_errors', false);

        $stderr = fopen('php://stderr', 'w');

        if(isset($_SERVER['argv']) && (!isset($argv) || empty($argv))) {
            $argv = $_SERVER['argv'];
        }
        if(is_array($argv)) {
            $this->executable = array_shift($argv);
        } else {
            $argv = array();
            $this->executable = 'unknown';
        }

        $options = array();
        $argvCount = count($argv);
        for($i = 0; $i < $argvCount; $i++) {
            if(strpos($argv[$i], '-') === 0) {
                if($argv[$i] == '-') {
                    define('CONFIG_STDIN', true);
                } else {
                    $optionKey = substr($argv[$i], 1);
                    $value = false;
                    if(($i+1 < $argvCount) && (strpos($argv[$i+1], '-') !== 0)) {
                        $value = $argv[$i+1];
                        $i++;
                    }
                    if($optionKey == 'report' || $optionKey == 'reports') {
                        if(!isset($options['reports'])) {
                            $options['reports'] = array();
                        }
                        $options['reports'][] = $value;
                    } else {
                        $options[$optionKey] = $value;
                    }
                }
            } else {
                $this->usage('invalid options format');
            }
        }

        if(isset($options['memorylimit']) && ((int) $options['memorylimit']) > 0) {
            $memoryLimit = (int) $options['memorylimit'];
        } else {
            $memoryLimit = $this->getMemoryLimitDefault();
        }
        ini_set('memory_limit', $memoryLimit.'M');

        if(!defined('CONFIG_STDIN')) {
            define('CONFIG_STDIN', false);
        }

        if(isset($options['help']) || isset($options['h']) || isset($options['-help'])) {
            $this->usage();
        }

        if(isset($options['debug'])) {
            define('DEBUG', 1);
        } else {
            define('DEBUG', 0);
        }
        if(isset($options['profile'])) {
            define('PROFILE', 1);
        } else {
            define('PROFILE', 0);
        }

        if(!CONFIG_STDIN) {
            if(!isset($options['file'])) {
                $this->usage('the -file option is required');
            } elseif(!$options['file']) {
                $this->usage('you have to specify a file path');
            } elseif(!is_readable($options['file'])) {
                $this->usage('file '.$options['file'].' cannot be read');
            } else {
                $filePath = realpath($options['file']);
            }
        } else {
            $filePath = 'php://stdin';
        }

        $this->assignOptions();

        $reports = array();
        if(isset($options['reports'])) {
            foreach($options['reports'] AS $report) {
                if(strpos($report, '=') !== false) {
                    list($outputFilePath, $blocks) = explode('=', $report);
                    $this->outputToFiles = true;
                } elseif(strpos($report, '.') !== false) {
                    $outputFilePath = $report;
                    $blocks = 'default';
                    $this->outputToFiles = true;
                } else {
                    $outputFilePath = false;
                    $blocks = $report;
                    $this->outputToFiles = false;
                }
                if($blocks == 'default') {
                    $selectedBlocks = $this->getDefaultReportBlocks();
                    $notSupportedBlocks = array();
                } elseif($blocks == 'all') {
                    $selectedBlocks = array_keys($this->getSupportedReportBlocks());
                    $notSupportedBlocks = array();
                } else {
                    $selectedBlocks = explode(',', $blocks);
                    $notSupportedBlocks = array_diff($selectedBlocks, array_keys($this->getSupportedReportBlocks()));
                }

                if(empty($notSupportedBlocks)) {
                    $outputFilePath = checkOutputFilePath($outputFilePath);
                    $reports[] = array(
                        'blocks' => $selectedBlocks,
                        'file' => $outputFilePath
                    );
                } else {
                    $this->usage('report types not supported: '.implode(',', $notSupportedBlocks));
                }
            }
        } else {
            $reports[] = array(
                'blocks' => $this->getDefaultReportBlocks(),
                'file' => false
            );
        }

        list($aggregator, $logReader) = $this->setupAggregation($filePath, $value);

        foreach($reports AS $report) {
            $reportAggregator = new $aggregator($logReader, $report['file']);
            foreach($report['blocks'] AS $block) {
                $reportAggregator->addReportBlock($this->getSupportedReportBlock($block));
            }
            $logReader->addReportAggregator($reportAggregator);
            unset($reportAggregator);
        }

        $logReader->parse();
        $logReader->output();

        fclose($stderr);
    }

    protected function checkOutputFilePath($filePath) {
        if(!$filePath) {
            return false;
        }

        $tmpOutputFilePath = $filePath;
        $tmpOutputDirectory = dirname($tmpOutputFilePath);
        $tmpOutputFileName = basename($tmpOutputFilePath);

        if(file_exists($tmpOutputFilePath) && (!is_file($tmpOutputFilePath) || !is_writable($tmpOutputFilePath))) {
            $this->usage($tmpOutputFilePath.' already exists and is not a file or is not writable');
            return false;
        } elseif(!is_dir($tmpOutputDirectory) || !is_writable($tmpOutputDirectory)) {
            $this->usage($tmpOutputDirectory.' is not a directory, does not exist or is not writable');
            return false;
        } elseif(!$tmpOutputFileName) {
            $this->usage('cannot find a valid basename in '.$tmpOutputFilePath);
            return false;
        } else {
            $outputFilePath = realpath($tmpOutputDirectory).'/'.$tmpOutputFileName;
            return $outputFilePath;
        }
    }

    private function getSupportedReportBlock($block)
    {
        $blocks = $this->getSupportedReportBlocks();
        return $blocks[$block];
    }
}
