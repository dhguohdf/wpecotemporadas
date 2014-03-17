 <?php
/**
 * Clone of dsIDXpress' client-assist.php, with changes.
 */

if (!defined('ZP_NO_REDIRECT')) {
    define('ZP_NO_REDIRECT', true);
}

//bootstrap wordpress
$bootstrapSearchDir = dirname($_SERVER["SCRIPT_FILENAME"]);
$docRoot            = dirname(isset($_SERVER["APPL_PHYSICAL_PATH"]) ? $_SERVER["APPL_PHYSICAL_PATH"] : $_SERVER["DOCUMENT_ROOT"]);

while (!file_exists($bootstrapSearchDir . "/wp-load.php")) {
    $bootstrapSearchDir = dirname($bootstrapSearchDir);
    if (strpos($bootstrapSearchDir, $docRoot) === false) {
        $bootstrapSearchDir = "../../.."; // critical failure in our directory finding, so fall back to relative
        break;
    }
}

if (!defined('ABSPATH')) {
    require($bootstrapSearchDir . "/wp-load.php");
}

/**
 * Clone of dsIDXpress' client-assist.php, with changes.
 * @since 2.0.51
 */
class Bon_IDX_ClientAssist
{
   
    
    static function LoadSoldListings()
    {
        $apiParams = array();
        
        $apiParams["query.SimilarToPropertyID"]        = $_POST["PropertyID"];
        $apiParams["query.ListingStatuses"]            = '8';
        $apiParams['responseDirective.ViewNameSuffix'] = 'Sold';
        $apiParams['directive.ResultsPerPage']         = '6';
        $apiHttpResponse                               = dsSearchAgent_ApiRequest::FetchData("Results", $apiParams, false, apply_filters('bon_idx_sold_properties_cachetime', 60 * 60 * 48));
        
        $response = json_decode($apiHttpResponse["body"]);
        echo (!is_null($response) ? $response : $apiHttpResponse["body"]);
    }
}

if (!empty($_REQUEST['action'])) {
    call_user_func(array(
        'Bon_IDX_ClientAssist',
        $_REQUEST['action']
    ));
} else {
    return;
} 