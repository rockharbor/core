<?php
/**
 * Publication controller class.
 *
 * @copyright     Copyright 2010, *ROCK*HARBOR
 * @link          http://rockharbor.org *ROCK*HARBOR
 * @package       core
 * @subpackage    core.app.controllers
 */

/**
 * Includes
 */
App::import('Controller', 'SimpleCruds');

/**
 * Publications Controller
 *
 * @package       core
 * @subpackage    core.app.controllers
 */
class PublicationsController extends SimpleCrudsController {

/**
 * The name of the controller
 *
 * @var string
 */
	var $name = 'Publications';
	
/**
 * Model::beforeFilter() callback
 *
 * Used to override Acl permissions for this controller.
 *
 * @access private
 */ 
	function beforeFilter() {
		parent::beforeFilter();
		$this->_editSelf('subscriptions');
	}

/**
 * Shows a list of subscriptions available to a User and what they have subscribed to
 */
	function subscriptions() {
		$this->viewPath = 'publications';
		
		$userId = $this->passedArgs['User'];
		if (!$userId) {
			$this->cakeError('error404');
		}
		
		// ones they are subscribed to (have to go a bit round about so we don't do HABTM joins
		$subscriptions = Set::extract('/Publication/id', $this->Publication->User->Profile->User->find('first', array(
			'conditions' => array(
				'User.id' => $userId
			),
			'contain' => array(
				'Publication'
			)
		)));
		
		// all
		$publications = $this->paginate();
		
		$this->set(compact('publications', 'subscriptions', 'userId'));
	}

/**
 * Subscribes or unsubscribes a user from a Publication
 *
 * @param integer $publicationId The id of the publication
 * @param boolean $subscribe Whether to subscribe or unsubscribe the User
 */
	function toggle_subscribe($publicationId = null, $subscribe = false) {
		$this->viewPath = 'publications';
		$userId = $this->passedArgs['User'];
		
		if (!$publicationId || !$userId) {
			$this->cakeError('error404');
		}
		
		$current = $this->Publication->User->find('first', array(
			'conditions' => array(
				'User.id' => $userId
			),
			'contain' => array(
				'Publication'
			)
		));
		
		$current = Set::extract('/Publication/id', $current);
		
		if ($subscribe) {
			if (!in_array($publicationId, $current)) {
				$current[] = $publicationId;
			}
		} else {
			$current = array_diff($current, array($publicationId));
		}
		
		$data = array(
			'User' => array(
				'id' => $userId
			),
			'Publication' => array(
				'Publication' => $current
			)
		);
		
		$publication = $this->Publication->read(null, $publicationId);
		
		$this->set(compact('subscribe', 'publication'));
		
		if ($this->Publication->User->saveAll($data)) {
			$this->Notifier->notify(array(
				'to' => $userId,
				'template' => 'publications_toggle_subscribe',
				'subject' => 'Subscription update'
			));
		}
	}

}
