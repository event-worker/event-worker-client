<?php

/**
 * Class for loading the map script.
 *
 * load the map.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerScriptLoaderHelper
{
    /** 
     * Get the map.
     *
     * @param string $location the location of the event
     *     
     */
    function getMap($location)
    {
        ?>
        <script>

            var l = "<?php echo $location; ?>";
            
            runMap(l);
            jQuery(document).ready(function(jQuery)
            {
                jQuery('.eventdate').datetimepicker(
                {
                    dateFormat: 'dd.mm.yy',
                    showButtonPanel: false,
                    constrainInput: true,
                    minDate: new Date(),
                    firstDay: 1
                });
            });

        </script>
        <?php
    }

    /** 
     * Get the map with user location.
     *
     */
    function getLocation()
    {
        ?>
        <script>

            runMap();
            jQuery(document).ready(function(jQuery)
            {
                jQuery('.eventdate').datetimepicker(
                {
                    dateFormat: 'dd.mm.yy',
                    showButtonPanel: false,
                    constrainInput: true,
                    minDate: new Date(),
                    firstDay: 1
                });
            });

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
            if ( rgb.search("rgb") == -1 )
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
            //alert(styles);
            jQuery(".today").css("color", rgb2hex(styles));
        });
        
        </script>
        <?php
    }
}

/**
 * Class for loading the scripts.
 *
 * load the needed scripts.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerMainScriptLoader
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
        wp_enqueue_style('prefix-style_reset', 'https://reset5.googlecode.com/hg/reset.min.css');
    }

    /** 
     * Register the scripts.
     *
     */
    function register_scripts()
    {
        wp_enqueue_script('google-maps',
                      '//maps.googleapis.com/maps/api/js?&sensor=false',
                      array(), '3', false);

        wp_enqueue_script('maphandler',
                          plugins_url('../js/maphandler.js', __FILE__),
                          array(), '1', false);

        wp_enqueue_script('validator',
                          plugins_url('../js/validator.js', __FILE__),
                          array(), '1', false);

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-slider');

        wp_enqueue_script('jquery-time-picker' ,
                          plugins_url('../js/jquery-ui-timepicker-addon.js', __FILE__),
                          array(), '1.4.5', true);

        wp_register_style('jquery-ui-theme', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css');
        wp_enqueue_style( 'jquery-ui-theme' );

        wp_register_style('timepicker-theme' , plugins_url('../css/jquery-ui-timepicker-addon.css', __FILE__));
        wp_enqueue_style('timepicker-theme');
    }
}
new WorkerMainScriptLoader();

?>