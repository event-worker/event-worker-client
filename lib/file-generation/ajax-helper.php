<?php

/**
 * Helper class for generating the PDF and the plain text files via AJAX.
 *
 * Generate the files via AJAX.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerAjaxHelper
{

    /**
     * The Constructor.
     *
     */
    function __construct()
    {   
        // Register scripts.
        add_action('wp_enqueue_scripts', array( &$this, 'register_plugin_scripts'));
        
        // Include the Ajax library to the front end.
        add_action('wp_head', array( &$this, 'add_ajax_library'));

        // Add file generation action.
        add_action('wp_ajax_generate_files', array(&$this, 'generate'));

    }
  
    /**
     * Adds the WordPress AJAX Library to the frontend
     * if the post type is equal to 'events'.
     *
     */
    public function add_ajax_library()
    {   
        //if (is_post_type_archive('events'))
        //{
            $html = '<script type="text/javascript">';
            $html .= 'var ajaxurl = "' . admin_url('admin-ajax.php') . '"';
            $html .= '</script>';

            echo $html;
        //}
    }
    
    /**
     * Registers scripts but return nothing from the helper yet.
     *
     */
    public function register_plugin_scripts()
    {
        wp_register_script('pdf-generation', plugin_dir_url(__FILE__) . 'js/ajax.js', array('jquery'));
        wp_enqueue_script('pdf-generation');
    }
    
    /**
     * Generate the files.
     *
     */
    public function generate()
    {
        require_once('file-generator.php');
        die();
    }
}
new WorkerAjaxHelper();

?>