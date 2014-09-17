<?php

/**
 * Class for redirecting to the events page.
 *
 * Redirect to the events page.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerRedirect
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_action('template_redirect', array($this, 'event_worker_frontpage_redirect'));
    }

    /**
     * A simple redirect function.
     *
     * Redirect if the events page is the static front page.
     *
     */
    function event_worker_frontpage_redirect()
    {
        if(is_front_page() && is_page('events'))
        {
            wp_redirect(home_url('/events/'));
            exit();
        }
    }
}
new WorkerRedirect();

?>