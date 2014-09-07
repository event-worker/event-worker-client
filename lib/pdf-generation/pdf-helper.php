<?php

class PDFHelper
{
    /**
     * The Constructor.
     *
     */
    function __construct()
    {
        // Register scripts.
        add_action( 'wp_enqueue_scripts', array( &$this, 'register_plugin_scripts' ) );
        
        // Include the Ajax library on the front end.
        add_action( 'wp_head', array( &$this, 'add_ajax_library' ) );

        add_action( 'wp_ajax_generate_pdf', array( &$this, 'generate' ) );
        add_action( 'wp_ajax_generate_pdf', array( &$this, 'generate' ) );
    }
  
    /**
     * Adds the WordPress Ajax Library to the frontend.
     *
     */
    public function add_ajax_library()
    {
        $html = '<script type="text/javascript">';
        $html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
        $html .= '</script>';

        echo $html;
    }
    
    /**
     * Registers and enqueues plugin-specific scripts.
     *
     */
    public function register_plugin_scripts()
    {
        wp_register_script( 'pdf-generation', plugin_dir_url(__FILE__) . '/js/pdf-helper.js', array( 'jquery' ) );
        wp_enqueue_script( 'pdf-generation' );
    }
    
    /**
     * TODO
     *
     */
    public function generate()
    {
        require(WP_PLUGIN_DIR . '/' . 'event-worker-client/lib/pdf-generator.php');
        die();
    }
}

new PDFHelper();
?>