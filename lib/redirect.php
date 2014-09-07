<?php

/**
 * A simple redirect function.
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
add_action('template_redirect', 'event_worker_frontpage_redirect');

?>