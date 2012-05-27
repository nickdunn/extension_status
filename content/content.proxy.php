<?php
    
	class contentExtensionExtension_statusProxy extends AJAXPage {
	
	    public static function normaliseVersionNumber($string) {
            $parts = explode('.', $string);
            return $string . str_repeat('.0', 3 - count($parts));
        }
	
		public function view() {
		    
		    header('Content-Type: text/xml');
		    $response = new XMLElement('response');
		    
		    $id = $_GET['id'];
		
			$version = Symphony::Configuration()->get('version', 'symphony');
			// remove text followed by numbers e.g. 2.3beta2 or 2.3rc1
			$version = preg_replace("/[a-z]+[0-9]+/i", '', $version);
			// remove text e.g. 2.3beta
			$version = preg_replace("/[a-z]+/i", '', $version);
			$symphony_version = self::normaliseVersionNumber($version);
			
			$response->setAttribute('symphony-version', $symphony_version);
		    
		    if(empty($id)) {
		        $response->setAttribute('error', '404');
		        echo $response->generate();die;
		    }
		    
			$ch = curl_init(); 
			curl_setopt($ch, CURLOPT_URL, sprintf('http://symphonyextensions.com/api/extensions/%s/', $id));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_USERAGENT, 'extension_status; Symphony ' . Symphony::Configuration()->get('version', 'symphony'));
			curl_setopt($ch, CURLOPT_REFERER, URL);

			$xml = curl_exec($ch); 
			
		    if(!$xml) {
		        $response->setAttribute('error', '404');
		        echo $response->generate();die;
		    }
		    		    
		    $extension = simplexml_load_string($xml);
		    $compatibility = $extension->xpath("//compatibility/symphony[@version='" . $symphony_version . "']");
		    
			$extensions = ExtensionManager::fetch();
			$current_version = $extensions[$id]['version'];
			
			$response->setAttribute('current-local-version', $current_version);
		
		    if(count($compatibility) == 0) {
		        $response->setAttribute('compatible-version-exists', 'no');
		    } else {
				$latest_version = $compatibility[0]->attributes()->use;
				$github_url = $extension->xpath("//link[@rel='github:page']/@href");
				$extension_url = $extension->xpath("//link[@rel='site:extension']/@href");
				
		        $response->setAttribute('compatible-version-exists', 'yes');
				$response->setAttribute('latest-url', (string)$github_url[0] . '/tree/' . $latest_version);
		        $response->setAttribute('latest', $latest_version);
				$response->setAttribute('can-update', version_compare($latest_version, $current_version, '>') ? 'yes' : 'no');
				$response->setAttribute('extension-url', 'http://symphonyextensions.com' . (string)$extension_url[0]);
		    }

		    echo $response->generate();die;
		    
		}
	
	}