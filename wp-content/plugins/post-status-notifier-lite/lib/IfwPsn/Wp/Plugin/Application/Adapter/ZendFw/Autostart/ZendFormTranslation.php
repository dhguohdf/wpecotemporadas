<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * Set default form translator
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: ZendFormTranslation.php 911603 2014-05-10 10:58:23Z worschtebrot $
 */
require_once dirname(__FILE__) . '/Abstract.php';

class IfwPsn_Wp_Plugin_Application_Adapter_ZendFw_Autostart_ZendFormTranslation extends IfwPsn_Wp_Plugin_Application_Adapter_ZendFw_Autostart_Abstract
{
    public function execute()
    {
        require_once IFW_PSN_LIB_ROOT . 'IfwPsn/Vendor/Zend/Translate/Adapter/Array.php';

        $translator = new IfwPsn_Vendor_Zend_Translate(
            'IfwPsn_Vendor_Zend_Translate_Adapter_Array',
            $this->_adapter->getPluginManager()->getPathinfo()->getRootLib() . 'IfwPsn/Zend/Form/resources/languages',
            IfwPsn_Wp_Proxy_Blog::getLanguage(),
            array('scan' => IfwPsn_Vendor_Zend_Translate::LOCALE_DIRECTORY)
        );

        IfwPsn_Vendor_Zend_Validate_Abstract::setDefaultTranslator($translator);
    }
}
