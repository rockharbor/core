<?php

App::import('Lib', 'CorePlugin');

class CommunicationsRequestInstall extends CorePlugin {
	
	function install() {
		Core::addAco('/CommunicationsRequest/CommunicationsRequestTypes', 4);
	}
	
}