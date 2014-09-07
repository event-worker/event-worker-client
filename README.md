Event Worker Client
===================

A WordPress plugin to fetch and show the events from the [Event Worker Host](https://github.com/sugardrunk/event-worker-host) JSON-LD feed.

<hr>
##1 Requirements
####1.1 cURL
The server requires cURL.
####1.2 Permalinks
The WordPress installation requires permalinks (Post name).

<hr>
##2 Install
####2.1 Install the plugin
Install the plugin to the `wp-content/plugins` folder.
####2.2 Set permalinks on
Set the permalinks to `Post name`.
####2.3 Activate the plugin
Activate the plugin from the `Plugins` menu.
####2.4 Set the host URL
Set the URL of the host from which to fetch the events in the `Event Worker Options`.

<hr>
##3 Optional settings
####3.1 Events as a front page
Create an empty page with a slug `events` and set it as a `static front page`.
####3.2 Change API endpoint
Set the API endpoint if needed in the `Event Worker Options`.