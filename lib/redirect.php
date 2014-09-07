<?php

/**
 * TODO.
 *
 */
function my_page_template_redirect()
{
    if(is_front_page() && is_page('events') )
    {
        wp_redirect( home_url( '/events/' ) );
        exit();
    }
}
add_action( 'template_redirect', 'my_page_template_redirect' );

?>