<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Controller
 * @subpackage IfwPsn_Vendor_Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Json.php 911603 2014-05-10 10:58:23Z worschtebrot $
 */

/**
 * @see IfwPsn_Vendor_Zend_Controller_Action_Helper_Abstract
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Action/Helper/Abstract.php';

/**
 * Simplify AJAX context switching based on requested format
 *
 * @uses       IfwPsn_Vendor_Zend_Controller_Action_Helper_Abstract
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Controller
 * @subpackage IfwPsn_Vendor_Zend_Controller_Action_Helper
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Controller_Action_Helper_Json extends IfwPsn_Vendor_Zend_Controller_Action_Helper_Abstract
{
    /**
     * Suppress exit when sendJson() called
     * @var boolean
     */
    public $suppressExit = false;

    /**
     * Create JSON response
     *
     * Encodes and returns data to JSON. Content-Type header set to
     * 'application/json', and disables layouts and viewRenderer (if being
     * used).
     *
     * @param  mixed   $data
     * @param  boolean $keepLayouts
     * @param  boolean|array $keepLayouts
     * @param  boolean $encodeData Provided data is already JSON
     * NOTE:   if boolean, establish $keepLayouts to true|false
     *         if array, admit params for IfwPsn_Vendor_Zend_Json::encode as enableJsonExprFinder=>true|false
     *         if $keepLayouts and parmas for IfwPsn_Vendor_Zend_Json::encode are required
     *         then, the array can contains a 'keepLayout'=>true|false and/or 'encodeData'=>true|false
     *         that will not be passed to IfwPsn_Vendor_Zend_Json::encode method but will be passed
     *         to IfwPsn_Vendor_Zend_View_Helper_Json
     * @throws IfwPsn_Vendor_Zend_Controller_Action_Helper_Json
     * @return string
     */
    public function encodeJson($data, $keepLayouts = false, $encodeData = true)
    {
        /**
         * @see IfwPsn_Vendor_Zend_View_Helper_Json
         */
        require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/View/Helper/Json.php';
        $jsonHelper = new IfwPsn_Vendor_Zend_View_Helper_Json();
        $data = $jsonHelper->json($data, $keepLayouts, $encodeData);

        if (!$keepLayouts) {
            /**
             * @see IfwPsn_Vendor_Zend_Controller_Action_HelperBroker
             */
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Controller/Action/HelperBroker.php';
            IfwPsn_Vendor_Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
        }

        return $data;
    }

    /**
     * Encode JSON response and immediately send
     *
     * @param  mixed   $data
     * @param  boolean|array $keepLayouts
     * @param  $encodeData Encode $data as JSON?
     * NOTE:   if boolean, establish $keepLayouts to true|false
     *         if array, admit params for IfwPsn_Vendor_Zend_Json::encode as enableJsonExprFinder=>true|false
     *         if $keepLayouts and parmas for IfwPsn_Vendor_Zend_Json::encode are required
     *         then, the array can contains a 'keepLayout'=>true|false and/or 'encodeData'=>true|false
     *         that will not be passed to IfwPsn_Vendor_Zend_Json::encode method but will be passed
     *         to IfwPsn_Vendor_Zend_View_Helper_Json
     * @return string|void
     */
    public function sendJson($data, $keepLayouts = false, $encodeData = true)
    {
        $data = $this->encodeJson($data, $keepLayouts, $encodeData);
        $response = $this->getResponse();
        $response->setBody($data);

        if (!$this->suppressExit) {
            $response->sendResponse();
            exit;
        }

        return $data;
    }

    /**
     * Strategy pattern: call helper as helper broker method
     *
     * Allows encoding JSON. If $sendNow is true, immediately sends JSON
     * response.
     *
     * @param  mixed   $data
     * @param  boolean $sendNow
     * @param  boolean $keepLayouts
     * @param  boolean $encodeData Encode $data as JSON?
     * @return string|void
     */
    public function direct($data, $sendNow = true, $keepLayouts = false, $encodeData = true)
    {
        if ($sendNow) {
            return $this->sendJson($data, $keepLayouts, $encodeData);
        }
        return $this->encodeJson($data, $keepLayouts, $encodeData);
    }
}
