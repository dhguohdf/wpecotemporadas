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
 * @package    IfwPsn_Vendor_Zend_View
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: Exception.php 911603 2014-05-10 10:58:23Z worschtebrot $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * IfwPsn_Vendor_Zend_Exception
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Exception.php';


/**
 * Exception for IfwPsn_Vendor_Zend_View class.
 *
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_View
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_View_Exception extends IfwPsn_Vendor_Zend_Exception
{
    protected $view = null;

    public function setView(IfwPsn_Vendor_Zend_View_Interface $view = null)
    {
        $this->view = $view;
        return $this;
    }

    public function getView()
    {
        return $this->view;
    }
}
