<?php
/*
 * pg_bench script generation, based on postgres slow queries log.
 *
 * Copyright (c) 2011 Denis Orlikhin
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

include('include/LogParsingCommand.class.php');
include('include/reporting/reports/PgBenchReport.class.php');

class PgfouineBench extends LogParsingCommand {
    /**
     * @return ReportAggregator
     */
    protected function getAggregator()
    {
        return 'TextReportAggregator';
    }

    protected function getDefaultReportBlocks()
    {
        return array('content');
    }

    protected function getSupportedReportBlocks()
    {
        return array('content' => 'PgBenchReport');
    }
}
