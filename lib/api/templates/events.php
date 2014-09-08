<?php

if ($this->data['search'] === null)
{
    get_events();
}
else
{
    get_single_event($this->data['search']);
}

/** 
 * Get TODO.
 *
 * @param TODO.
 *
 * @return TODO.
 *
 */
/*function get_the_api_uri($title)
{
    $uri = 'http' .
           (isset($_SERVER['HTTPS']) ? 's' : '') .
           '://' . "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}" .
           '/' . $title;

    return $uri;
}*/

/** 
 * Get the geolocation from post metadata.
 *
 * @param array $meta the post metadata
 *
 * @return array return the geolocation array
 *
 */
function get_geolocation($meta)
{
    $geolocation = $meta["event_geolocation"];

    $latitude = explode(", ", $geolocation[0]);
    $lat = str_replace("(", "", $latitude[0]);
    $lon = str_replace(")", "", $latitude[1]);

    $arr = array('@type'=>'GeoCoordinates', 'latitude'=>$lat, 'longitude'=>$lon);

    return $arr;
}

/** 
 * Get TODO.
 *
 * @return TODO.
 *
 */
function parse_the_time()
{
    date_default_timezone_set('Europe/Helsinki');
    $today = new DateTime('NOW');
    $today = $today->format('YmdHi');

    return $today;
}

/** 
 * Get single event by search term.
 *
 * @param string $data the passed parameter
 *
 */
function get_single_event($data)
{
    $object = array();

    //$post = get_page_by_title($data, OBJECT, 'events');
    $post = get_page_by_path($data , OBJECT, 'events');

    if ($post !== null && $post->post_status === 'publish')
    {
        $meta = get_post_meta($post->ID);
        $start_date = new DateTime($meta["event_start_date"][0]);
        $end_date = new DateTime($meta["event_end_date"][0]);

        $url = $meta["event_website"][0];
        $price = $meta["event_price"][0];

        $organizer_name = $meta["event_organizer"][0];
        $data = unserialize($meta["event_organizer_data"][0]);

        //$uri = get_the_api_uri($post->post_slug);
        //$uri = substr($uri, 0, -1);

        $holder = null;
        $product_terms = wp_get_object_terms($post->ID, 'event_category');

        foreach($product_terms as $term)
        {
            $holder[] = $term->name; 
        }

        $object = array('@context' => 'http://schema.org',
                        '@id'=>$post->post_name,
                        '@type' => 'Event',
                        'name' => $post->post_title,
                        'startDate'=>$start_date->format('Y-m-d H:i:s'),
                        'endDate'=>$end_date->format('Y-m-d H:i:s'),
                        'description'=>$post->post_content,
                        'sameAs'=>$url,
                        'url'=>get_permalink($post->ID),
                        'keywords'=>array('@type'=> 'CreativeWork', 'keywords'=>$holder),
                        'Date'=>array('@type'=> 'date', 'dateModified'=>$post->post_modified),
                        'offers'=>array('@type'=> 'Offer', 'price'=>$price),
                        'organizer'=>array('@type'=> 'Organization',
                                                         'name'=>$organizer_name,
                                                         'url'=>$data['website'],
                                                         'address'=>$data['address'],
                                                         'email'=>$data['email'],
                                                         'telephone'=>$data['phone']),
                        'location'=>array('@type'=> 'Place', 'geo'=>get_geolocation($meta))
            );
    }
    else
    {
    }

    //$events[] = $object;
    echo json_encode($object);
}

/** 
 * Get all the events.
 *
 */
function get_events()
{
    $args = array(
        'post_type'   => 'events',
        'post_status' => 'publish',
        'numberposts' => -1,
        'meta_key' => 'event_start_date',
        'orderby' => 'meta_value_num'
    );

    $posts = get_posts($args);
    
    $count = count($posts);

    $events = array();

    for ($i = 0; $i < $count; $i++)
    {
        $object = array();

        $meta = get_post_meta($posts[$i]->ID);
        $post =  get_post($posts[$i]->ID);

        $start_date = new DateTime($meta["event_start_date"][0]);
        $end_date = new DateTime($meta["event_end_date"][0]);

        $url = $meta["event_website"][0];
        $price = $meta["event_price"][0];

        $organizer_name = $meta["event_organizer"][0];
        $data = unserialize($meta["event_organizer_data"][0]);

        //$uri = get_the_api_uri($post->post_name);

        $holder = null;
        $product_terms = wp_get_object_terms($posts[$i]->ID, 'event_category');

        foreach($product_terms as $term)
        {
            $holder[] = $term->name; 
        }

        $events[] = array('@id'=>$post->post_name,
                          '@type'=>'Event',
                          'name' => $post->post_title,
                          'description'=>$post->post_content,
                          'startDate'=>$start_date->format('Y-m-d H:i:s'),
                          'endDate'=>$end_date->format('Y-m-d H:i:s'),
                          'sameAs'=>$url,
                          'url'=>get_permalink($post->ID),
                          'keywords'=>array('@type'=> 'CreativeWork', 'keywords'=>$holder),
                          'Date'=>array('@type'=> 'date', 'dateModified'=>$post->post_modified),
                          'offers'=>array('@type'=> 'Offer', 'price'=>$price),
                          'organizer'=>array('@type'=> 'Organization',
                                             'name'=>$organizer_name,
                                             'url'=>$data['website'],
                                             'address'=>$data['address'],
                                             'email'=>$data['email'],
                                             'telephone'=>$data['phone']),
                          'location'=>array('@type'=> 'Place', 'geo'=>get_geolocation($meta),
                                            'address'=>$meta["event_location"][0],
                                            'name'=>$meta["event_location_name"][0]));
    }
    $object = array(
            '@context' => 'http://schema.org',
            '@graph' => $events
    );
    echo json_encode($object);
}

?>