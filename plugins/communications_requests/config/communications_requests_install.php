<?php

App::import('Lib', 'CorePlugin');

class CommunicationsRequestsInstall extends CorePlugin {
	
	function install() {
		Core::addAco('/CommunicationsRequests', 4);
		Core::addAco('/CommunicationsRequests/Requests/history', 5);
		Core::addAco('/CommunicationsRequests/Requests/add', 5);
		Core::addAco('/CommunicationsRequests/Requests/view', 12);
	}
	
}