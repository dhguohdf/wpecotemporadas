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
 * @package    IfwPsn_Vendor_Zend_Validate
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Alpha.php 911603 2014-05-10 10:58:23Z worschtebrot $
 */

/**
 * @see IfwPsn_Vendor_Zend_Validate_Abstract
 */
require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    IfwPsn_Vendor_Zend_Validate
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IfwPsn_Vendor_Zend_Validate_Alpha extends IfwPsn_Vendor_Zend_Validate_Abstract
{
    const INVALID      = 'alphaInvalid';
    const NOT_ALPHA    = 'notAlpha';
    const STRING_EMPTY = 'alphaStringEmpty';

    /**
     * Whether to allow white space characters; off by default
     *
     * @var boolean
     * @deprecated
     */
    public $allowWhiteSpace;

    /**
     * Alphabetic filter used for validation
     *
     * @var IfwPsn_Vendor_Zend_Filter_Alpha
     */
    protected static $_filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID      => "Invalid type given. String expected",
        self::NOT_ALPHA    => "'%value%' contains non alphabetic characters",
        self::STRING_EMPTY => "'%value%' is an empty string"
    );

    /**
     * Sets default option values for this instance
     *
     * @param  boolean|IfwPsn_Vendor_Zend_Config $allowWhiteSpace
     * @return void
     */
    public function __construct($allowWhiteSpace = false)
    {
        if ($allowWhiteSpace instanceof IfwPsn_Vendor_Zend_Config) {
            $allowWhiteSpace = $allowWhiteSpace->toArray();
        }

        if (is_array($allowWhiteSpace)) {
            if (array_key_exists('allowWhiteSpace', $allowWhiteSpace)) {
                $allowWhiteSpace = $allowWhiteSpace['allowWhiteSpace'];
            } else {
                $allowWhiteSpace = false;
            }
        }

        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
    }

    /**
     * Returns the allowWhiteSpace option
     *
     * @return boolean
     */
    public function getAllowWhiteSpace()
    {
        return $this->allowWhiteSpace;
    }

    /**
     * Sets the allowWhiteSpace option
     *
     * @param boolean $allowWhiteSpace
     * @return IfwPsn_Vendor_Zend_Filter_Alpha Provides a fluent interface
     */
    public function setAllowWhiteSpace($allowWhiteSpace)
    {
        $this->allowWhiteSpace = (boolean) $allowWhiteSpace;
        return $this;
    }

    /**
     * Defined by IfwPsn_Vendor_Zend_Validate_Interface
     *
     * Returns true if and only if $value contains only alphabetic characters
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);

        if ('' === $value) {
            $this->_error(self::STRING_EMPTY);
            return false;
        }

        if (null === self::$_filter) {
            /**
             * @see IfwPsn_Vendor_Zend_Filter_Alpha
             */
            require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Filter/Alpha.php';
            self::$_filter = new IfwPsn_Vendor_Zend_Filter_Alpha();
        }

        self::$_filter->allowWhiteSpace = $this->allowWhiteSpace;

        if ($value !== self::$_filter->filter($value)) {
            $this->_error(self::NOT_ALPHA);
            return false;
        }

        return true;
    }

}
