<?php

/**
 * Class for the single event template
 *
 * Load the page template.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerSingleEventTemplate
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        $this->get_the_template();
    }

    /** 
     * Check if the category is available.
     *
     */
    function check()
    {
        if (taxonomy_exists('event_category')) // is available
        {
            //$terms = get_terms('event_category'));
            echo get_the_term_list(get_the_ID(), 'event_category', ' ', ' &bull; ', ' ');
        }
        else
        {
        }
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
     * Get the page template.
     *
     */
    function get_the_template()
    {   
        get_header();

        $title = get_the_title(get_the_ID());

        echo '<div class="eventdivcontainer" align="center">';

        the_post();
        
        $var = get_post_meta(get_the_ID());
        
        $temp_one = $var['event_start_date'][0];
        $start = $this->explode_the_date($temp_one);

        $temp_two = $var['event_end_date'][0];
        $end = $this->explode_the_date($temp_two);

        echo '<table style="width:100%">';

        echo '<tr><td colspan="2" class="eventtitlecontainer"><h2>
                           <a href="' . esc_url(get_permalink(get_the_ID())) . '">' .
                           esc_attr($title) . '</a></h2></td></tr>';

        echo '<tr><td class="eventtablecontainer">' . __('date', 'event-worker-translations') . '/' . __('time', 'event-worker-translations') . '</td><td class="eventtablecontainersecond">' . esc_attr($start) .
             '<strong> &rarr; </strong>' .
             esc_attr($end) . '</td></tr>';

        echo '<tr><td class="eventtablecontainer">' . __('price', 'event-worker-translations') . '</td><td class="eventtablecontainersecond">' . esc_attr($var['event_price'][0]) . '&#8364;</td></tr>';
        echo '<tr><td class="eventtablecontainer">' . __('category', 'event-worker-translations') . '</td><td class="eventtablecontainersecond">';

        $this->check();

        echo '</td></tr>';

        $data = '';
        $data2 = '';
        
        $odata = $var['event_organizer_data'];
        $odata = unserialize($odata[0]);

        if ($odata['address'] !== '')
        {
            $data = esc_attr($odata['address']) . '  ';
        }
        if ($odata['website'] !== '')
        {
            $data .= '<a href="' . esc_url($odata['website']) . '">' . esc_url($odata['website']) . '</a>  ';
        }
        if ($odata['email'] !== '')
        {
            $data2 .= $odata['email'] . '  ';
        }
        if ($odata['phone'] !== '')
        {
            $data2 .= $odata['phone'] . '  ';
        }

        if ($data !== '' && $data2 !== '')
        {
            $sep = '<br>';
        }
        else
        {
            $sep = '';
        }
        
        $dummy = array();
        $data = preg_replace( '/\s\s+/', ', ', $data, preg_match_all( '/\s\s+/', $data, $dummy) - 1);
        $data2 = preg_replace( '/\s\s+/', ', ', $data2, preg_match_all( '/\s\s+/', $data2, $dummy) - 1);

        echo '<tr><td class="eventtablecontainer">'. __('website', 'event-worker-translations') . '</td>' . '<td class="eventtablecontainersecond"><a href="' . esc_url($var['event_website'][0]) . '">' . esc_url($var['event_website'][0]) . '</a></td></tr>';
        echo '<tr><td class="eventtablecontainer">'. __('organizer', 'event-worker-translations') . '</td><td class="eventtablecontainersecond">' . esc_attr($var['event_organizer'][0]) . '<br>' . 
        $data . $sep . esc_attr($data2) . '</td></tr>';

        $lname =  $var['event_location_name'][0];

        if ($lname == '')
        {
            $lname = '';
        }
        else if ($lname != '')
        {
            $lname .= ' - ';
        }

        echo '<tr><td class="eventtablecontainer">' . __('location', 'event-worker-translations') . '</td><td class="eventtablecontainersecond">' .
             esc_attr($lname) .
             esc_attr($var['event_location'][0]) . '</td></tr>';
            
        $wslh = new WorkerClientScriptLoaderHelper();

        $wslh->getMapOnly($var['event_location'][0]);

        ob_start();
        the_content();
        $content = ob_get_clean();

        echo '<tr><td colspan="2" class="eventcontentcontainer">' . $content . '</td></tr>';

        echo '</table>';

        echo'<div id="googleMap" style="width: 100%; height: 300px"></div>';

        echo '</div>';

        echo '<div style="text-align:center">';

        $args = array(
            'orderby'     => 'meta_value',
            'meta_key'    =>'event_start_date',
            'order'       => 'ASC',
            'post_type'   => 'events',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_query'  => array(array('value' => 'http://schema.org/EventScheduled'))
        ); 

        $pagelist = get_posts($args);
        $pages = array();

        foreach ($pagelist as $page)
        {
            $pages[] += $page->ID;
        }

        $current = array_search(get_the_ID(), $pages);
        
        if ($current !== false)
        {       
            if ($current !== 0 && !is_preview())
        {
            $prevID = $pages[$current-1];
            $prev = __("Previous", 'event-worker-translations');

            echo '<a href="' . esc_url(get_permalink($prevID)) . '" ' .
                 'title="' . esc_attr(get_the_title($prevID)) . '">&laquo; ' . esc_attr($prev) . '</a>';
        }
        if ($current !== count($pages)-1 && !is_preview())
        {
            if (isset($prev))
            {
                echo ' | ';
            }

            $nextID = $pages[$current+1];
            $next = __("Next", 'event-worker-translations');

            echo '<a href="' . esc_url(get_permalink($nextID)) . '" ' .
                 'title="' . esc_attr(get_the_title($nextID)) . '">' . esc_attr($next) . ' &raquo;</a>';
        }
        }
        

        echo '</div><br><br>';
        get_footer();
    }
}
new WorkerSingleEventTemplate();

?>