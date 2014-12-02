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
class WorkerClientCore
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
     * TODO.
     *
     * TODO.
     *
     */
    function add_new_events_columns($columns)
    {
        $columns = array(
            //'cb' => '<input type="checkbox" />',
            'title'    => __('Event', 'event-worker-translations'),
            'event_status' => ucfirst(__('status', 'event-worker-translations')),
            'event_duration'    => ucfirst(__('duration', 'event-worker-translations')),
            'date'     => ucfirst(__('added', 'event-worker-translations'))
        );
        return $columns;
    }

    /**
     * TODO.
     *
     * TODO.
     *
     */
    function manage_events_columns($column, $post_id)
    {
        if ($column == "event_status")
        {
            if (get_post_meta($post_id, 'event_status')[0] == "http://schema.org/EventCancelled")
            {
                $status = '<font color="red">' . strtoupper(__('cancelled', 'event-worker-translations')) . '</font>' ;
                echo $status;
            }
            else
            {
                $status = '<font color="green">' . strtoupper(__('active', 'event-worker-translations')) . '</font>' ;
                echo $status;
            }
        }
        if ($column == "event_duration")
        {
            echo get_post_meta($post_id, 'event_start_date')[0] . '<br>' . get_post_meta($post_id, 'event_end_date')[0];
        }
    }

    /**
     * Init the plugin.
     *
     */
    function worker_plugin_init()

    {
        new WorkerClientCommonOptions();

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
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => true,
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'author'),
            'capabilities' => array(
                'create_posts' => false,
            ),
            'map_meta_cap' => false // Set to true, if users are allowed to edit/delete existing posts
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
            'show_ui' => false,
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

        if (!class_exists('WorkerHostMain'))
        {
            add_filter('manage_edit-events_columns', array($this, 'add_new_events_columns'));
            add_action('manage_events_posts_custom_column', array($this, 'manage_events_columns'), 10, 2);
        }
    }

}
new WorkerClientCore();

?>