(function (jQuery)
{
    jQuery(function ()
    {
        jQuery.post(ajaxurl,
        {
            action: 'generate_pdf'
            //data: 'test'
        },
        function (response)
        {
            //console.log(response);
        });
    });
}(jQuery));