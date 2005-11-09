<?php

require_once('PostgreSQLAccumulator.class.php');

// lines
require_once('lines/PostgreSQLLogLine.class.php');
require_once('lines/PostgreSQLDurationLine.class.php');
require_once('lines/PostgreSQLQueryStartLine.class.php');
require_once('lines/PostgreSQLQueryStartWithDurationLine.class.php');
require_once('lines/PostgreSQLContinuationLine.class.php');
require_once('lines/PostgreSQLDetailLine.class.php');
require_once('lines/PostgreSQLContextLine.class.php');
require_once('lines/PostgreSQLStatementLine.class.php');
require_once('lines/PostgreSQLErrorLine.class.php');
require_once('lines/PostgreSQLHintLine.class.php');
require_once('lines/PostgreSQLStatusLine.class.php');

// parsers
require_once('parsers/PostgreSQLParser.class.php');
//require_once('parsers/LogPostgreSQLParser.class.php');
require_once('parsers/SyslogPostgreSQLParser.class.php');

?>