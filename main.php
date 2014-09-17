<?php
/*
Plugin Name: Event Worker Client
Plugin URI: https://github.com/event-worker/event-worker-client
Description: Fetch events
Version: 1.0
License: GPLv2
Author: Janne Kähkönen
Author URI: http://koti.tamk.fi/~c1jkahko/
*/

/**
 * The init point of the app.
 *
 * Load the needed classes and start the app.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerMainClass
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        require_once('lib/redirect.php');
        require_once('lib/core.php');
        require_once('lib/api/slim-helper.php');
        require_once('lib//api/api-routes.php');
        require_once('lib/loaders/scripts-and-styles-loader.php');
        require_once('lib/loaders/page-template-loader.php');

        add_action('plugins_loaded', array($this, 'event_worker_init'));
        add_filter('query_vars', array($this, 'add_new_query_vars' ));
    }

    /**
     * Add filter variable to filter the posts.
     *
     * @param string $vars the filter variable.
     *
     */
    function add_new_query_vars($vars)
    {   
        $vars[] = 'filter';
        return $vars;
    }

    /**
     * Load the transaltions on plugin load.
     *
     */
    function event_worker_init()
    {
        load_plugin_textdomain('event-worker-translations', FALSE, dirname(plugin_basename(__FILE__)).'/lib/languages/');
    }
}
new WorkerMainClass();

?>
