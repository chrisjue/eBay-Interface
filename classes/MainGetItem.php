<?php

// ==============================================================================================
// General Class for all GetItem requests
// ==============================================================================================

abstract class MainGetItem {

    // Properties ----------------------------------------------------------------------------
    
    protected $itemId;
    protected $xmlRequest;
    protected $call;
    protected $xmlSnippet;
    
    protected $siteId = 77;   // default: Germany
	protected $eBayApiVersion = 963;
    protected $encoding = 'XML';
    protected $environment;   // toggle between sandbox and production
    protected $keys = array(
        'production' => array(
            'DEVID'     => 'd19b9xxxxxxxxxxxxxxxxxxxxxxxxxxx0860',
            'AppID'     => 'Pxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxa', 
            'CertID'    => 'exxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxa',
            'ServerUrl' => 'http://open.api.ebay.com/shopping?',
            ),
        'sandbox' => array(
            'DEVID'     => '6daxxxxxxxxxxxxxxxxxxxxxxxxxx1e4622',
            'AppID'     => 'Mixxxxxxxxxxxxxxxxxxxxxxxxxxxxxx930',
            'CertID'    => '68xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx00e',
            'UserToken' => 'AgAxxxxxxxxlaaaangxxxxxxxxxxIrGgYZ',
            'ServerUrl' => 'https://api.sandbox.ebay.com/ws/api.dll'
        )
    );    
    
    
    // Constructor ------------------------------------------------------------------------------
    
    function __construct($env){
        
        //$env must be 'production' or 'sandbox'
        //if not, exit
        if ($env == 'production' || $env == 'sandbox'){
            $this->environment = $env;
        }
        else{
            //$this->__destruct();
            exit('Creation of Object was not successful! Please choose either <i>production</i> or <i>sandbox</i> as environment!<br>');
        }
        
        // The name of extended class is the name of the eBay API call name
        $this->call = get_class($this);
        
    }
    
    
    // Protected Methods --------------------------------------------------------------------------
    
    
    protected function getRequestXml($itemId){
        
        /*
            This methods creates a XML file we need for the eBay API call.
            It includes all provided eBay items IDs
            The chosen Include Selector 'Details' provides as much info as possible of every item.
        */
        
        //Creating a new XML
        $requestXml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><'. $this->call . 'Request></' . $this->call . 'Request>');
        
        $requestXml->addAttribute('xmlns','urn:ebay:apis:eBLBaseComponents');
        
        //Adding all IdemIDs
        if(is_array($itemId)){                                      // If more IDs are in an array
            for($i=0; $i<=(count($itemId)-1); $i++){                // list them all
                $requestXml->addChild('ItemID', $itemId[$i]);    
            }
        }else{                                                      // If $itemId only contains one ID
            $requestXml->addChild('ItemID', $itemId);               // List one single ID
        }
        
        $requestXml->addChild('IncludeSelector','Details');         // The selector 'Details' provides all Infos about the item
        
        return $requestXml->asXML(); 
    }
    
    
    protected function callEbay($itemId){
        
        /*
            This methods calls eBay using the generated request XML.
            Ebay returns a XML containing the requested data. 
        */
        
        //Get API values according to environment
        $apiValues = $this->keys[$this->environment];
        
        //Get XML body
        $requestXml = $this->getRequestXml($itemId);
        
        //Headers
		$headers = array (
				'X-EBAY-API-DEV-NAME: ' . $apiValues['DEVID'],
                'X-EBAY-API-APP-NAME: ' . $apiValues['AppID'],
                'X-EBAY-API-CERT-NAME: ' . $apiValues['CertID'],
				'X-EBAY-API-VERSION:' . $this->eBayApiVersion,
				'X-EBAY-API-SITE-ID:' . $this->siteId,
				'X-EBAY-API-CALL-NAME:' . $this->call,
				'X-EBAY-API-REQUEST-ENCODING:' . $this->encoding,
				'Content-type:text/xml;charset=utf-8'
			);
    
		$session  = curl_init($apiValues['ServerUrl']);                // create a curl session
		curl_setopt($session, CURLOPT_POST, true);                     // POST request type
		curl_setopt($session, CURLOPT_HTTPHEADER, $headers);           // set headers using $headers array
		curl_setopt($session, CURLOPT_POSTFIELDS, $requestXml);        // set the body of the POST
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);           // return values as a string, not to std out
		$responseXml = curl_exec($session);                            // send the request
		curl_close($session);                                          // close the session
		
		return $responseXml;                                           // returns a string	
    }
    
    
    
    
    // Public Methods ---------------------------------------------------------------------------
    
    
    public function printRequest($itemId){
        
        /*
            This methods can be used by developers, if there are any issues with the request file.
            It prints the used environment, the used eBay API call name and all item IDs.
        */

        //Call ebayAPI
        $requestXml = $this->getRequestXml($itemId);
        
        //Read XML-File into object
        $xmlBody = simplexml_load_string($requestXml);
        
        //Print on screen
        echo '<p>';
        echo "Environment: " . $this->environment . "<br>";
        echo "eBay Call: " . $this->call . "<br><br>";
        
        foreach($xmlBody->ItemID as $item){
            echo "Item ID: " . $item . "<br>";
        }
        echo "Selector: " . $xmlBody->IncludeSelector . "<br>";
        
        echo '</p>';
    }
    
    
    abstract public function printResult($itemId);
    
}