(function (jQuery)
{
    jQuery(function () {
                


        // Initial the request to mark this this particular post as read
        jQuery.post(ajaxurl, {
        
            action: 'mark_as_read',
            data: 'testdata'
            
        }, function (response)
        {
            console.log(response);
        });
    });
}(jQuery));