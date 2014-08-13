<?php
/**
 * Register Ajax request for metabox 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: load-psn-plugin_info.php 937488 2014-06-23 21:21:03Z worschtebrot $
 */
$metabox = new IfwPsn_Wp_Plugin_Metabox_PluginInfo($pm);
return $metabox->getAjaxRequest();
