<?php
class PgBenchReport extends Report {
    public function __construct(& $reportAggregator) {
        parent::__construct($reportAggregator, 'Queries history as pg_bench input script', array('QueriesHistoryListener'), false);
    }

    public function getText() {
        $listener =& $this->reportAggregator->getListener('QueriesHistoryListener');
        $text = '';

        $queries = $listener->getQueriesHistory();
        foreach ($queries as $query) {
            $text .= rtrim($query->getText()) . ";\n";
        }
        return $text;
    }

    public function getHtml() {
        $html = '<p>Report not supported by HTML format</p>';

        return $html;
    }
}
