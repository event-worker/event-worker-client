<?php

/**
 * Class for ordering the posts.
 *
 * Order the posts.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerPreOrderPosts
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_filter('pre_get_posts', array($this,'custom_pre_get_posts'));
        //add_filter('pre_get_posts', array($this, 'search_filter'));
    }

    /** 
     * Check if the post already exists by the meta value.
     *
     * @param string $val the post meta value.
     *
     * @return array
     *
     */
    function wp_exist_post_by_id($val)
    {
        global $wpdb;

        $table = $wpdb->prefix . 'postmeta';

        $sql = $wpdb->prepare('SELECT * FROM ' . $table . ' WHERE meta_value = %s', $val);

        return $wpdb->get_row($sql , ARRAY_A);
    }

    /** 
     * Return the parsed time.
     *
     * @param TODO.
     *
     * @return string
     *
     */
    function parse_the_time($format)
    {
        date_default_timezone_set('Europe/Helsinki');
        $today = new DateTime('NOW');
        $today = $today->format($format);

        return $today;
    }

    /** 
     * TODO.
     *
     * @param object $query query object.
     *
     * @return object
     *
     */
    function search_filter($query)
    {
        if ($query->is_search)
        {
            $query->set('post_type', array('post', 'pages', 'events'));
            return $query;
        }
    }

    /** 
     * TODO.
     *
     * @param object $query query object.
     *
     * @return object
     *
     */
    function filter_by_date($query)
    {   
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $query->set('post_type', 'events');
        $query->set('orderby', 'meta_value_num');
        $query->set('meta_key', 'event_start_order');
        $query->set('paged', $paged);
        $query->set('order', 'ASC'); 

        if (isset($_GET['filter']) && $_GET['filter'] === 'today')
        {
            $meta_query = array(
                array(
                    'key' => 'event_start_date_filter',
                    'type' => 'numeric',
                    'value' => $this->parse_the_time('Ymd'),
                    'compare' => '<='
                )
            );
            $query->set('meta_query', $meta_query);
        }
    }

    /** 
     * Order the posts by the event start date.
     *
     * @param object $query query object.
     *
     * @return object
     *
     */
    function custom_pre_get_posts($query)
    {
        if ($query->is_main_query() && $query->is_post_type_archive("events"))
        {   
            if (is_admin())
            {
                return $query;
            }
            else
            {
                $this->filter_by_date($query);

                $options = get_option('event_worker_host_url');

                $url = $options['host-url'];
                $output = wp_remote_get($url);

                $output = json_decode($output['body'], true);
                $output = $output["@graph"];

                $args = array(
                    'post_type'   => 'events',
                    'post_status' => 'publish',
                    'numberposts' => -1
                );

                $posts = get_posts($args);

                foreach ($posts as $post)
                {
                    $compare = get_post_meta($post->ID, 'event_end_order');

                    if ($compare[0] < $this->parse_the_time('YmdHi'))
                    {
                        $post = array('ID' => $post->ID, 'post_status' => 'draft');
                        wp_update_post($post);
                    }
                }

                for ($i = 0; $i < count($output); $i++)
                {
                    $post = $this->wp_exist_post_by_id($output[$i]['version']);

                    if (get_post_status($post['post_id']) === 'publish')
                    {                    
                        $args = array(
                            'post_type'   => 'events',
                            'post_status' => 'publish',
                            'numberposts' => -1,
                            'meta_value' => $output[$i]['version']
                        );

                        $posts = get_posts($args);
						
						$var = get_post_meta($posts[0]->ID);

                        $datetime1 = date_create($var['event_modified'][0]);
                        $datetime2 = date_create($output[$i]['workPerformed']['dateModified']);

                        if ($datetime1 != $datetime2)
                        {
                            $worker_event_modified = $output[$i]['workPerformed']['dateModified'];
                            $worker_event_start_date = $output[$i]['startDate'];
                            $worker_event_end_date = $output[$i]['endDate'];

                            $worker_event_price = $output[$i]['offers']['price'];
                            $worker_event_website = $output[$i]['sameAs'];

                            $worker_event_location = $output[$i]['location']['address'];
                            $worker_event_location_name = $output[$i]['location']['name'];

                            $worker_event_geolocation = "(".$output[$i]['location']['geo']['latitude']. ", ".$output[$i]['location']['geo']['longitude'].")";

                            $worker_event_organizer = $output[$i]['organizer']['name'];

                            $event_data = array(
                                'ID' => $posts[0]->ID,
                                'post_title' => $output[$i]['name'],
                                'post_content' => $output[$i]['description'],
                                'post_status' => 'publish',
                                'post_type' => 'events'
                            );

                            $names = $output[$i]['workPerformed']['keywords'];

                            wp_set_object_terms($posts[0]->ID, $names, 'event_category');

                            update_post_meta($posts[0]->ID,
                                             'event_modified',
                                             sanitize_text_field($worker_event_modified));

                            update_post_meta($posts[0]->ID,
                                             'event_start_date',
                                             sanitize_text_field($worker_event_start_date));
                           
                            update_post_meta($posts[0]->ID,
                                             'event_end_date',
                                             sanitize_text_field($worker_event_end_date));

                            $ws = new DateTime($worker_event_start_date);

                            update_post_meta($posts[0]->ID,
                                             'event_start_order',
                                             date_format($ws, 'YmdHi'));

                            $we = new DateTime($worker_event_end_date);

                            update_post_meta($posts[0]->ID,
                                             'event_end_order',
                                             date_format($we, 'YmdHi'));

                            update_post_meta($posts[0]->ID,
                                             'event_start_date_filter',
                                             date_format($ws, 'Ymd'));

                            update_post_meta($posts[0]->ID,
                                             'event_location',
                                             sanitize_text_field($worker_event_location));

                            update_post_meta($posts[0]->ID,
                                             'event_location_name',
                                             sanitize_text_field($worker_event_location_name));

                            update_post_meta($posts[0]->ID,
                                             'event_geolocation',
                                             sanitize_text_field($worker_event_geolocation));

                            update_post_meta($posts[0]->ID,
                                             'event_price',
                                             sanitize_text_field(floatval($worker_event_price)));

                            update_post_meta($posts[0]->ID,
                                             'event_website',
                                             esc_url_raw($worker_event_website));

                            $organizer_data = Array(
                                'address' => sanitize_text_field($output[$i]['organizer']['address']),
                                'phone' => sanitize_text_field($output[$i]['organizer']['telephone']),
                                'email' => sanitize_text_field($output[$i]['organizer']['email']),
                                'website' => esc_url_raw($output[$i]['organizer']['url'])
                            );

                            update_post_meta($posts[0]->ID,
                                             'event_organizer',
                                             sanitize_text_field($worker_event_organizer));

                            update_post_meta($posts[0]->ID,
                                             'event_organizer_data',
                                             $organizer_data);

							update_post_meta($posts[0]->ID,
										     'event_status',
											 $output[$i]['eventStatus']);

                            wp_update_post($event_data);

                        }
                    }

                    if (is_null($post))
                    {   
                        $worker_event_category = '';
                        
                        $worker_event_modified = $output[$i]['workPerformed']['dateModified'];

                        $worker_event_start_date = $output[$i]['startDate'];
                        $worker_event_end_date = $output[$i]['endDate'];

                        $worker_event_price = $output[$i]['offers']['price'];
                        $worker_event_website = $output[$i]['sameAs'];

                        $worker_event_location = $output[$i]['location']['address'];
                        $worker_event_location_name = $output[$i]['location']['name'];

                        $worker_event_geolocation = "(".$output[$i]['location']['geo']['latitude']. ", ".$output[$i]['location']['geo']['longitude'].")";

                        $worker_event_organizer = $output[$i]['organizer']['name'];

                        $event_data = array(
                            'post_title' => $output[$i]['name'],
                            'post_content' => $output[$i]['description'],
                            'post_status' => 'publish',
                            'post_type' => 'events'
                        );

                        // Add to database.
                        if ($event_id = wp_insert_post($event_data))
                        {
                            $names = $output[$i]['workPerformed']['keywords'];

                            wp_set_object_terms($event_id, $names, 'event_category');

                            update_post_meta($event_id,
                                             'event_modified',
                                              sanitize_text_field($worker_event_modified));

                            update_post_meta($event_id,
                                             'event_start_date',
                                              sanitize_text_field($worker_event_start_date));
                           
                            update_post_meta($event_id,
                                             'event_end_date',
                                              sanitize_text_field($worker_event_end_date));

                            $ws = new DateTime($worker_event_start_date);

                            update_post_meta($event_id,
                                             'event_start_order',
                                             date_format($ws, 'YmdHi'));

                            $we = new DateTime($worker_event_end_date);

                            update_post_meta($event_id,
                                             'event_end_order',
                                             date_format($we, 'YmdHi'));

                            update_post_meta($event_id,
                                             'event_start_date_filter',
                                             date_format($ws, 'Ymd'));

                            update_post_meta($event_id,
                                             'event_location',
                                             sanitize_text_field($worker_event_location));

                            update_post_meta($event_id,
                                             'event_location_name',
                                             sanitize_text_field($worker_event_location_name));

                            update_post_meta($event_id,
                                             'event_geolocation',
                                             sanitize_text_field($worker_event_geolocation));

                            update_post_meta($event_id,
                                             'event_price',
                                             sanitize_text_field(floatval($worker_event_price)));

                            update_post_meta($event_id,
                                             'event_website',
                                             esc_url_raw($worker_event_website));

                            $organizer_data = Array(
                                'address' => sanitize_text_field($output[$i]['organizer']['address']),
                                'phone' => sanitize_text_field($output[$i]['organizer']['telephone']),
                                'email' => sanitize_text_field($output[$i]['organizer']['email']),
                                'website' => esc_url_raw($output[$i]['organizer']['url'])
                            );

                            update_post_meta($event_id,
                                             'event_organizer',
                                             sanitize_text_field($worker_event_organizer));

                            update_post_meta($event_id,
                                             'event_organizer_data',
                                             $organizer_data);

                            update_post_meta($event_id,
                                             'event_version',
                                             $output[$i]['version']);
											 
														update_post_meta($event_id,
										     'event_status',
											 $output[$i]['eventStatus']);
                        }
                    }
                }
            }
        }

        if (!$query->is_page() && $query->is_post_type_archive("events"))
        {
            // WHY?
        }

        if ($query->is_tax() && !$query->is_page())
        {
            $this->filter_by_date($query);
        }
        if ($query->is_author())
        {
            $this->filter_by_date($query);
        }

        remove_action('pre_get_posts', 'custom_pre_get_posts'); // run once
    }
}
new WorkerPreOrderPosts();

?>