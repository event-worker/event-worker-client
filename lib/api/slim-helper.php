<?php

require_once 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

// TODO.
add_filter('rewrite_rules_array', function ($rules)
{   
    $options = get_option('event_worker_api_endpoint');
    $endpoint = $options['api-endpoint'];

    $new_rules = array(
        '('.$endpoint.')' => 'index.php',
    );
    $rules = $new_rules + $rules;
    return $rules;
});

// TODO.
add_action('init', function ()
{   
    $options = get_option('event_worker_api_endpoint');
    $endpoint = $options['api-endpoint'];
    
    if (strstr($_SERVER['REQUEST_URI'],
               $endpoint))
    {
        $slim = new \Slim\Slim();

        // Use if needed.
        //$slim->add(new \TemporaryMiddleware());

        $slim->response->headers->set('Content-Type', 'application/ld+json');

        $slim->config(array(
            'debug' => false,
            'templates.path' => plugin_dir_path( __FILE__ ) . 'templates'
        ));

        client_main_custom_hook($slim);

        do_action('slim_mapping', $slim);
        $slim->run();
        exit;
    }
});

/**
 * Check if request has access.
 *
 */
function client_main_custom_hook($slim)
{
    $slim->hook('slim.before.dispatch', function() use ($slim)
    {   
       $req = $slim->request->getMethod();
        
       //echo $req;

       //print_r($slim);

       if ($req == "POST")
       {
            //$slim->halt('403', json_encode('You shall not pass.')); // or redirect, or something
            $keyToCheck = $slim->request()->params();
            //print_r($keyToCheck);
       }
    });
}

// TODO.
class ClientTemporaryMiddleware extends \Slim\Middleware
{
    public function call()
    {
        // Other
    }
}

?>