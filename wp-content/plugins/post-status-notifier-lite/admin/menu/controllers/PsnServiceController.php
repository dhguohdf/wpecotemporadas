<?php
/**
 * Service controller
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $$Id: PsnServiceController.php 937488 2014-06-23 21:21:03Z worschtebrot $$
 * @package  IfwPsn_Wp
 */
class PsnServiceController extends PsnApplicationController
{
    /**
     * @var IfwPsn_Wp_Email
     */
    protected $_email;



    public function preDispatch()
    {
        if ($this->_request->has('send_test_mail')) {


        }
    }

    public function onCurrentScreen()
    {
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->_initHelp();

        // set up metaboxes
        $metaBoxContainer1 = new IfwPsn_Wp_Plugin_Metabox_Container($this->_pageHook, 'normal');
        $metaBoxContainer2 = new IfwPsn_Wp_Plugin_Metabox_Container($this->_pageHook, 'side');

        $metaBoxContainer1->addMetabox(new Psn_Admin_Metabox_TestMail($this->_pm));
        $metaBoxContainer2->addMetabox(new Psn_Admin_Metabox_ServerEnv($this->_pm));

        $this->view->metaBoxContainer1 = $metaBoxContainer1;
        $this->view->metaBoxContainer2 = $metaBoxContainer2;
    }

    /**
     *
     */
    public function sendTestMailAction()
    {
        $this->_email = new IfwPsn_Wp_Email();

        $subject = sprintf(__('Test Email from %s', 'psn'), $this->_pm->getEnv()->getName());

        $body = IfwPsn_Wp_Proxy_Filter::applyPlugin($this->_pm, 'send_test_mail_body', sprintf(
            __('This is a test email generated by %s on %s (%s)', 'psn'),
                $this->_pm->getEnv()->getName(),
                IfwPsn_Wp_Proxy_Blog::getName(),
                IfwPsn_Wp_Proxy_Blog::getUrl()
        ));

        switch (trim($this->_request->get('recipient'))) {
            case 'admin':
            default:
                $recipient = IfwPsn_Wp_Proxy_Blog::getAdminEmail();
                break;
        }

        $recipient = IfwPsn_Wp_Proxy_Filter::applyPlugin($this->_pm, 'send_test_mail_recipient', $recipient);

        $this->_email->setTo($recipient)
            ->setSubject($subject)
            ->setMessage($body)
        ;

        if ($this->_email->send()) {
            // mail sent successfully
            $resultMsg = __('Test email has been sent successfully.', 'psn');
            $msgType = null;

            IfwPsn_Wp_Proxy_Action::doPlugin($this->_pm, 'send_test_mail_success', $this);

        } else {
            // email could not be sent
            $resultMsg = __('Test email could not be sent.', 'psn');
            $msgType = 'error';

            IfwPsn_Wp_Proxy_Action::doPlugin($this->_pm, 'send_test_mail_failure', $this);
        }

        $this->getMessenger()->addMessage($resultMsg, $msgType);

        $this->_gotoIndex();
    }

    protected function _initHelp()
    {
        $help = new IfwPsn_Wp_Plugin_Menu_Help($this->_pm);
        $help->setTitle(__('Help', 'psn'))
            ->setHelp($this->_getDefaultHelpText())
            ->setSidebar($this->_getHelpSidebar())
            ->load();
    }

    /**
     * @return string
     */
    protected function _getDefaultHelpText()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function _getHelpSidebar()
    {
        $sidebar = '<p><b>' . __('For more information:', 'ifw') . '</b></p>';
        $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Plugin homepage', 'ifw') . '</a></p>',
            $this->_pm->getEnv()->getHomepage());
        if (!empty($this->_pm->getConfig()->plugin->docUrl)) {
            $sidebar .= sprintf('<p><a href="%s" target="_blank">' . __('Documentation', 'ifw') . '</a></p>',
            $this->_pm->getConfig()->plugin->docUrl);
        }
        return $sidebar;
    }

}