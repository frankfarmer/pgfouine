<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/base.lib.php');

class TestSlowestQueryList extends UnitTestCase {
	
	function testAddQuery() {
		$query1 = new Query('');
		$query1->setDuration(1.2);
		$query2 = new Query('');
		$query2->setDuration(1.5);
		$query3 = new Query('');
		$query3->setDuration(1.7);
		$query4 = new Query('');
		$query4->setDuration(1.3);
		
		$list = new SlowestQueryList(2);
		$list->addQuery($query1);
		$queries =& $list->getQueries();
		$this->assertEqual(1, count($queries));
		$this->assertReference($queries['1.2'][0], $query1);
		
		$list->addQuery($query2);
		$queries =& $list->getQueries();
		$this->assertEqual(2, count($queries));
		$this->assertReference($queries['1.2'][0], $query1);
		$this->assertReference($queries['1.5'][0], $query2);
		
		$list->addQuery($query3);
		$queries =& $list->getQueries();
		$this->assertEqual(2, count($queries));
		$this->assertReference($queries['1.5'][0], $query2);
		$this->assertReference($queries['1.7'][0], $query3);
		
		$list->addQuery($query4);
		$queries =& $list->getQueries();
		$this->assertEqual(2, count($queries));
		$this->assertReference($queries['1.5'][0], $query2);
		$this->assertReference($queries['1.7'][0], $query3);
	}
	
	function testGetSortedQueries() {
		
	}
}

?>