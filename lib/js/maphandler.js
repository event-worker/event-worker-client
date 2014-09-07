/*
Handle the maps for the Event Worker plugin.
*/
var runMap = function runMap(location)
{
    "use strict";

    var map,
        mapProp,
        geocoder,
        timer;

    if (location == undefined)
    {
        getLocationFromUserPosition();
    }
    else
    {
        getLocationFromData();
    }

    /*
    Get the location from user.
    */
    function getLocationFromUserPosition()
    {
        function initialize()
        {
            var marker;

            geocoder = new google.maps.Geocoder();

            mapProp =
            {
                zoom:16,
                mapTypeId:google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("googleMap"),
                                      mapProp);

            jQuery('#worker_event_location').keyup(function()
            {
                var t = clearInterval(timer);

                timer = setTimeout(
                    function() { codeAddress(
                        document.getElementById('worker_event_location').value);
                        marker.setMap(null); }, 999);
            });

            // No Comment!!
            function codeAddress(loc)
            {
                geocoder.geocode( { 'address': loc}, function(results, status)
                {
                    if (status == google.maps.GeocoderStatus.OK)
                    {
                        jQuery('#worker_event_geolocation').val(results[0].geometry.location);

                        map.setCenter(results[0].geometry.location);

                        marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            animation: google.maps.Animation.DROP
                        });

                        //infowindow.close();

                        //var infowindow = new google.maps.InfoWindow({
                        //map: map,
                        //position: results[0].geometry.location,
                        //content: 'Location found using HTML5.'
                        //});
                    }
                    else
                    {
                       jQuery('#worker_event_geolocation').val("(null, null)");
                    }
                });
            }

            // Try HTML5 geolocation
            if(navigator.geolocation)
            {
                navigator.geolocation.getCurrentPosition(function(position)
                {
                    var pos = new google.maps.LatLng(position.coords.latitude,
                                                     position.coords.longitude);

                    marker = new google.maps.Marker({
                            map: map,
                            position: pos,
                            animation: google.maps.Animation.DROP
                        });

                        //var infowindow = new google.maps.InfoWindow({
                        //map: map,
                        //position: pos,
                        //content: 'Location found using HTML5.'
                        //});

                    map.setCenter(pos);
                },
                function()
                {
                    handleNoGeolocation(true);
                });
            }
            else
            {
                // Browser doesn't support Geolocation
                handleNoGeolocation(false);
            }
        }

        function handleNoGeolocation(errorFlag)
        {
            //if (errorFlag)
            //{
                //var content = 'Error: The Geolocation service failed.';
            //}
            //else
            //{
                //var content = 'Error: Your browser doesn\'t support geolocation.';
            //}

            //var options = {
                //map: map,
                //position: new google.maps.LatLng(60, 105),
                //content: content
            //};

            //var infowindow = new google.maps.InfoWindow(options);
            //map.setCenter(options.position);
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    }

    /*
    Get the location from database.
    */
    function getLocationFromData()
    {
         // Initialize the script.
        function initialize()
        {
            var marker;

            geocoder = new google.maps.Geocoder();

            mapProp =
            {
                zoom: 16,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById("googleMap"),mapProp);

            jQuery('#worker_event_location').keyup(function()
            {            
                var t = clearInterval(timer);

                timer = setTimeout(
                    function() { codeAddress(
                        document.getElementById('worker_event_location').value);
                        marker.setMap(null); }, 999);
            });

            codeAddress(location);

            // No Comment!!
            function codeAddress(loc)
            {
                geocoder.geocode( { 'address': loc}, function(results, status)
                {
                    if (status == google.maps.GeocoderStatus.OK)
                    {
                        jQuery('#worker_event_geolocation').val(results[0].geometry.location);

                        map.setCenter(results[0].geometry.location);

                        marker = new google.maps.Marker({
                            map: map,
                            position: results[0].geometry.location,
                            animation: google.maps.Animation.DROP
                        });
                    }
                    else
                    {
                        jQuery('#worker_event_geolocation').val("(null, null)");
                    }
                });
            }
        }

        // Add on load.
        google.maps.event.addDomListener(window, "load", initialize);
    }
};