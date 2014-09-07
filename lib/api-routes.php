<?php

/**
 * Class to set all the API routes.
 *
 * Set API routes.
 *
 * @package EventWorker
 * @author  Janne Kahkonen <jannekahkonen@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 *
 */
class WorkerApiRoutes
{
    /** 
     * The constructor.
     *
     */
    function __construct()
    {
        $this->set_the_routes();
    }

    /**
     * Set all the routes.
     *
     */
    function set_the_routes()
    {
        /** 
         * Show the error message if CALL not found.
         *
         * @param object $slim framework object
         *
         */
        add_action('slim_mapping', function($slim)
        {
            $slim->notFound(function() use ($slim) {
                echo json_encode("API Call Does Not Exist");
            });
        });

        /** 
         * GET the API info.
         *
         * @param object $slim framework object
         *
         */
        add_action('slim_mapping', function($slim)
        {   

            $options = get_option('event_worker_api_endpoint');
            $endpoint = $options['api-endpoint'];

            //$base_url = get_option('slim_base_path','v01/api/');
            
            $slim->get('/' . $endpoint . '/', function() use ($slim)
            {
                echo json_encode($slim->response->getStatus());
                //var_dump($slim);
            });
        });

        /** 
         * GET all the categories.
         *
         * @param object $slim framework object
         *
         */
        add_action('slim_mapping', function($slim)
        {
            $options = get_option('event_worker_api_endpoint');
            $endpoint = $options['api-endpoint'];

            //$base_url = get_option('slim_base_path','v01/api/');
            
            $slim->get('/' . $endpoint . '/categories', function() use ($slim)
            {
                $slim->render('categories.php');
            });
        });


        /** 
         * GET all the events.
         *
         * @param object $slim framework object
         *
         */
        add_action('slim_mapping', function($slim)
        {   
            $options = get_option('event_worker_api_endpoint');
            $endpoint = $options['api-endpoint'];

            //$base_url = get_option('slim_base_path','v01/api/');
            
            $slim->get('/' . $endpoint . '/event', function() use ($slim)
            {
                $slim->render('events.php');
            });
        });

        /** 
         * GET event by name or ID.
         *
         * @param object $slim framework object
         *
         */
        add_action('slim_mapping', function($slim)
        {   
            $options = get_option('event_worker_api_endpoint');
            $endpoint = $options['api-endpoint'];

            //$base_url = get_option('slim_base_path','v01/api/');

            $slim->get('/' . $endpoint . '/event/:e', function($event) use ($slim)
            {
                $slim->render('events.php', array('search' => $event));
            });
        });

        /** 
         * POST new event to the database.
         *
         * @param object $slim framework object
         *
         */
        add_action('slim_mapping', function($slim)
        {   
            $options = get_option('event_worker_api_endpoint');
            $endpoint = $options['api-endpoint'];

            //$base_url = get_option('slim_base_path','v01/api/');

            $slim->post('/' . $endpoint . '/', function() use ($slim)
            {
                if (is_user_logged_in())
                {
                    //$var = $slim->request()->post();
                    //echo json_encode($var);
                    $headers = $slim->request->headers;
                    print_r($headers);

                }
                else
                {
                    echo json_encode("NOT ALLOWED!");
                }
            });
        });
    }
}
new WorkerApiRoutes();

?>