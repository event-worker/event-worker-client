<?php

/**
 * The core of the plugin
 *
 * Run the core.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerCore
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {   
        require_once('common-options.php');
        require_once('file-generation/ajax-helper.php');
        require_once('pre-order-events.php');
        require_once('admin-meta.php');

        add_action('init', array($this, 'worker_plugin_init'));
    }

    /** 
     * Return the event category slug.
     *
     * @return string return the event category slug
     *
     */
    function category_slug()
    {
        return 'event-category';
    }

    /** 
     * Init the plugin.
     *
     */
    function worker_plugin_init()
    {   
        new CommonOptions();

        $event_type_labels = array(
            'name' => 'events',
            'singular_name' => __('Event', 'event-worker-translations'),
            'add_new' => __('Add new event', 'event-worker-translations'),
            'add_new_item' => __('Add new event', 'event-worker-translations'),
            'edit_item' => __('Edit event', 'event-worker-translations'),
            'new_item' => __('Add new event', 'event-worker-translations'),
            'all_items' => __('View events', 'event-worker-translations'),
            'view_item' => __('View event', 'event-worker-translations'),
            'search_items' => __('Search events', 'event-worker-translations'),
            'not_found' =>  __('No events found', 'event-worker-translations'),
            'not_found_in_trash' => __('No events found in Trash', 'event-worker-translations'), 
            'parent_item_colon' => '',
            'menu_name' => __('Events', 'event-worker-translations'),
        );

        $event_type_args = array(
            'labels' => $event_type_labels,
            'public' => true,
            'query_var' => true,
            'rewrite' => true,
            'capability_type' => 'post',
            'has_archive' => true, 
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'author')
            //'taxonomies' => array('category')
        ); 

        register_post_type('events', $event_type_args);

        $event_category_labels = array(
            'name' => __( 'Event categories', 'event-worker-translations' ),
            'singular_name' => _x( 'event', 'event-worker-translations' ),
            'search_items' =>  __( 'Search event categories', 'event-worker-translations' ),
            'all_items' => __( 'All event categories', 'event-worker-translations' ),
            'parent_item' => __( 'Parent event category', 'event-worker-translations' ),
            'parent_item_colon' => __( 'Parent event category:', 'event-worker-translations' ),
            'edit_item' => __( 'Edit event category', 'event-worker-translations' ), 
            'update_item' => __( 'Update event category', 'event-worker-translations' ),
            'add_new_item' => __( 'Add new event category', 'event-worker-translations' ),
            'new_item_name' => __( 'New event name', 'event-worker-translations' ),
            'menu_name' => __('Event categories', 'event-worker-translations'),
        );

        $event_category_args = array(
            'hierarchical' => true,
            'labels' => $event_category_labels,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array('slug' => $this->category_slug()),
        );

        register_taxonomy('event_category', array('events'), $event_category_args);
        $default_cat = __('Uncategorized event', 'event-worker-translations');

        $default_event_cats = array($default_cat);

        foreach ($default_event_cats as $cat)
        {
            if (!term_exists($cat, 'event_category'))
            {
                wp_insert_term($cat, 'event_category');
            }
        }
    }
}
new WorkerCore();

?>