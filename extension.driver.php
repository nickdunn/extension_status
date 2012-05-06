<?php

	Class extension_extension_status extends Extension {

		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => '__appendAssets'
				)
			);
		}
        
 		public function __appendAssets($context) {
			$callback = Symphony::Engine()->getPageCallback();
            
			// Append styles for publish area
			if($callback['driver'] == 'systemextensions') {
				Administration::instance()->Page->addScriptToHead(URL . '/extensions/extension_status/assets/system.extensions.js', 100, false);
				Administration::instance()->Page->addStylesheetToHead(URL . '/extensions/extension_status/assets/system.extensions.css', 'screen', 101, false);
			}
		}

	}