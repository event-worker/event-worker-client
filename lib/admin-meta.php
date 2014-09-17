<?php

/**
 * Class for adding the metaboxes to admin.
 *
 * Add custom metaboxes to admin.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerAdminMetaBoxes
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_action('save_post', array($this,'custom_save'));
        add_action('add_meta_boxes', array($this, 'add_my_meta_boxes'));
    }

    /** 
     * Add the fields.
     *     
     */
    function add_my_meta_boxes()
    {
        add_meta_box('organizer-meta-box', ucfirst(__( 'organizer', 'event-worker-translations' )), array($this, 'show_organizer_meta_box'), 'events', 'normal', 'high');
        add_meta_box('website-meta-box', ucfirst(__( 'website', 'event-worker-translations' )), array($this, 'show_website_meta_box'), 'events', 'normal', 'high');
        add_meta_box('price-meta-box', ucfirst(__( 'price', 'event-worker-translations' )), array($this, 'show_price_meta_box'), 'events', 'normal', 'high');
        add_meta_box('date-meta-box', ucfirst(__( 'event date', 'event-worker-translations' )), array($this, 'show_date_meta_box'), 'events', 'normal', 'high');
        add_meta_box('map-meta-box', ucfirst(__( 'location', 'event-worker-translations' )), array($this, 'show_map_meta_box'), 'events', 'normal', 'high');
    }

    /** 
     * Show the map and the location fields.
     *     
     */
    function show_map_meta_box()
    {
        $name = 'event_worker_map_nonce'; // Make sure this is unique, prefix it with your plug-in/theme name
        $action = 'event_worker_action_xyz_' . get_the_ID(); // This is the nonce action

        wp_nonce_field($action, $name);
        $count = count(get_post_meta(get_the_ID(), 'event_location'));

        if ($count !== 0)
        {
            $l =  get_post_meta(get_the_ID(), 'event_location')[0];
            $n =  get_post_meta(get_the_ID(), 'event_location_name')[0];
        }
        else
        {
            $l =  null;
            $n =  null;
        }

        echo '<input style="width:100%;" placeholder="' . ucfirst(__( 'name', 'event-worker-translations' )) . '"' . 
             'name="worker_event_location_name" value="' . esc_attr($n) . '"/><br/>';

        echo '<input style="width:100%;" id="worker_event_location" placeholder="' . ucfirst(__( 'address', 'event-worker-translations' )) . '"' .
             'name="worker_event_location" value="' . esc_attr($l) . '"/><br/>';

        echo '<input type="hidden" id="worker_event_geolocation" name="worker_event_geolocation" value=""/>';

        echo '<div id="googleMap" style="width: 100%; height: 300px"></div>';

        $wslh = new WorkerScriptLoaderHelper();
        $wslh->getMap($l);
    }

    /** 
     * Explode the date.
     *
     * @param string $date the date.
     *
     * @return string
     *
     */
    function explode_the_date($date)
    {
        $date = new DateTime($date);
        $date = $date->format('d.m.Y H:i');

        return $date;
    }

    /** 
     * Show the date fields.
     *     
     */
    function show_date_meta_box()
    {   
        $name = 'event_worker_date_nonce'; // Make sure this is unique, prefix it with your plug-in/theme name
        $action = 'event_worker_action_xyz_' . get_the_ID(); // This is the nonce action

        wp_nonce_field($action, $name);

        $count = count(get_post_meta(get_the_ID(), 'event_start_date'));
        $count2 = count(get_post_meta(get_the_ID(), 'event_end_date'));

        if ($count !== 0 or $count2 !== 0)
        {
            $temp_one = get_post_meta(get_the_ID(), 'event_start_date')[0];
            $start = $this->explode_the_date($temp_one);

            $temp_two = get_post_meta(get_the_ID(), 'event_end_date')[0];
            $end = $this->explode_the_date($temp_two);
        }
        else
        {
            $start = null;
            $end = null;
        }

        echo '<label for="AdminEventStartDate">' . ucfirst(__( 'start date', 'event-worker-translations' )) . '</label><br/>';   
        echo '<input class="eventdate" id="AdminEventStartDate" name="AdminEventStartDate" value="' . esc_attr($start) . '"/><br/>';

        echo '<label for="AdminEventEndDate">' . ucfirst(__( 'end date', 'event-worker-translations' )) . '</label><br/>';
        echo '<input class="eventdate" id="AdminEventEndDate" name="AdminEventEndDate" value="' . esc_attr($end) . '"/><br/>';
    }

    /** 
     * Show the price field.
     *     
     */
    function show_price_meta_box()
    {   
        $name = 'event_worker_price_nonce'; // Make sure this is unique, prefix it with your plug-in/theme name
        $action = 'event_worker_action_xyz_' . get_the_ID(); // This is the nonce action

        wp_nonce_field($action, $name);

        $count = count(get_post_meta(get_the_ID(), 'event_price'));

        if ($count !== 0)
        {            
            $price = get_post_meta(get_the_ID(), 'event_price')[0];
        }
        else
        {
            $price = null;
        }
        echo '<input type="text" class="eventprice" id="AdminEventPrice" name="AdminEventPrice" onkeypress="return isNumberKey(event)" value="' . esc_attr($price) . '"/><br/>';
    }

    /** 
     * Show the website field.
     *     
     */
    function show_website_meta_box()
    {
        $name = 'event_worker_website_nonce'; // Make sure this is unique, prefix it with your plug-in/theme name
        $action = 'event_worker_action_xyz_' . get_the_ID(); // This is the nonce action

        wp_nonce_field($action, $name);

        $count = count(get_post_meta(get_the_ID(), 'event_website'));

        if ($count !== 0)
        {            
            $website = get_post_meta(get_the_ID(), 'event_website')[0];
        }
        else
        {
            $website = null;
        }

        echo '<label for="AdminEventWebsite">URL</label><br/>';
        echo '<input <input type="url" class="eventwebsite" id="AdminEventWebsite" name="AdminEventWebsite" value="' . esc_attr($website) . '"/><br/>';
    }

    /** 
     * Show the organizer field.
     *     
     */
    function show_organizer_meta_box()
    {   
        $name = 'event_worker_organizer_nonce'; // Make sure this is unique, prefix it with your plug-in/theme name
        $action = 'event_worker_action_xyz_' . get_the_ID(); // This is the nonce action

        wp_nonce_field($action, $name);
        $count = count(get_post_meta(get_the_ID(), 'event_organizer'));

        if ($count !== 0)
        {            
            $organizer = get_post_meta(get_the_ID(), 'event_organizer')[0];
            $organizer_address = get_post_meta(get_the_ID(), 'event_organizer_data')[0]['address'];
            $organizer_phone = get_post_meta(get_the_ID(), 'event_organizer_data')[0]['phone'];
            $organizer_email = get_post_meta(get_the_ID(), 'event_organizer_data')[0]['email'];
            $organizer_website = get_post_meta(get_the_ID(), 'event_organizer_data')[0]['website'];
        }
        else
        {
            $organizer = wp_get_current_user()->display_name;
            $organizer_address = '';
            $organizer_phone = '';
            $organizer_email ='';
            $organizer_website = '';
        }

        echo '<input type="text" class="auto" name="AdminEventOrganizer" placeholder="' . ucfirst(__( 'name', 'event-worker-translations' )) . '"value="' . esc_attr($organizer) . '" style="width: 100%;"/><br/>';
        echo '<input type="text" id="organizer_address" name="organizer_address" placeholder="' . ucfirst(__( 'address', 'event-worker-translations' )) . '" value="' .  esc_attr($organizer_address) . '" style="width: 100%;"/>';
        echo '<input type="text" id="organizer_phone" name="organizer_phone" placeholder="' . ucfirst(__( 'phone', 'event-worker-translations' )) . '" value="' .  esc_attr($organizer_phone) . '" style="width: 100%;"/><br/>';
        echo '<input type="text" id="organizer_email" name="organizer_email" placeholder="' . ucfirst(__( 'e-mail', 'event-worker-translations' )) . '" value="' .  esc_attr($organizer_email) . '" style="width: 100%;"/>';
        echo '<input type="text" id="organizer_website" name="organizer_website" placeholder="' . ucfirst(__( 'website', 'event-worker-translations' )) . '" value="' .  esc_attr($organizer_website) . '" style="width: 100%;"/>';
    }

    /** 
     * Save the data.
     *
     * @param int $post_id the post id.
     *
     */
    function custom_save($post_id)
    {   
        // Check its not an auto save.
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
            return;
     
        // Check your data has been sent - this helps verify that we intend to process our metabox.
        if ( !isset($_POST['event_worker_website_nonce']) &&
             !isset($_POST['event_worker_organizer_nonce']) )
            return;
     
        // Check permissions.
        if ( !current_user_can('edit_post', $post_id) )
            return;
     
        // Finally check the nonces.
        check_ajax_referer('event_worker_action_xyz_' . $post_id, 'event_worker_map_nonce');
        check_ajax_referer('event_worker_action_xyz_' . $post_id, 'event_worker_date_nonce');
        check_ajax_referer('event_worker_action_xyz_' . $post_id, 'event_worker_price_nonce');
        check_ajax_referer('event_worker_action_xyz_' . $post_id, 'event_worker_website_nonce');
        check_ajax_referer('event_worker_action_xyz_' . $post_id, 'event_worker_organizer_nonce');

        if(isset($_POST['AdminEventStartDate']) && isset($_POST['AdminEventEndDate']))
        {
            $worker_event_start_date = $_POST['AdminEventStartDate'];
            $worker_event_end_date = $_POST['AdminEventEndDate'];

            $worker_event_location = trim($_POST['worker_event_location']);
            $worker_event_location_name = trim($_POST['worker_event_location_name']);
            $worker_event_geolocation = trim($_POST['worker_event_geolocation']);

            $worker_event_price = trim($_POST['AdminEventPrice']);

            $worker_event_website = trim($_POST['AdminEventWebsite']);

            $worker_event_organizer = trim($_POST['AdminEventOrganizer']);

            update_post_meta($post_id,
                             'event_start_date',
                             sanitize_text_field($worker_event_start_date));
            update_post_meta($post_id,
                             'event_end_date',
                             sanitize_text_field($worker_event_end_date));

            update_post_meta($post_id,
                             'event_location',
                             sanitize_text_field($worker_event_location));

            update_post_meta($post_id,
                             'event_location_name',
                             sanitize_text_field($worker_event_location_name));

            update_post_meta($post_id,
                             'event_geolocation',
                             sanitize_text_field($worker_event_geolocation));

            update_post_meta($post_id,
                             'event_price',
                             sanitize_text_field(floatval($worker_event_price)));

            update_post_meta($post_id,
                             'event_website',
                             esc_url_raw($worker_event_website));

            update_post_meta($post_id,
                             'event_organizer',
                             sanitize_text_field($worker_event_organizer));

            $organizer_data = Array(
                'address' => sanitize_text_field($_POST['organizer_address']),
                'phone' => sanitize_text_field($_POST['organizer_phone']),
                'email' => sanitize_email($_POST['organizer_email']),
                'website' => esc_url_raw($_POST['organizer_website'])
            );

            update_post_meta($post_id,
                             'event_organizer_data',
                             $organizer_data);

            $ws = new DateTime('NOW');
            $we = new DateTime('NOW');

            if ($worker_event_start_date !== "")
            {
                $ws = new DateTime($worker_event_start_date);
            }
            if ($worker_event_end_date !== "")
            {
                 $we = new DateTime($worker_event_end_date);
            }

            update_post_meta($post_id,
                             'event_start_order',
                             date_format($ws, 'YmdHi'));

            update_post_meta($post_id,
                             'event_end_order',
                             date_format($we, 'YmdHi'));
        }
    }
}
new WorkerAdminMetaBoxes();

?>