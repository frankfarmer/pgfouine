<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/Query.class.php');

class TestQuery extends UnitTestCase {
	
	function testInstanciation() {
		define('TEST_TEXT', 'test text');
		
		$query = new Query(TEST_TEXT);
		$this->assertFalse($query->isIgnored());
		
		$query = new Query(TEST_TEXT, true);
		$this->assertTrue($query->isIgnored());
		$this->assertEqual(TEST_TEXT, $query->getText());
		
		$query = new Query(TEST_TEXT, false);
		$this->assertFalse($query->isIgnored());
	}
	
	function testSettersAndGetters() {
		define('TEST_TEXT', 'test text');
		
		$query = new Query(TEST_TEXT);
		
		define('TEST_DB', 'test_db');
		define('TEST_USER', 'test_user');
		define('TEST_DURATION', 100);
		define('TEST_COMMAND_NUMBER', 43);
		
		$query = new Query(TEST_TEXT);
		$query->setDb(TEST_DB);
		$this->assertEqual(TEST_DB, $query->getDb());
		
		$query = new Query(TEST_TEXT);
		$query->setUser(TEST_USER);
		$this->assertEqual(TEST_USER, $query->getUser());
		
		$query = new Query(TEST_TEXT);
		$query->setDuration(TEST_DURATION);
		$this->assertEqual(TEST_DURATION, $query->getDuration());
		
		$query = new Query(TEST_TEXT);
		$query->setCommandNumber(TEST_COMMAND_NUMBER);
		$this->assertEqual(TEST_COMMAND_NUMBER, $query->getCommandNumber());
	}
	
	function testTypeDetection() {
		$query = new Query('select * from mytable');
		$this->assertTrue($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new Query('SELECT * FROM mytable');
		$this->assertTrue($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new Query('delete from mytable');
		$this->assertFalse($query->isSelect());
		$this->assertTrue($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new Query('DELETE FROM mytable');
		$this->assertFalse($query->isSelect());
		$this->assertTrue($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new Query('insert into mytable values(4)');
		$this->assertFalse($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertTrue($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new Query('INSERT INTO mytable VALUES(4)');
		$this->assertFalse($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertTrue($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new Query('update mytable set field=4');
		$this->assertFalse($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertTrue($query->isUpdate());
		
		$query = new Query('UPDATE mytable SET field=4');
		$this->assertFalse($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertTrue($query->isUpdate());
	}
	
	function testNormalize() {
		define('TEST_QUERY', "SELECT * FROM   mytable WHERE field1=4 AND field2='string'");
		$query = new Query(TEST_QUERY, false);
		$this->assertEqual(TEST_QUERY, $query->getText());
		$this->assertEqual("SELECT * FROM mytable WHERE field1=0 AND field2=''", $query->getNormalizedText());
	}
	
	function testAppend() {
		define('TEST_TEXT1', 'test text 1');
		define('TEST_TEXT2', 'test text 2');
		
		$query = new Query(TEST_TEXT1);
		$query->append(TEST_TEXT2);
		$this->assertEqual(TEST_TEXT1.' '.TEST_TEXT2, $query->getText());
	}
	
	function testSubQuery() {
		define('TEST_TEXT1', 'test text 1');
		define('TEST_TEXT2', 'test text 2');
		define('TEST_TEXT3', 'test text 3');
		define('TEST_TEXT4', 'test text 4');
		
		$query = new Query('');
		$query->setSubQuery(TEST_TEXT1);
		$query->append(TEST_TEXT2);
		
		$query->setSubQuery(TEST_TEXT3);
		$query->append(TEST_TEXT4);
		
		$subQueries = $query->getSubQueries();
		
		$this->assertEqual(
			array(TEST_TEXT1.' '.TEST_TEXT2, TEST_TEXT3.' '.TEST_TEXT4),
			$subQueries
		);
	}
}

?>