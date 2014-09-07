<?php

class CommonOptions
{
    function __construct()
    {   
        $arr = array('api-endpoint' => 'v01/api');
        $arr2 = array('host-url' => '');

        add_action( 'admin_menu', array( $this, 'add_plugin_settings_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_option( 'event_worker_api_endpoint', $arr, '', 'yes' );
        add_option( 'event_worker_host_url', $arr2, '', 'yes' );
    }
 
    function add_plugin_settings_menu()
    {
        // add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function )
        add_options_page('Event Worker',
                         'Event Worker Options',
                         'manage_options',
                         'event-worker',
                          array($this, 'create_plugin_settings_page'));
    }
 
    function create_plugin_settings_page()
    {
        ?>
        <div class="wrap">
            
            <h2>Settings</h2>         
            <form method="post" action="options.php">

            <?php

                // This prints out all hidden setting fields
                // settings_fields( $option_group )
                settings_fields('settings-group');

                // do_settings_sections( $page )
                do_settings_sections('event-worker');
            ?>

            <?php submit_button('Save Changes'); ?>
            </form>

        </div>

        <?php
    }
 
    function register_settings()
    {
        // add_settings_section( $id, $title, $callback, $page )
        add_settings_section(
            'api-endpoint-settings-section',
            'API Options',
            array($this, 'print_api_endpoint_settings_section_info'),
            'event-worker'
        );
 
        // add_settings_field( $id, $title, $callback, $page, $section, $args )
        add_settings_field(
            'api-endpoint', 
            'Endpoint', 
            array($this, 'create_input_api_endpoint'), 
            'event-worker', 
            'api-endpoint-settings-section'
        );
 
        // register_setting( $option_group, $option_name, $sanitize_callback )
        register_setting( 'settings-group', 'event_worker_api_endpoint', array($this, 'plugin_api_endpoint_settings_validate') );
 
        // add_settings_section( $id, $title, $callback, $page )
        add_settings_section(
            'host-url-settings-section',
            'Host URL',
            array($this, 'print_host_info_settings_section_info'),
            'event-worker'
        );
 
        // add_settings_field( $id, $title, $callback, $page, $section, $args )
        add_settings_field(
            'host-url', 
            'Host URL', 
            array($this, 'create_input_host_url'), 
            'event-worker', 
            'host-url-settings-section'
        );
 
        // register_setting( $option_group, $option_name, $sanitize_callback )
        register_setting( 'settings-group', 'event_worker_host_url', array($this, 'plugin_host_url_settings_validate') );
    }
 
    function print_api_endpoint_settings_section_info()
    {
        echo 'Set the API endpoint.';
    }
 
    function create_input_api_endpoint()
    {   
        $options = get_option('event_worker_api_endpoint');
        //$options['api-endpoint'] = empty($options['api-endpoint']) ? 'v01/api' : $options['api-endpoint'];
        ?><input size="40" type="text" name="event_worker_api_endpoint[api-endpoint]" value="<?php echo $options['api-endpoint']; ?>" /><?php
    }
 
    function plugin_api_endpoint_settings_validate($arr_input)
    {
        $options = get_option('event_worker_api_endpoint');
        $options['api-endpoint'] = trim( $arr_input['api-endpoint'] );
        return $options;
    }
 
    function print_host_info_settings_section_info()
    {
        echo 'Set the URL to fetch events.';
    }
 
    function create_input_host_url()
    {
        $options = get_option('event_worker_host_url');
        ?><input size="40" type="text" name="event_worker_host_url[host-url]" value="<?php echo $options['host-url']; ?>" /><?php
    }

    function plugin_host_url_settings_validate($arr_input)
    {
        $options = get_option('event_worker_host_url');
        $options['host-url'] = trim( $arr_input['host-url'] );
        return $options;
    }
}

?>