<?php
$GLOBALS['hook_suffix'] = '';
/**
 * Register Ajax request for metabox 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: load-psn-rules.php 735754 2013-07-03 23:08:36Z worschtebrot $
 */
$metabox = new Psn_Admin_Metabox_Rules($pm);
return $metabox->getAjaxRequest();
