Event Worker REST API
=====================

An add-on for Event Worker plugin to enable usage as a REST API.

Based on the plugin [Wordpress Slim framework plugin](https://github.com/Botnary/wp-slim-framework) by [Constantin Botnari](https://github.com/Botnary).


##1 Usage
#### 1.1 Set the base route
The default base route is `v01/api`. The possibility to change the route can be found
in `Settings -> Event Worker Options`.
***
#### 1.2 Routes

##### 1.2.1 `GET`
| Call                                 | Description                            | Status                            |
|:-------------------------------------|:---------------------------------------|:----------------------------------|
| `<url><base route>`                  | Get API status                         | Working                           |
| `<url><base route>/event`            | Get all the events                     | Working                           |
| `<url><base route>/event/<id>`       | Get single event data by ID            | Working                           |
| `<url><base route>/organizers`       | Get all the organizers                 | Not implemented                   |


##### 1.2.2 `POST`
| Call                                 | Description                            | Status                            |
|:-------------------------------------|:---------------------------------------|:----------------------------------|
| `<url><base route>/event`            | Post a new event to the database       | Not implemented                   |
| `<url><base route>/organizer`        | Post a new organizer to the database   | Not implemented                   |


##### 1.2.3 `PUT`
| Call                                 | Description                            | Status                            |
|:-------------------------------------|:---------------------------------------|:----------------------------------|
| `<url><base route>/event`            | Update event in the database           | Not implemented                   |
| `<url><base route>/organizer`        | Update organizer in the database       | Not implemented                   |


##### 1.2.4 `DELETE`
| Call                                 | Description                            | Status                            |
|:-------------------------------------|:---------------------------------------|:----------------------------------|
| `<url><base route>/event`            | Delete event in the database           | Not implemented                   |
| `<url><base route>/organizer`        | Delete organizer in the database       | Not implemented                   |