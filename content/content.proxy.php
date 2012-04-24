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
		    $version = self::normaliseVersionNumber(Symphony::Configuration()->get('version', 'symphony'));
		    $version = '2.3.0';
		    
		    if(empty($id)) {
		        $response->setAttribute('error', 'ID not set');
		        echo $response->generate();die;
		    }
		    
		    $xml = file_get_contents(sprintf('http://symphonyextensions.com/api/extensions/%s/', $id));
		    
		    if(!$xml) {
		        $response->setAttribute('error', 'ID not found');
		        echo $response->generate();die;
		    }
		    		    
		    $extension = simplexml_load_string($xml);
		    $compatibility = $extension->xpath("//compatibility/symphony[@version='" . $version . "']");
		    
		    if(count($compatibility) == 0) {
		        $response->setAttribute('compatible', 'no');
		    } else {
		        $response->setAttribute('compatible', 'yes');
		        $response->setAttribute('latest', $compatibility[0]->attributes()->use);
		    }

		    echo $response->generate();die;
		    
		}
	
	}