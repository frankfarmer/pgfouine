<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/LogStream.class.php');
require_once('../include/Query.class.php');

define('LOG_STREAM_HOST', 'test_host');
define('LOG_STREAM_PORT', '30123');
define('LOG_STREAM_USER', 'test_user');
define('LOG_STREAM_DB', 'test_db');

class TestLogStream extends UnitTestCase {
	
	function testSetHostConnection() {
		$logStream = new LogStream();
		$logStream->setHostConnection(LOG_STREAM_HOST, LOG_STREAM_PORT);
		$this->assertEqual(LOG_STREAM_HOST, $logStream->getHost());
		$this->assertEqual(LOG_STREAM_PORT, $logStream->getPort());
	}
	
	function testSetUserDb() {
		$logStream = new LogStream();
		$logStream->setUserDb(LOG_STREAM_USER, LOG_STREAM_DB);
		$this->assertEqual(LOG_STREAM_USER, $logStream->getUser());
		$this->assertEqual(LOG_STREAM_DB, $logStream->getDb());
	}
	
	function testGotDuration() {
		$logStream = new LogStream();
		$this->assertFalse($logStream->hasDurationInfo());
		$logStream->gotDuration();
		$this->assertTrue($logStream->hasDurationInfo());
	}
	
	function testGetQueriesAndPush() {
		$query1 = new Query('');
		$query2 = new Query('');
		$logStream = new LogStream();
		
		$this->assertEqual(0, count($logStream->getQueries()));
		
		$logStream->push($query1);
		$queries =& $logStream->getQueries();
		
		$this->assertEqual(1, count($queries));
		$this->assertEqual('Unknown', $queries[0]->getDb());
		$this->assertEqual('Unknown', $queries[0]->getUser());
		$this->assertReference($query1, $queries[0]);
		
		$logStream->push($query2);
		$queries =& $logStream->getQueries();
		
		$this->assertEqual(2, count($queries));
		$this->assertEqual('Unknown', $queries[0]->getDb());
		$this->assertEqual('Unknown', $queries[0]->getUser());
		$this->assertEqual('Unknown', $queries[1]->getDb());
		$this->assertEqual('Unknown', $queries[1]->getUser());
		$this->assertReference($query1, $queries[0]);
		$this->assertReference($query2, $queries[1]);
	}
	
	function testPop() {
		$query1 = new Query('');
		$query2 = new Query('');
		$logStream = new LogStream();
		
		$logStream->push($query1);
		$logStream->push($query2);
		
		$queries =& $logStream->getQueries();
		$this->assertEqual(2, count($queries));
		
		$poppedQuery =& $logStream->pop();
		$this->assertReference($query2, $poppedQuery);
		$this->assertEqual(1, count($queries));
		
		$queries =& $logStream->getQueries();
		$this->assertReference($query1, $queries[0]);
	}
	
	function testLast() {
		$query1 = new Query('');
		$query2 = new Query('');
		$logStream = new LogStream();
		
		$logStream->push($query1);
		$logStream->push($query2);
		
		$queries =& $logStream->getQueries();
		$this->assertEqual(2, count($queries));
		
		$lastQuery =& $logStream->last();
		$this->assertReference($query2, $lastQuery);
		
		$queries =& $logStream->getQueries();
		$this->assertEqual(2, count($queries));
	}
	
	function testAppend() {
	}
}

?>