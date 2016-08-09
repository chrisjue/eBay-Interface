<?php

// ==============================================================================================
// Description of class
// ==============================================================================================

require_once 'MainGetItem.php';

class GetMultipleItems extends MainGetItem {
    
    public function printResult($itemIDs){
        
        //Get XML body incl item ID
        $xml = $this->callEbay($itemIDs);
        $xml = simplexml_load_string($xml);

        if ($xml->Ack == "Success") {
            
            echo "<p>Download successful!</p>";
            
            foreach($xml->Item as $it){
                
                echo "<p>";
                echo "Title: " . $it->Title . "<br>";
                echo "Price: " . $it->ConvertedCurrentPrice ."<br>";
                echo "Currency: " . $it->ConvertedCurrentPrice["currencyID"] ."<br>";
                echo "Status: " . $it->ListingStatus ."<br>";
                echo "URL: " . $it->ViewItemURLForNaturalSearch ."<br>";
                echo "Shop: " . $it->Seller->UserID ."<br>";
                echo "Quantity sold items: " . $it->QuantitySold ."<br>";
                echo "</p>";
                
            }
        }
        else // If the response does not indicate 'Success,' print an error
        {  
            echo "<h3>Oops! The request was not successful. Make sure you are using a valid ";
            echo "AppID for the production environment.</h3>";
            
            echo "<p>";
            echo "Errore Code: " . $xml->Errors->ErrorCode . "<br>";
            echo "Error Classification: " . $xml->Errors->ErrorClassification . "<br>";
            echo "Short Message: " . $xml->Errors->ShortMessage ."<br>";
            echo "Long Message: " . $xml->Errors->LongMessage;
            echo "</p>"; 
        }
        
    }
    
}