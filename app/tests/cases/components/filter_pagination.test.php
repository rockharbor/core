<?php

App::import('Lib', 'CoreTestCase');
App::import('Model', 'App');

class EmptyModel extends AppModel {
	var $useTable = false;
}

class PaginateTest extends AppModel {
	var $hasOne = array(
		'EmptyModel'
	);

	var $belongsTo = array(
		'EmptyModel'
	);
}

class PaginateTestsController extends Controller {

	var $name = 'PaginateTests';

	var $components = array('FilterPagination', 'Session');

	function index() {
		$this->paginate = array(
			'limit' => 1
		);
		$results = $this->FilterPagination->paginate();
		$this->set(compact('results'));
	}

	function filter() {
		$conditions = $this->postConditions($this->data, 'LIKE', 'OR');
		$limit = 2;
		$this->paginate = compact('conditions', 'limit');
		$results = $this->FilterPagination->paginate();
		$this->set(compact('results'));
	}
}

Mock::generatePartial('PaginateTestsController', 'MockPaginateTestsController', array('render', 'header', 'stop'));

class FilterPaginationTestCase extends CoreTestCase {

	var $fixtures = array('app.paginate_test');

	function startTest() {
		$this->Controller = new MockPaginateTestsController();
		$this->Controller->constructClasses();
		$this->Controller->Component->initialize($this->Controller);
		$this->testController = $this->Controller;
	}

	function endTest() {		
		$this->Controller->Session->destroy();
		unset($this->Controller);
		ClassRegistry::flush();
	}

	function testFilterPaginate() {
		$vars = $this->testAction('/paginate_tests/');
		$this->assertEqual($vars['results'], array());

		$data = array('Some data to persist');
		$vars = $this->testAction('/paginate_tests/', array(
			'data' => $data
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing'
		);
		$this->assertTrue($this->Controller->Session->check('FilterPagination'));
		$this->assertEqual($results, $expected);

		// check to make sure data leaves when a new pagination call is made
		$vars = $this->testAction('/paginate_tests/');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$this->assertEqual($results, array());
		$this->assertEqual($this->Controller->data, array());
	}

	function testDataPersist() {
		$data = array(
			'PaginateTest' => array(
				'name' => 'a'
			)
		);
		$vars = $this->testAction('/paginate_tests/filter', array(
			'data' => $data
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing',
			'The CORE Awesomeness'
		);
		$this->assertTrue($this->Controller->Session->check('FilterPagination'));
		$this->assertEqual($results, $expected);

		// check to see that data persists when a pagination call is made
		$vars = $this->testAction('/paginate_tests/filter/page:1/sort:name/direction:desc');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'The CORE Awesomeness',
			'Back to the Future'
		);
		$this->assertEqual($results, $expected);
		$this->assertEqual($this->Controller->data, $data);

		// and check to see that changing pages continues the original search filter
		$vars = $this->testAction('/paginate_tests/filter/page:2/sort:name/direction:desc');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing'
		);
		$this->assertEqual($results, $expected);
	}

}

?>
