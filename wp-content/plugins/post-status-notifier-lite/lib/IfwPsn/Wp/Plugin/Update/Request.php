<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 *
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Request.php 911603 2014-05-10 10:58:23Z worschtebrot $
 */
class IfwPsn_Wp_Plugin_Update_Request extends IfwPsn_Wp_Http_Request
{

    /**
     * The request action
     * @var
     */
    protected $_action;


    /**
     *
     */
    protected function _init()
    {
        parent::_init();

        $this->setUrl($this->_pm->getConfig()->plugin->updateServer);

        $this->addData('api-key', md5(IfwPsn_Wp_Proxy_Blog::getUrl()));
    }

    /**
     * @param mixed $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->addData('action', $action);
        return $this;
    }

}
 