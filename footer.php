<footer id="colophon" class="site-footer">
    <div class="site-info">
    </div><!-- .site-info -->
</footer><!-- #colophon -->
<script>

    var markerIcons = {
        admin: '<?php echo get_template_directory_uri()?>/html_templates/build/images/tag-primary.png',
        user: '<?php echo get_template_directory_uri()?>/html_templates/build/images/tag-user.png',
        unactive: '<?php echo get_template_directory_uri()?>/html_templates/build/images/tag-unactive.png',
        minimap: '<?php echo get_template_directory_uri()?>/html_templates/build/images/tag-minimap.png'
    }

    var regionsUrl = '<?php echo get_template_directory_uri()?>/regions.KML';

    let picturefb = '<?php echo get_template_directory_uri()?>/html_templates/build/images/logo_fb.jpg"';
</script>
<?php wp_footer(); ?>
<script>
    var points = [];
    var routes = [];

    var timeout = 20000;
    if ($('body').hasClass('user-is-logins')) {
        $('.into').fadeOut();
        timeout = 0;
    }


    jQuery.ajax({
        type: "POST",
        url: myajax.url,
        data: {
            action: 'get_points',
            nonce_code: myajax.nonce
        },
        nonce_code: myajax.nonce,
        success: function (points_json) {

            jQuery.ajax({
                type: "POST",
                url: myajax.url,
                data: {
                    action: 'get_routes',
                    nonce_code: myajax.nonce
                },
                nonce_code: myajax.nonce,
                success: function (routes_json) {
                    routes = routes_json.data.data;
                    points = points_json.data.data;
                    var tags = '';
                    for (var i = 0; i < points.length; i++) {
                        points[i].localId = i;
                        if (points[i].tags) {
                            tags += points[i].tags + ','
                        }
                    }

                    tags = tags.split(',');
                    $.each(tags, function(i, el){
                        if($.inArray(el, state.tags) === -1) state.tags.push(el);
                    });
                    setTimeout(function () {
                        initMap();
                        addMarkers('markersLayer', points);
                        addAvailablePoints();
                        addRoutesList();
                        listeners();
                    }, timeout);
                },
                error: function (errorThrown) {
                    console.log('error');
                    console.log(errorThrown);
                }
            });

        },
        error: function (errorThrown) {
            console.log('error');
            console.log(errorThrown);
        }
    });
</script>
</body>
</html>