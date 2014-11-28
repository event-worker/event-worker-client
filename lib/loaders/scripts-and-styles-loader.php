<?php

/**
 * Class for loading the map scripts and the datetime picker.
 *
 * load the maps and the datetime picker.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerClientScriptLoaderHelper
{
    /** 
     * Get the map only.
     *
     * @param string $location the location of the event
     *     
     */
    function getMapOnly($location)
    {
        ?>
        <script>

            var l = "<?php echo esc_attr($location); ?>";
            runMap(l);

        </script>
        <?php
    }

    /** 
     * Append the styles.
     *
     */
    function append_styles()
    {

        ?>
        <script>

        function rgb2hex(rgb)
        {
            if(rgb.search("rgb") == -1)
            {
                return rgb;
            }
            else
            {
                rgb = rgb.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/);

                function hex(x)
                {
                    return ("0" + parseInt(x).toString(16)).slice(-2);
                }

                return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]); 
            }
        }

        jQuery(function()
        {
            var styles = jQuery("#common_wrapper a").css( "color" );
            jQuery(".today").css("color", rgb2hex(styles));
        });
        
        </script>
        <?php
    }
}

/**
 * Class for loading the needed scripts.
 *
 * load the needed scripts.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerClientMainScriptLoader
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        add_action('wp_enqueue_scripts', array($this,'add_stylesheet'));
        add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
    }

    /** 
     * Add the plugin custom stylesheet.
     *
     */
    function add_stylesheet()
    {
        wp_enqueue_style('prefix-style', plugins_url('../css/style.css', __FILE__));
    }

    /** 
     * Register the scripts.
     *
     */
    function register_scripts()
    { 
        if (!is_archive())
        {
            wp_enqueue_script('google-maps',
                          '//maps.googleapis.com/maps/api/js?&sensor=false',
                          array(), '3', false);

            wp_enqueue_script('maphandler',
                              plugins_url('../js/maphandler.js', __FILE__),
                              array(), '1', false);
        }        
    }
}
new WorkerClientMainScriptLoader();

?>