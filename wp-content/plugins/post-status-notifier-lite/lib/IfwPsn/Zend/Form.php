<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Form.php 911603 2014-05-10 10:58:23Z worschtebrot $
 */ 
class IfwPsn_Zend_Form extends IfwPsn_Vendor_Zend_Form
{
    public function addElement($element, $name = null, $options = null)
    {
        if ($element instanceof IfwPsn_Vendor_Zend_Form_Element) {
            $name = $element->getName();
        }

        IfwPsn_Wp_Proxy_Action::doAction($this->getName() . '_before_' . $name, $this);

        $result = parent::addElement($element, $name, $options);

        $result->getElement($name)->getDecorator('HtmlTag')->setOption('id', 'form_element_' . $name);

        IfwPsn_Wp_Proxy_Action::doAction($this->getName() . '_after_' . $name, $this);

        return $result;
    }
}
