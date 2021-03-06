<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 * 
 * 
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id: Action.php 911603 2014-05-10 10:58:23Z worschtebrot $
 */ 
class IfwPsn_Wp_Proxy_Action
{
    /**
     * Alias for add_action
     *
     * @param $tag
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function add($tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        return add_action($tag, $function_to_add, $priority, $accepted_args);
    }

    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @param $tag
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addPlugin(IfwPsn_Wp_Plugin_Manager $pm, $tag, $function_to_add, $priority = 10, $accepted_args = 1)
    {
        $tag = $pm->getAbbrLower() . '_' . $tag;
        return self::add($tag, $function_to_add, $priority, $accepted_args);
    }

    /**
     * Alias for do_action
     *
     * @param $tag
     * @param string $arg
     * @return null
     */
    public static function doAction($tag, $arg = '')
    {
        return do_action($tag, $arg);
    }

    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @param $tag
     * @param string $args
     * @return null
     */
    public static function doPlugin(IfwPsn_Wp_Plugin_Manager $pm, $tag, $args = '')
    {
        return self::doAction($pm->getAbbrLower() . '_' . $tag, $args);
    }

    /**
     * @param $tag
     * @param $function_to_remove
     * @param int $priority
     * @return bool
     */
    public static function remove($tag, $function_to_remove, $priority = 10)
    {
        return remove_action($tag, $function_to_remove, $priority);
    }

    /**
     * @param IfwPsn_Wp_Plugin_Manager $pm
     * @param $tag
     * @param $function_to_remove
     * @param int $priority
     * @return bool
     */
    public static function removePlugin(IfwPsn_Wp_Plugin_Manager $pm, $tag, $function_to_remove, $priority = 10)
    {
        return remove_action($pm->getAbbrLower() . '_' . $tag, $function_to_remove, $priority);
    }

    /**
     * Alias for did_action
     *
     * @param $tag string
     * @return bool
     */
    public static function did($tag)
    {
        return did_action($tag) > 0;
    }

    /**
     * @return bool
     */
    public static function didPluginsLoaded()
    {
        return self::did('plugins_loaded');
    }

    /**
     * Shortcut for add_action( 'admin_init', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
     * @return bool|void
     */
    public static function addAdminInit($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('admin_init', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'admin_menu', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
     * @return bool|void
     */
    public static function addAdminMenu($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('admin_menu', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'admin_head', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addAdminHead($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('admin_head', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'current_screen', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @see http://codex.wordpress.org/Plugin_API/Action_Reference/admin_menu
     * @return bool|void
     */
    public static function addCurrentScreen($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('current_screen', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'plugins_loaded', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addPluginsLoaded($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('plugins_loaded', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'wp_loaded', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addWpLoaded($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('wp_loaded', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'admin_enqueue_scripts', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addAdminEnqueueScripts($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('admin_enqueue_scripts', $function_to_add, $priority, $accepted_args);
    }

    /**
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addAdminEnqueueStyles($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::addAdminEnqueueScripts($function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'wp_enqueue_scripts', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addEnqueueScripts($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('wp_enqueue_scripts', $function_to_add, $priority, $accepted_args);
    }

    /**
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addEnqueueStyles($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::addEnqueueScripts($function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'wp_footer', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addWpFooter($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('wp_footer', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'admin_footer-*', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addAdminFooterCurrentScreen($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('admin_footer-'. IfwPsn_Wp_Proxy_Screen::getCurrent()->id, $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'wp_dashboard_setup', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addWpDashboardSetup($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('wp_dashboard_setup', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'init', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addInit($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('init', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'widgets_init', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addWidgetsInit($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('widgets_init', $function_to_add, $priority, $accepted_args);
    }

    /**
     * Shortcut for add_action( 'wp_head', 'function_name' )
     *
     * @param $function_to_add
     * @param int $priority
     * @param int $accepted_args
     * @return bool|void
     */
    public static function addWpHead($function_to_add, $priority = 10, $accepted_args = 1)
    {
        return self::add('wp_head', $function_to_add, $priority, $accepted_args);
    }
}
