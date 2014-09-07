<?php

/**
 * Class for adding the field to admin events.
 *
 * Add custom metaboxes (fields) to admin events.
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
        add_meta_box('organizer-meta-box', 'Organizer', array($this, 'show_organizer_meta_box'), 'events', 'normal', 'high');
        add_meta_box('website-meta-box', 'Website', array($this, 'show_website_meta_box'), 'events', 'normal', 'high');
        add_meta_box('price-meta-box', 'Price', array($this, 'show_price_meta_box'), 'events', 'normal', 'high');
        add_meta_box('date-meta-box', 'Date', array($this, 'show_date_meta_box'), 'events', 'normal', 'high');
        add_meta_box('map-meta-box', 'Location', array($this, 'show_map_meta_box'), 'events', 'normal', 'high');
    }

    /** 
     * Show the map and the location fields.
     *     
     */
    function show_map_meta_box()
    {
        $count = count(get_post_meta(get_the_ID(), 'event_location'));

        if ($count !== 0)
        {
            $l =  get_post_meta(get_the_ID(), 'event_location')[0];
            $n =  get_post_meta(get_the_ID(), 'event_location_name')[0];
            $g =  get_post_meta(get_the_ID(), 'event_geolocation')[0];
        }
        else
        {
            $l =  null;
            $n =  null;
            $g = null;
        }

        echo '<input style="width:100%;" placeholder="Name"' . 
             'name="worker_event_location_name" value="' . $n . '"/><br/>';

        echo '<input style="width:100%;" id="worker_event_location" placeholder="Address"' .
             'name="worker_event_location" value="' . $l . '"/><br/>';

        echo '<input type="hidden" id="worker_event_geolocation" name="worker_event_geolocation" value=""/>';

        echo '<div id="googleMap" style="width: 100%; height: 300px"></div>';

        $wslh = new WorkerScriptLoaderHelper();
        $wslh->getMap($l);
    }

    /** 
     * Explode the date.
     *
     * @param string $date the date as a string
     *
     * @return string $date return the date and time
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

        echo '<label for="AdminEventStartDate">Start date</label><br/>';   
        echo '<input class="eventdate" id="AdminEventStartDate" name="AdminEventStartDate" value="' . $start. '"/><br/>';

        echo '<label for="AdminEventEndDate"> End date</label><br/>';
        echo '<input class="eventdate" id="AdminEventEndDate" name="AdminEventEndDate" value="' . $end . '"/><br/>';
    }

    /** 
     * Show the price field.
     *     
     */
    function show_price_meta_box()
    {
        $count = count(get_post_meta(get_the_ID(), 'event_price'));

        if ($count !== 0)
        {            
            $price = get_post_meta(get_the_ID(), 'event_price')[0];
        }
        else
        {
            $price = null;
        }

        echo '<label for="AdminEventPrice">Price</label><br/>';   
        echo '<input type="number" class="eventprice" id="AdminEventPrice" name="AdminEventPrice" onkeypress="return isNumberKey(event)" value="' . $price . '"/><br/>';
    }

    /** 
     * Show the website field.
     *     
     */
    function show_website_meta_box()
    {
        $count = count(get_post_meta(get_the_ID(), 'event_website'));

        if ($count !== 0)
        {            
            $website = get_post_meta(get_the_ID(), 'event_website')[0];
        }
        else
        {
            $website = null;
        }

        echo '<label for="AdminEventWebsite">Website</label><br/>';
        echo '<input <input type="url" class="eventwebsite" id="AdminEventWebsite" name="AdminEventWebsite" value="' . $website . '"/><br/>';
    }

    /** 
     * Show the organizer field.
     *     
     */
    function show_organizer_meta_box()
    {
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

        echo '<label for="AdminEventOrganizer">Organizer</label><br/>';
        echo '<input type="text" class="auto" name="AdminEventOrganizer" value="' . $organizer . '" style="width: 100%;"/><br/>';
        echo '<input type="text" id="organizer_address" name="organizer_address" placeholder="Address" value="' .  $organizer_address . '" style="width: 100%;"/>';
        echo '<input type="text" id="organizer_phone" name="organizer_phone" placeholder="Phone" value="' .  $organizer_phone . '" style="width: 100%;"/><br/>';
        echo '<input type="text" id="organizer_email" name="organizer_email" placeholder="E-mail" value="' .  $organizer_email . '" style="width: 100%;"/>';
        echo '<input type="text" id="organizer_website" name="organizer_website" placeholder="Website" value="' .  $organizer_website . '" style="width: 100%;"/>';
    }

    /** 
     * Save the data.
     *
     * @param int $post_id the post id
     *
     */
    function custom_save($post_id)
    {
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

            update_post_meta($post_id, 'event_start_date', $worker_event_start_date);
            update_post_meta($post_id, 'event_end_date', $worker_event_end_date);

            update_post_meta($post_id,
                             'event_location',
                             $worker_event_location);

            update_post_meta($post_id,
                             'event_location_name',
                             $worker_event_location_name);

            update_post_meta($post_id,
                             'event_geolocation',
                             $worker_event_geolocation);

            update_post_meta($post_id,
                             'event_price',
                             $worker_event_price);

            update_post_meta($post_id,
                             'event_website',
                             $worker_event_website);

            $organizer_data = Array(
                'address' => trim($_POST['organizer_address']),
                'phone' => trim($_POST['organizer_phone']),
                'email' => trim($_POST['organizer_email']),
                'website' => trim($_POST['organizer_website'])
            );

            update_post_meta($post_id,
                             'event_organizer',
                             $worker_event_organizer);

            update_post_meta($post_id,
                             'event_organizer_data',
                             $organizer_data);

            if ($worker_event_start_date !== "")
            {
                $start_order = new WorkerFormatDate($worker_event_start_date);
                $only_digits = $start_order->$worker_event_start_date;

                $end_order = new WorkerFormatDate($worker_event_end_date);
                $only_digits2 = $end_order->$worker_event_end_date;
            }

            update_post_meta($post_id,
                             'event_start_order',
                             $only_digits);

            update_post_meta($post_id,
                             'event_end_order',
                             $only_digits2);
        }
    }
}
new WorkerAdminMetaBoxes();

?>