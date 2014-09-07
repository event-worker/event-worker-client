<?php

/** 
 * Get all the Categories
 *
 */
function get_all_categories()
{
    $events = array();
    $object = array();

    $orderby = 'name';
    $show_count = 0;
    $pad_counts = 0;
    $hierarchical = 0;
    $taxonomy = 'event_category';
    $title = '';

    $args = array(  
      'orderby' => $orderby,
      'style' => 'none',
      'show_count' => $show_count,
      'pad_counts' => $pad_counts,
      'hierarchical' => $hierarchical,
      'taxonomy' => $taxonomy,
      'title_li' => $title,
      'echo' => 0
    );

    $cats = wp_list_categories($args);
    $cats = explode('<br />', $cats);

    for ($i = 0; $i < count($cats)-1; $i++)
    {
        $cat = explode('">', $cats[$i]);

        $title = strip_tags($cat[1]);
        $link = explode(" ", $cat[0]);

        $r = array('href="', '"');
        $url = str_replace($r, '', $link[1]);

        $object = array(
                    '@context' => 'http://schema.org',
                    '@type' => 'CreativeWork',
                    'keywords'=>array('@type'=> 'CreativeWork', 'name'=>$title, 'url'=>$url)
                    );

        $events[] = $object;
    }

    echo json_encode($events);
}

get_all_categories();

?>