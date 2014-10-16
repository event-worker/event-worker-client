<?php

/**
 * Class for the events archive templage
 *
 * Load the template.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerArchiveEventsTemplate
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_action('wp_footer', array($this, 'append_styles'));
        $this->get_the_template();
    }

     /** 
     * Append the styles.
     *
     */
    function append_styles()
    {
        $wslh = new WorkerClientScriptLoaderHelper();
        $wslh->append_styles();
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
        date_default_timezone_set('Europe/Helsinki');
        $today = new DateTime('NOW');
        $date = new DateTime($date);

        if ($today->format('Ymd') >= $date->format('Ymd'))
        {
            $date = '<div class="today">' . __("TODAY", 'event-worker-translations') . ' ' . $date->format('H:i') . '</div>';
        }
        else
        {
            $date = $date->format('d.m.Y H:i');
        }
        return $date;
    }

    /** 
     * Get the page template.
     *
     */
    function get_the_template()
    {
        get_header();

        $current_url = $_SERVER["REQUEST_URI"];

        echo '<div class="floating-menu">';
        echo '<a href="' . home_url() . '/events' .'">' . __('ALL EVENTS', 'event-worker-translations') . '</a>';
        echo '<a href="' .  str_replace("?filter=today", "", $current_url) . '?filter=today' .'">' . __('EVENTS TODAY', 'event-worker-translations') . '</a>';
        echo '<br>';

        $core = new WorkerClientCore();

        $customPostTaxonomies = get_object_taxonomies('events');

        if(count($customPostTaxonomies) > 0)
        {
            foreach($customPostTaxonomies as $tax)
            {
                $args = array(
                    'orderby' => 'name',
                    'show_count' => 1,
                    'pad_counts' => 0,
                    'hierarchical' => 1,
                    'taxonomy' => $tax,
                    'fields' => 'all',
                    'title_li' => ''
                );

                $categories = get_categories($args);

                $link = null;

                foreach ($categories as $category)
                {
                    $link .= '<a href="' .
                             esc_url(home_url($core->category_slug() .
                             '/' . $category->slug)) .
                             '"> ' . $category->name .
                             '</a>';
                }
            }
            echo $link;
        }

        echo '</div>';

        echo '<div class="eventdivcontainer2">';
        echo '<div class="printbuttoncontainer" align="right">';

        echo '<a href="' . home_url() . '/events.pdf' .'">PDF</a>';
        echo ' | ';
        echo '<a href="' . home_url() . '/events.txt' .'">' . __("PLAIN TEXT", 'event-worker-translations') . '</a>';

        echo '</div>';

        while (have_posts())
        {   
            date_default_timezone_set('Europe/Helsinki');

            the_post();

            $temp_one = get_post_meta(get_the_ID(), 'event_start_date')[0];
            $start = $this->explode_the_date($temp_one);

            $temp_two = get_post_meta(get_the_ID(), 'event_end_date')[0];
            $end = $this->explode_the_date($temp_two);

            $title = get_the_title(get_the_ID());
            $title = strtoupper($title);
            echo '<br>';
            echo '<div id="date_wrapper">';
            echo $start . ' <div>&darr;</div> ' . $end;
            echo '</div>';

            echo '<div id="title_wrapper">';
            echo '<a href="' . get_permalink(get_the_ID()) . '">' . esc_html($title) . '</a>';
            //echo '<div id="ics"><a href="ics.php">' . "ICS" . '</a></div>';
            echo '</div>';

            $lname =  get_post_meta(get_the_ID(), 'event_location_name')[0];

            if ($lname == '')
            {
                $lname = '';
            }
            else if ($lname != '')
            {
                $lname .= ' - ';
            }

            echo '<div id="common_wrapper">';
            echo '<div id="test">' . strtoupper(__("location", 'event-worker-translations'));
            echo '</div>';
            echo $lname . get_post_meta(get_the_ID(), 'event_location')[0];
            echo '</div>';

            echo '<div id="common_wrapper">';
            echo '<div id="test">' . strtoupper(__('category', 'event-worker-translations'));
            echo '</div>';
            echo get_the_term_list(get_the_ID(), 'event_category', '', ' &bull; ', '');
            echo '</div><br>';
        }

        $prev_link = "";

        echo '<div style="text-align:center">';

        previous_posts_link('&laquo; ' . __('Previous', 'event-worker-translations'));

        if(get_previous_posts_link() && get_next_posts_link())
        {
            echo ' | ';
        }

        next_posts_link(__('Next', 'event-worker-translations') .' &raquo;') . '</div><br><br>';

        echo '</div>';
        get_footer();
    }
}
new WorkerArchiveEventsTemplate();

?>