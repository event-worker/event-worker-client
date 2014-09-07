Event Worker REST API
=====================

An add-on for Event Worker plugin to enable usage as a REST API.

Based on the plugin [Wordpress Slim framework plugin](https://github.com/Botnary/wp-slim-framework) by [Constantin Botnari](https://github.com/Botnary).


##1 Usage
#### 1.1 Set the base route
The default base route is `slim/api`. The possibility to change the route can be found
in `Settings -> Event Worker API`.

The WordPress permalinks needs to be updated after this.
***
#### 1.2 Create new routes
The framework will register your routes when action `slim_mapping` is triggered.
This action has one argument which is the Slim object.

Example of usage:
    
    add_action('slim_mapping',function($slim)
    {
        $slim->get('/slim/api/user/:u', function($user)
        {
            printf("User is %s",$user);
        });
    });

Example of usage inside of a class:

    class Rest
    {
        function __construct()
        {
            add_action('slim_mapping',array(&$this,'slim_mapping');
        }

        function slim_mapping($slim)
        {
            //if needed the class context
            $context = $this;
            $slim->get('/slim/api/user/:u',function($user)use($context)
            {
                $context->printUser($user);
            });
            $slim->put('/slim/api/user/:id',function($id)use($context)
            {
                $context->updateUser($id);
            });
            //...and so on
        }

        function printUser($user)
        {
            printf("User is %s",$user);
        }
    }
***
#### 1.3 Routes

##### 1.3.1 `GET`
| Call                                 | Description                            | Status                            |
|:-------------------------------------|:---------------------------------------|:----------------------------------|
| `<url><base route>`                  | Get API status                         | Not implemented                   |
| `<url><base route>/event`            | Get all the events                     | Working                           |
| `<url><base route>/event/<name>`     | Get single event data by title         | Working                           |
| `<url><base route>/event/<id>`       | Get single event data by ID            | Not implemented                   |
| `<url><base route>/categories`       | Get all the categories                 | Working                           |
| `<url><base route>/organizers`       | Get all the organizers                 | Not implemented                   |


##### 1.3.2 `POST`
| Call                                 | Description                            | Status                            |
|:-------------------------------------|:---------------------------------------|:----------------------------------|
| `<url><base route>/event`            | Post a new event to the database       | Not implemented                   |
| `<url><base route>/organizer`        | Post a new organizer to the database   | Not implemented                   |


##### 1.3.3 `PUT`
| Call                                 | Description                            | Status                            |
|:-------------------------------------|:---------------------------------------|:----------------------------------|
| `<url><base route>/event`            | Update event in the database           | Not implemented                   |
| `<url><base route>/organizer`        | Update organizer in the database       | Not implemented                   |


##### 1.3.4 `DELETE`
| Call                                 | Description                            | Status                            |
|:-------------------------------------|:---------------------------------------|:----------------------------------|
| `<url><base route>/event`            | Delete event in the database           | Not implemented                   |
| `<url><base route>/organizer`        | Delete organizer in the database       | Not implemented                   |