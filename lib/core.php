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
        require_once('pdf-generation/plugin.php');
        require_once('pre-order-events.php');
        require_once('admin-meta.php');
        require_once('format-date.php');
        
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
            'singular_name' => __('Event', 'my-pluginname'),
            'add_new' => __('Add new event', 'my-pluginname'),
            'add_new_item' => __('Add new event', 'my-pluginname'),
            'edit_item' => __('Edit event', 'my-pluginname'),
            'new_item' => __('Add new event', 'my-pluginname'),
            'all_items' => __('View events'),
            'view_item' => __('View event'),
            'search_items' => __('Search events'),
            'not_found' =>  __('No events found'),
            'not_found_in_trash' => __('No events found in Trash'), 
            'parent_item_colon' => '',
            'menu_name' => __('Events', 'my-pluginname'),
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
            'name' => _x( 'Event Categories', 'Taxonomy general name' ),
            'singular_name' => _x( 'event', 'Taxonomy singular name' ),
            'search_items' =>  __( 'Search event categories' ),
            'all_items' => __( 'All event categories' ),
            'parent_item' => __( 'Parent event category' ),
            'parent_item_colon' => __( 'Parent event category:' ),
            'edit_item' => __( 'Edit event category' ), 
            'update_item' => __( 'Update event category' ),
            'add_new_item' => __( 'Add new event category' ),
            'new_item_name' => __( 'New event name' ),
            'menu_name' => __( 'Event categories' ),
        );

        $event_category_args = array(
            'hierarchical' => true,
            'labels' => $event_category_labels,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array('slug' => $this->category_slug()),
        );

        register_taxonomy('event_category', array('events'), $event_category_args);
        $default_cat = __('Uncategorized event', 'my-pluginname');

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