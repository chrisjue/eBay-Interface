<?php

// ==============================================================================================
// Description of class
// ==============================================================================================

require_once 'MainGetItem.php';

class GetSingleItem extends MainGetItem {
    
    // Public Methods ---------------------------------------------------------------------------
    
    public function printResult($itemId){
        
        //Get XML body incl item ID
        $xml = $this->callEbay($itemId);

        $xml = simplexml_load_string($xml);

        if ($xml->Ack == "Success") {
            
            echo "<p>Download successful!</p>";

            echo "<p>";
            echo "Title: " . $xml->Item->Title . "<br>";
            echo "Price: " . $xml->Item->ConvertedCurrentPrice ."<br>";
            echo "Currency: " . $xml->Item->ConvertedCurrentPrice["currencyID"] ."<br>";
            echo "Status: " . $xml->Item->ListingStatus ."<br>";
            echo "URL: " . $xml->Item->ViewItemURLForNaturalSearch ."<br>";
            echo "Shop: " . $xml->Item->Seller->UserID ."<br>";
            echo "Quantity sold items: " . $xml->Item->QuantitySold ."<br>";
            echo "</p>";


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