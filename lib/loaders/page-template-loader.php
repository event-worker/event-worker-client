<?php

/**
 * Class for loading the templates.
 *
 * load the template for events archive, single event and add events page.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerClientTemplateLoader
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_filter('single_template', array($this, 'single_event_template'));
        add_filter('template_redirect', array($this, 'archive_event_template'));
    }

    /** 
     * Get the archive template.
     *
     */
    function archive_event_template()
    {   
        $dir = dirname( __FILE__ ) . '/../templates/archive-events.php';

        if ( /*(is_front_page() && !is_page()) || */
             (!is_page() && is_post_type_archive("events")) ||
             (is_tax() && !is_page()) ||
             //(is_search()) ||
             (is_author())
           )
        {
            include_once($dir);
            exit();
        }
    }

    /** 
     * Get the single event template.
     *
     * @param string $single_template the template
     *
     * @return string
     *
     */
    function single_event_template($single_template)
    {
        if (is_singular('events'))
        {
            $single_template = dirname( __FILE__ ) .
                               '/../templates/single-events.php';
        }
        return $single_template;
    }
}
new WorkerClientTemplateLoader();

?>