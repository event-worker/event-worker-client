(function (jQuery)
{
    jQuery(function ()
    {
        jQuery.post(ajaxurl,
        {
            action: 'generate_files'
            //data: 'example data'
        },
        function (response)
        {
            //console.log(response);
        });
    });
}(jQuery));