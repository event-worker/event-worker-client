<?php

class IveReadThis
{
    
    /**
     * TODO.
     *
     */
    function __construct()
    {
        //load_plugin_textdomain( 'ive-read-this', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
    
        // Register scripts
        add_action( 'wp_enqueue_scripts', array( &$this, 'register_plugin_scripts' ) );
        
        // Include the Ajax library on the front end
        add_action( 'wp_head', array( &$this, 'add_ajax_library' ) );
        
        // Setup the event handler for marking this post as read for the current user
        add_action( 'wp_ajax_mark_as_read', array( &$this, 'mark_as_read' ) );
        add_action( 'wp_ajax_nopriv_mark_as_read', array( &$this, 'mark_as_read' ) );
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
        wp_register_script( 'pdf-generation', plugin_dir_url(__FILE__) . '/js/plugin.js', array( 'jquery' ) );
        wp_enqueue_script( 'pdf-generation' );
    
    }
    
    /**
     * TODO
     *
     */
    public function mark_as_read()
    {
        require(WP_PLUGIN_DIR . '/' . 'event-worker/lib/pdf-generator.php');
        echo "hello";

        if( isset($_POST['data']) )
        {            
            echo $_POST['data'];
        }
        die();
    }
}

new IveReadThis();
?>