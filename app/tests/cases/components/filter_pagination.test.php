<?php

App::import('Lib', 'CoreTestCase');
App::import('Model', 'App');

class CompletelyUnrelatedModel extends AppModel {
	var $useTable = false;
}

class UnrelatedModel extends AppModel {
	var $useTable = false;
}

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

	var $uses = array('PaginateTest', 'UnrelatedModel');

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

	function paginate_other_model($model = 'EmptyModel') {
		$results = $this->FilterPagination->paginate($model);
		$this->set(compact('results'));
	}
}

Mock::generatePartial('PaginateTestsController', 'MockPaginateTestsController', array('render', 'header', 'stop'));

class FilterPaginationTestCase extends CoreTestCase {

	function startTest() {
		$this->loadFixtures('PaginateTest');
		$this->Controller = new PaginateTestsController();
		$this->Controller->__construct();
		$this->Controller->constructClasses();
		$this->Controller->Component->initialize($this->Controller);
		$this->testController = $this->Controller;
	}

	function endTest() {		
		$this->Controller->Session->destroy();
		unset($this->Controller);
		ClassRegistry::flush();
	}

	function testIndirectlyAssociatedModel() {
		$this->assertNoErrors();
		$vars = $this->testAction('/paginate_tests/paginate_other_model');
		$results = $vars['results'];
		$this->assertEqual($results, array());

		$this->assertNoErrors();
		$vars = $this->testAction('/paginate_tests/paginate_other_model/UnrelatedModel');
		$results = $vars['results'];
		$this->assertEqual($results, array());

		$this->assertNoErrors();
		$vars = $this->testAction('/paginate_tests/paginate_other_model/CompletelyUnrelatedModel');
		$results = $vars['results'];
		$this->assertEqual($results, array());
	}

	function testStartEmpty() {
		// make it not start with an empty array
		$this->Controller->FilterPagination->startEmpty = false;
		$vars = $this->testAction('/paginate_tests/');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing'
		);
		$this->assertTrue($this->Controller->Session->check('FilterPagination'));
		$this->assertEqual($results, $expected);

		// simulate new page
		$this->Controller->Session->delete('FilterPagination');
		$this->Controller->FilterPagination->startEmpty = false;
		$data = array('PaginateTest' => array('name' => 'CORE'));
		$vars = $this->testAction('/paginate_tests/filter', array(
			'data' => $data
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'The CORE Awesomeness'
		);
		$this->assertEqual($this->Controller->Session->read('FilterPagination.data'), $data);
		$this->assertEqual($results, $expected);

		// same data, different page
		$vars = $this->testAction('/paginate_tests/filter/page:1', array(
			'data' => $data
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'The CORE Awesomeness'
		);
		$this->assertEqual($this->Controller->data, $data);
		$this->assertEqual($results, $expected);

		// changed data (with passed pagination var)
		$newData = array('PaginateTest' => array('name' => 'Paginated'));
		$vars = $this->testAction('/paginate_tests/filter/page:1', array(
			'data' => $newData
		));
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing'
		);
		$this->assertEqual($this->Controller->Session->read('FilterPagination.data'), $newData);
		$this->assertEqual($results, $expected);

		// change page via paginator
		$vars = $this->testAction('/paginate_tests/filter/page:1');
		$results = Set::extract('/PaginateTest/name', $vars['results']);
		$expected = array(
			'A Paginated Thing'
		);
		$this->assertEqual($this->Controller->Session->read('FilterPagination.data'), $newData);
		$this->assertEqual($results, $expected);
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
