<?php
/*
 * Get points
 */
add_action('wp_ajax_nopriv_get_routes', 'get_routes_endpoint');
add_action('wp_ajax_get_routes', 'get_routes_endpoint');
function get_routes_endpoint()
{

    check_ajax_referer('myajax-nonce', 'nonce_code');

    $routes = get_posts(array(
        'post_type' => 'routes',
        'numberposts' => -1,
        'post_status' => 'publish,private',
        'lang' => 'uk'
    ));

    $routes_json = array();

    foreach ($routes as $route):

        $points = get_field('points_route', $route->ID);


        $count = array_key_last($points);
        if (empty($points)) {
            continue;
        }
        $points_string = '';
        foreach ($points as $key => $point) {

            $post_point = get_post($point);

            if ($route->post_status == 'publish' and $post_point->post_status == 'private' or $post_point->post_status == 'pending') {
                continue;
            }

            if ($key == $count) {
                $points_string .= 'marker-wordpress-id-' . $point['point'];
            } else {
                $points_string .= 'marker-wordpress-id-' . $point['point'] . ', ';
            }
        }

        $kartynka_route = get_field('kartynka_route', $route->ID);
        if ($kartynka_route == '') {
            $kartynka_route = get_template_directory_uri() . '/html_templates/build/images/to-theme-1.png';
        }

        $bg_route = get_field('zadnyj_fon_route', $route->ID);
        if ($bg_route == '') {
            $bg_route = false;
        }

        $title_route = get_field('title_route', $route->ID);

        $subtitle_route = get_field('subtitle_route', $route->ID);

        $virtual_route = get_field('virtualnyj_marshrut', $route->ID);
        $real_route = get_field('realnyj_marshrut', $route->ID);
        $audio = get_field('audyo_route', $route->ID);

        $description = get_field('opys_route', $route->ID);

        $status = $route->post_status;

        if ($route->post_status != 'publish') {
            $edit = array(
                'status' => 'can_edit',
                'id_user' => $route->post_author
            );
        } else {
            $edit = array(
                'edit' => 'no_edit'
            );
        }

        //English version route
        $eng_route = pll_get_post($route->ID, 'en');
        $description_en = get_field('opys_route', $eng_route);

        $routes_json[] = array(
            'wp_id' => $route->ID,
            'status' => $status,
            'edit' => $edit,
            'title' => $title_route,
            'subtitle' => $subtitle_route,
            'description' => $description,
            'descriptionEn' => $description_en,
            'image_route' => $kartynka_route,
            'bg_route' => $bg_route,
            'points' => $points_string,
            'virtual_route' => $virtual_route,
            'real_route' => $real_route,
            'audio' => array(
                'url' => $audio['url'],
                'name' => $audio['filename']
            ),
        );

    endforeach;

    $result = array('data' => $routes_json);

    wp_send_json_success($result);
    wp_die();
}