<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/Query.class.php');
require_once('../include/ErrorQuery.class.php');

class TestErrorQuery extends UnitTestCase {
	
	function testInstanciation() {
		define('TEST_TEXT', 'test text');
		
		$errorQuery = new ErrorQuery(TEST_TEXT);
		$this->assertFalse($errorQuery->isIgnored());
		$this->assertEqual(TEST_TEXT, $errorQuery->getText());
		$this->assertEqual(TEST_TEXT, $errorQuery->getError());
	}
	
	function testSettersAndGetters() {
		define('TEST_TEXT', 'test text');
		define('TEST_STATEMENT', 'test_statement');
		define('TEST_HINT', 'test_hint');
		define('TEST_DETAIL', 'test_detail');
		
		$errorQuery = new ErrorQuery(TEST_TEXT);
		$errorQuery->appendStatement(TEST_STATEMENT);
		$this->assertEqual(TEST_STATEMENT, $errorQuery->getText());
		
		$errorQuery->appendHint(TEST_HINT);
		$this->assertEqual(TEST_HINT, $errorQuery->getHint());
		
		$errorQuery->appendDetail(TEST_DETAIL);
		$this->assertEqual(TEST_DETAIL, $errorQuery->getDetail());
	}
}

?>