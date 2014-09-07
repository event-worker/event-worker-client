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
        else // not available
        {
        }
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
     * Get the page template.
     *
     */
    function get_the_template()
    {   
        get_header();

        $title = get_the_title(get_the_ID());

        echo '<div class="eventdivcontainer" align="center">';

        the_post();
        
        $temp_one = get_post_meta(get_the_ID(), 'event_start_date')[0];
        $start = $this->explode_the_date($temp_one);

        $temp_two = get_post_meta(get_the_ID(), 'event_end_date')[0];
        $end = $this->explode_the_date($temp_two);

        echo '<table style="width:100%">';

        echo '<tr><td colspan="2" class="eventtitlecontainer"><h2>
                           <a href="' . get_permalink(get_the_ID()) . '">' .
                           $title . '</a></h2></td></tr>';

        echo '<tr><td class="eventtablecontainer">' . __('date', 'event-worker-translations') . '/' . __('time', 'event-worker-translations') . '</td><td class="eventtablecontainersecond">' . $start .
             '<strong> &rarr; </strong>' .
             $end . '</td></tr>';    

        echo '<tr><td class="eventtablecontainer">' . __('price', 'event-worker-translations') . '</td><td class="eventtablecontainersecond">' . get_post_meta(get_the_ID(), 'event_price')[0] . '&#8364;</td></tr>';
        echo '<tr><td class="eventtablecontainer">' . __('category', 'event-worker-translations') . '</td><td class="eventtablecontainersecond">';

        $this->check();

        echo '</td></tr>';

        $data = '';
        $data2 = '';

        if (get_post_meta(get_the_ID(), 'event_organizer_data')[0]['address'] !== '')
        {
            $data = get_post_meta(get_the_ID(), 'event_organizer_data')[0]['address'] . '  ';
        }
        if (get_post_meta(get_the_ID(), 'event_organizer_data')[0]['phone'] !== '')
        {
            $data .= get_post_meta(get_the_ID(), 'event_organizer_data')[0]['phone'] . '  ';
        }
        if (get_post_meta(get_the_ID(), 'event_organizer_data')[0]['email'] !== '')
        {
            $data2 .= get_post_meta(get_the_ID(), 'event_organizer_data')[0]['email'] . '  ';
        }
        if (get_post_meta(get_the_ID(), 'event_organizer_data')[0]['website'] !== '')
        {
            $data2 .= get_post_meta(get_the_ID(), 'event_organizer_data')[0]['website'] . '  ';
        }
        else
        {
            $data = '';
            $data2 = '';
        }

        if ($data !== '' && $data2 !== '')
        {
            $sep = '<br>';
        }
        else
        {
            $sep = '';
        }
       
        $data = preg_replace( '/\s\s+/', ', ', $data, preg_match_all( '/\s\s+/', $data) - 1);
        $data2 = preg_replace( '/\s\s+/', ', ', $data2, preg_match_all( '/\s\s+/', $data2) - 1);

        echo '<tr><td class="eventtablecontainer">'. __('website', 'event-worker-translations') . '</td>' . '<td class="eventtablecontainersecond"><a href="' . get_post_meta(get_the_ID(), 'event_website')[0] . '">' . get_post_meta(get_the_ID(), 'event_website')[0] . '</td></tr>';
        echo '<tr><td class="eventtablecontainer">'. __('organizer', 'event-worker-translations') . '</td><td class="eventtablecontainersecond">' . get_post_meta(get_the_ID(), 'event_organizer')[0] . '</a><br>' . 
        $data . $sep . $data2 . '</td></tr>';

        $lname =  get_post_meta(get_the_ID(), 'event_location_name')[0];

        if ($lname == '')
        {
            $lname = '';
        }
        else if ($lname != '')
        {
            $lname .= ' - ';
        }

        echo '<tr><td class="eventtablecontainer">' . __('location', 'event-worker-translations') . '</td><td class="eventtablecontainersecond">' .
             $lname .
             get_post_meta(get_the_ID(), 'event_location')[0] . '</td></tr>';
            
        $wslh = new WorkerScriptLoaderHelper();

        $wslh->getMap(get_post_meta(get_the_ID(), 'event_location')[0]);

        ob_start();
        the_content();
        $content = ob_get_clean();

        echo '<tr><td colspan="2" class="eventcontentcontainer">' . $content . '</td></tr>';

        echo '</table>';

        echo'<div id="googleMap" style="width: 100%; height: 300px"></div>';

        echo '</div>';

        echo '<div style="text-align:center">';

        $args = array(
            'orderby' => 'event-start-date',
            'post_type' => 'events',
            'post_status' => 'publish',
            'numberposts' => -1
        );

        $pagelist = get_posts($args);
        $pages = array();

        foreach ($pagelist as $page)
        {
            $pages[] += $page->ID;
        }

        $current = array_search(get_the_ID(), $pages);

        if ($current !== 0 && !is_preview())
        {
            $prevID = $pages[$current-1];
            $prev = __("Previous", 'event-worker-translations');

            echo '<a href="' . get_permalink($prevID) . '" ' .
                 'title="' . get_the_title($prevID) . '">&laquo; ' . $prev . '</a>';
        }
        if (count($pagelist) > 1 && !is_preview())
        {
            echo ' | ';
        }
        if ($current !== count($pages)-1 && !is_preview())
        {        
            $nextID = $pages[$current+1];
            $next = __("Next", 'event-worker-translations');

            echo '<a href="' . get_permalink($nextID) . '" ' .
                 'title="' . get_the_title($nextID) . '">' . $next . ' &raquo;</a>';
        }

        echo '</div><br><br>';
        get_footer();
    }
}
new WorkerSingleEventTemplate();

?>