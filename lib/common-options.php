<?php

/**
 * Class the common options.
 *
 * The options page for the common/shared options of the plugin.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerCommonOptions
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {   
        $arr = array('api-endpoint' => 'v01/api');
        $arr2 = array('host-url' => '');

        add_action('admin_menu', array( $this, 'add_plugin_settings_menu'));
        add_action('admin_init', array( $this, 'register_settings'));
        add_option('event_worker_api_endpoint', $arr, '', 'yes');
        add_option('event_worker_host_url', $arr2, '', 'yes');
    }
 
    /** 
     * Add the link to the settings menu.
     *
     * add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function )
     *
     */
    function add_plugin_settings_menu()
    {
        add_options_page('Event Worker',
                         __('Event Worker Options', 'event-worker-translations'),
                         'manage_options',
                         'event-worker',
                         array($this, 'create_plugin_settings_page'));
    }
 
    /** 
     * Add the link to the settings menu.
     *
     * settings_fields($option_group)
     * do_settings_sections($page)
     *
     */
    function create_plugin_settings_page()
    {
        ?>
        <div class="wrap">

            <h2><?php _e('Settings', 'event-worker-translations'); ?></h2>
            <form method="post" action="options.php">

            <?php
                settings_fields('settings-group');  // This prints out all hidden setting fields

                do_settings_sections('event-worker');
                submit_button(__('Save Changes', 'event-worker-translations'));
            ?>
            </form>

        </div>

        <?php
    }

    /** 
     * Register the settings/options.
     *
     * add_settings_section($id, $title, $callback, $page)
     * add_settings_field($id, $title, $callback, $page, $section, $args)
     * register_setting($option_group, $option_name, $sanitize_callback)
     *
     */
    function register_settings()
    {
        add_settings_section(
            'api-endpoint-settings-section',
            __('API Options', 'event-worker-translations'),
            array($this, 'print_api_endpoint_settings_section_info'),
            'event-worker'
        ); 
       
        add_settings_field(
            'api-endpoint', 
            __('Endpoint', 'event-worker-translations'),
            array($this, 'create_input_api_endpoint'), 
            'event-worker', 
            'api-endpoint-settings-section'
        );

        register_setting('settings-group',
                         'event_worker_api_endpoint',
                         array($this, 'plugin_api_endpoint_settings_validate'));

        add_settings_section(
            'host-url-settings-section',
            __('Host URL Options', 'event-worker-translations'),
            array($this, 'print_host_url_settings_section_info'),
            'event-worker'
        ); 

        add_settings_field(
            'host-url', 
            'URL', 
            array($this, 'create_input_host_url'), 
            'event-worker', 
            'host-url-settings-section'
        );

        register_setting('settings-group',
                         'event_worker_host_url',
                         array($this, 'plugin_host_url_settings_validate'));
    }

    /** 
     * Print the settings info for the API endpoint.
     *
     */
    function print_api_endpoint_settings_section_info()
    {
        _e('Set the API endpoint', 'event-worker-translations');
    }

    /** 
     * Input for the API endpoint.
     *
     */
    function create_input_api_endpoint()
    {   
        $options = get_option('event_worker_api_endpoint');
        //$options['api-endpoint'] = empty($options['api-endpoint']) ? 'v01/api' : $options['api-endpoint'];
        ?><input size="40" type="text" name="event_worker_api_endpoint[api-endpoint]" value="<?php echo esc_attr($options['api-endpoint']); ?>" /><?php
    }

    /** 
     * Validate the input.
     *
     */
    function plugin_api_endpoint_settings_validate($arr_input)
    {
        $options = get_option('event_worker_api_endpoint');
        $options['api-endpoint'] = sanitize_text_field($arr_input['api-endpoint']);

        return $options;
    }

    /** 
     * Print the settings info for the host url.
     *
     */
    function print_host_url_settings_section_info()
    {
        _e('Set the URL to fetch events.', 'event-worker-translations');
    }

    /** 
     * Input for the host url.
     *
     */
    function create_input_host_url()
    {
        $options = get_option('event_worker_host_url');
        ?><input size="40" type="text" name="event_worker_host_url[host-url]" value="<?php echo esc_url($options['host-url']); ?>" /><?php
    }

    /** 
     * Validate the input.
     *
     * @param array $arr_input the input.
     *
     * @return array
     *
     */
    function plugin_host_url_settings_validate($arr_input)
    {
        $options = get_option('event_worker_host_url');
        $options['host-url'] = esc_url_raw($arr_input['host-url']);

        return $options;
    }
}

?>