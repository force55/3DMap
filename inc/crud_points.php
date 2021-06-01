<?php

/*
 * Deactivate point
 */
add_action('wp_ajax_nopriv_deactivate_point', 'deactivate_point_callback');
add_action('wp_ajax_deactivate_point', 'deactivate_point_callback');

function deactivate_point_callback()
{
    check_ajax_referer('myajax-nonce', 'nonce_code');

    $id_deactivate = $_POST['data']['id'];

    $args = array(
        'post_type' => 'routes',
        'numberposts' => -1,
        'post_status' => 'private'
    );
    $routes = get_posts($args);

    foreach ($routes as $route) {
        $points_route = get_field('points_route', $route->ID);

        foreach ($points_route as $point_route) {
            if ($point_route['point'] == $id_deactivate) {
                wp_send_json_success(array(
                    'message' => 'Для того, щоб деактивувати точку, цю точку треба видалити з маршруту ' . get_field('title_route', $route->ID) . '!',
                    'url' => ''
                ));

                die();
            }
        }
    }

    $update_post = array(
        'post_type' => 'points',
        'ID' => $id_deactivate,
        'post_status' => 'trash'
    );

    $deactivate = wp_update_post($update_post);

    $pointEn = pll_get_post($id_deactivate, 'en');
    $update_postEN = array(
        'post_type' => 'points',
        'ID' => $pointEn,
        'post_status' => 'trash'
    );

    $deactivateEN = wp_update_post($update_postEN);
    $url_return = home_url();
    wp_send_json_success(array(
        'status' => 'deactivate',
        'id' => $id_deactivate,
        'url' => $url_return,
        'deactivate' => $deactivate,
        'deactivateEN' => $deactivateEN,
        'message' => 'Точку було деактивовано'
    ));
}

/*
 * Create point
 */
add_action('wp_ajax_nopriv_create_point', 'create_point_callback');
add_action('wp_ajax_create_point', 'create_point_callback');

function create_point_callback()
{
    check_ajax_referer('myajax-nonce', 'nonce_code');
    if (!function_exists('wp_handle_upload'))
        require_once(ABSPATH . 'wp-admin/includes/file.php');

    $_POST['title-ua'] = $_POST['title-ua'] == '' ? 'TEMP' : $_POST['title-ua'];

    $args = array(
        'post_title' => $_POST['title-ua'],
        'post_status' => 'private',
        'post_type' => 'points',
        'post_author' => get_current_user_id()
    );

    $point = wp_insert_post(wp_slash($args));

    $name = $_POST['title-ua'];
    $subtitle = $_POST['subtitle-ua'];
    $display_admins = false;
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $description = $_POST['description-ua'];
    $main_photo = $_FILES['main-thumbnail'];
    $aditional_security = $_POST['monument-information-description-ua'];
    $photo_security = $_FILES['monument-information-thumbnail'];
    $file_security = $_FILES['monument-information-file'];
    $file_audio = $_FILES['audio'];
    $file_panorama = $_FILES['panorama-photo'];
    $file_mtl = $_FILES['mtl-3d-model'];
    $file_obj = $_FILES['obj-3d-model'];
    $file_texstue = $_FILES['texture-3d-model'];
    $publish = $_POST['publish'];

    if ($publish) {
        $point_edit = array();
        $point_edit['ID'] = $point;
        $point_edit['post_status'] = 'pending';

        wp_update_post(wp_slash($point_edit));
    }

    update_field('name_point', $name, $point);
    update_field('subtitle_point', $subtitle, $point);
    update_field('displyay_not_logged_users', $display_admins, $point);

    $values = array(
        'shyryna' => $lat,
        'dolgota' => $lng,

    );
    update_field('kordynaty_point', $values, $point);

    update_field('additional_point', $description, $point);


    if ($main_photo['name'] != '' and isset($_FILES['main-thumbnail'])) {
        $main_photo_upload = media_handle_upload('main-thumbnail', 0);
        if ($main_photo_upload && empty($main_photo_upload['error'])) {
            update_field('additional_image_point', $main_photo_upload, $point);
        }
    }

    update_field('additional_security', $aditional_security, $point);

    if ($photo_security['name'] != '') {
        $photo_security_upload = media_handle_upload('monument-information-thumbnail', 0);
        if ($photo_security_upload && empty($photo_security_upload['error'])) {
            update_field('image_security', $photo_security_upload, $point);
        }
    }

    if ($file_security['name'] != '') {
        $file_security_upload = media_handle_upload('monument-information-file', 0);
        if ($file_security_upload && empty($file_security_upload['error'])) {
            update_field('file_security', $file_security_upload, $point);
        }
    }

    $publications_count = $_POST['publications'];

    $publications = array();
    for ($i = 1; $i <= $publications_count; $i++) {
        $text = $_POST["publication-$i-description-ua"];
        $photo = $_FILES["publication-$i-photo"];
        $file = $_FILES["publication-$i-file"];

        if ($photo['name'] != '') {
            $photo_upload = media_handle_upload("publication-$i-photo", 0);
        } else {
            $photo_upload = '';
        }

        if ($file['name'] != '') {
            $file_upload = media_handle_upload("publication-$i-file", 0);
        } else {
            $file_upload = '';
        }


        $publication = array(
            'article' => $text,
            'foto' => $photo_upload,
            'file' => $file_upload
        );

        $publications[] = $publication;
    }

    update_field('scientist_articles', $publications, $point);


    if ($file_audio['name'] != '') {
        $file_audio_upload = media_handle_upload('audio', 0);
        if ($file_audio_upload && empty($file_audio_upload['error'])) {
            update_field('audio_file_point', $file_audio_upload, $point);
        }
    }

    if ($file_panorama['name'] != '') {
        $file_panorama_upload = media_handle_upload('panorama-photo', 0);
        if ($file_panorama_upload && empty($file_panorama_upload['error'])) {
            update_field('panorama_point', $file_panorama_upload, $point);
        }
    }

    if ($file_mtl['name'] != '' and $file_obj['name'] != '' and $file_texstue['name']) {
        $file_obj_upload = media_handle_upload('obj-3d-model', 0);
        $file_mtl_upload = media_handle_upload('mtl-3d-model', 0);
        $file_texstue_upload = media_handle_upload('texture-3d-model', 0);

        $values_3d = array(
            'fajl_mtl' => $file_mtl_upload,
            'fail_obj' => $file_obj_upload,
            'tekstura_svitlyna' => $file_texstue_upload,
        );

        update_field('3d_image', $values_3d, $point);
    }

    $photos_videos = array();
    $photos_gallery = $_POST['photos_gallery'];
    if ($photos_gallery > 0 and $_FILES["gallery-photo-1"]['name'] != '') {
        for ($i = 1; $i <= $photos_gallery; $i++) {
            $photo = $_FILES["gallery-photo-$i"];
            if ($photo['name'] != '') {
                $photo_upload = media_handle_upload("gallery-photo-$i", 0);

                if ($photo_upload && empty($photo_upload['error'])) {
                    $arr = array(
                        'photo' => $photo_upload,
                        'photo_or_video' => 1
                    );
                    $photos_videos[] = $arr;
                }
            }
        }
    }

    $videos_gallery = $_POST['videos_gallery'];
    if ($videos_gallery > 0) {
        for ($i = 1; $i <= $videos_gallery; $i++) {
            $video = $_POST["link-to-video-$i"];
            if ($video != '') {
                $arr = array(
                    'video' => esc_url($video),
                    'photo_or_video' => 0
                );
                $photos_videos[] = $arr;
            }
        }
    }

    if (!empty($photos_videos)) {
        update_field('photo_video_point', $photos_videos, $point);
    }

    $other_resources = $_POST['other_resources'];
    $other_resources_arr = array();
    if ($other_resources > 0) {
        for ($i = 1; $i <= $other_resources; $i++) {
            $resource = $_FILES["other-resource-$i"];
            if ($resource['name'] != '') {
                $resource_upload = media_handle_upload("other-resource-$i", 0);

                if ($resource_upload && empty($resource_upload['error'])) {
                    $other_resources_arr[] = $resource_upload;
                }
            }
        }
    }

    if (!empty($other_resources_arr)) {
        update_field('galereya_photo_temi', $other_resources_arr, $point);
    }


    //translate to english language ( polylang )
    //не удалять!!!
    /*    global $polylang;

        $args = array(
            'post_title' => $_POST['title-en'],
            'post_status' => 'pending',
            'post_type' => 'points',
            'post_author' => get_current_user_id()
        );

        $pointEn = wp_insert_post(wp_slash($args), true);
        $polylang->model->set_post_language($pointEn, 'en');
        $polylang->model->save_translations('post', $point, array('en' => $pointEn));

        //copy fields ukl to en
        $name = $_POST['title-en'];
        $subtitle = $_POST['subtitle-en'];
        $display_admins = false;
        $lat = $_POST['lat'];
        $lng = $_POST['lng'];
        $description = $_POST['description-en'];
        $main_photo = $_FILES['main-thumbnail'];
        $aditional_security = $_POST['monument-information-description-en'];
        $photo_security = $_FILES['monument-information-thumbnail'];
        $file_security = $_FILES['monument-information-file'];
        $file_audio = $_FILES['audio'];
        $file_panorama = $_FILES['panorama-photo'];
        $file_mtl = $_FILES['mtl-3d-model'];
        $file_obj = $_FILES['obj-3d-model'];

        update_field('name_point', $name, $pointEn);
        update_field('subtitle_point', $subtitle, $pointEn);
        update_field('displyay_not_logged_users', $display_admins, $pointEn);

        $values = array(
            'shyryna' => $lat,
            'dolgota' => $lng,

        );
        update_field('kordynaty_point', $values, $pointEn);

        update_field('additional_point', $description, $pointEn);


        if ($main_photo['name'] != '' and isset($_FILES['main-thumbnail'])) {
            $main_photo_upload = media_handle_upload('main-thumbnail', 0);
            if ($main_photo_upload && empty($main_photo_upload['error'])) {
                update_field('additional_image_point', $main_photo_upload, $pointEn);
            }
        }

        update_field('additional_security', $aditional_security, $pointEn);

        if ($photo_security['name'] != '') {
            $photo_security_upload = media_handle_upload('monument-information-thumbnail', 0);
            if ($photo_security_upload && empty($photo_security_upload['error'])) {
                update_field('image_security', $photo_security_upload, $pointEn);
            }
        }

        if ($file_security['name'] != '') {
            $file_security_upload = media_handle_upload('monument-information-file', 0);
            if ($file_security_upload && empty($file_security_upload['error'])) {
                update_field('file_security', $file_security_upload, $pointEn);
            }
        }

        $publications_count = $_POST['publications'];

        $publications = array();
        for ($i = 1; $i <= $publications_count; $i++) {
            $text = $_POST["publication-$i-description-en"];
            $photo = $_FILES["publication-$i-photo"];
            $file = $_FILES["publication-$i-file"];

            if ($photo['name'] != '') {
                $photo_upload = media_handle_upload("publication-$i-photo", 0);
            } else {
                $photo_upload = '';
            }

            if ($file['name'] != '') {
                $file_upload = media_handle_upload("publication-$i-file", 0);
            } else {
                $file_upload = '';
            }


            $publication = array(
                'article' => $text,
                'foto' => $photo_upload,
                'file' => $file_upload
            );

            $publications[] = $publication;
        }

        update_field('scientist_articles', $publications, $pointEn);


        if ($file_audio['name'] != '') {
            $file_audio_upload = media_handle_upload('audio', 0);
            if ($file_audio_upload && empty($file_audio_upload['error'])) {
                update_field('audio_file_point', $file_audio_upload, $pointEn);
            }
        }

        if ($file_panorama['name'] != '') {
            $file_panorama_upload = media_handle_upload('panorama-photo', 0);
            if ($file_panorama_upload && empty($file_panorama_upload['error'])) {
                update_field('panorama_point', $file_panorama_upload, $pointEn);
            }
        }

        if ($file_mtl['name'] != '' and $file_obj['name'] != '' and $file_texstue['name']) {
            $file_obj_upload = media_handle_upload('obj-3d-model', 0);
            $file_mtl_upload = media_handle_upload('mtl-3d-model', 0);
            $file_texstue_upload = media_handle_upload('texture-3d-model', 0);

            $values_3d = array(
                'fajl_mtl' => $file_mtl_upload,
                'fail_obj' => $file_obj_upload,
                'tekstura_svitlyna' => $file_texstue_upload,
            );

            update_field('3d_image', $values_3d, $pointEn);
        }

        if (!empty($photos_videos)) {
            update_field('photo_video_point', $photos_videos, $pointEn);
        }

        if (!empty($other_resources_arr)) {
            update_field('galereya_photo_temi', $other_resources_arr, $pointEn);
        }*/


    if (!$publish) {
        $url_return = home_url() . '?visitid=' . $point;
        $message = 'Точку було додано';
    } else {
        $url_return = home_url();
        $message = 'Точку була створена та вiдправлена на модерацiю';
    }

    wp_send_json_success(array(
        'status' => 'create',
        'point' => $point,
        'url' => $url_return,
        'message' => $message
    ));
}

/*
 * Edit point
 */
add_action('wp_ajax_nopriv_edit_point', 'edit_point_callback');
add_action('wp_ajax_edit_point', 'edit_point_callback');
function edit_point_callback()
{
    check_ajax_referer('myajax-nonce', 'nonce_code');
    if (!function_exists('wp_handle_upload'))
        require_once(ABSPATH . 'wp-admin/includes/file.php');

    $_POST['title-ua'] = $_POST['title-ua'] == '' ? 'TEMP' : $_POST['title-ua'];

    $id_of_marker = $_POST['id-of-marker'];
    $point = str_replace('marker-wordpress-id-', '', $id_of_marker);;

    $name = $_POST['title-ua'];
    $subtitle = $_POST['subtitle-ua'];
    $display_admins = false;
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $description = $_POST['description-ua'];
    $main_photo = $_FILES['main-thumbnail'];
    $aditional_security = $_POST['monument-information-description-ua'];
    $photo_security = $_FILES['monument-information-thumbnail'];
    $file_security = $_FILES['monument-information-file'];
    $file_audio = $_FILES['audio'];
    $file_panorama = $_FILES['panorama-photo'];
    $file_mtl = $_FILES['mtl-3d-model'];
    $file_obj = $_FILES['obj-3d-model'];
    $file_texstue = $_FILES['texture-3d-model'];
    $publish = $_POST['publish'];

    if ($publish == 'on') {

        $routes = get_posts(array(
            'post_type' => 'routes',
            'numberposts' => -1,
            'post_status' => 'private',
            'lang' => 'uk'
        ));

        foreach ($routes as $route) {
            $points_route = get_field('points_route', $route->ID);
            foreach ($points_route as $point_route) {
                if ($point_route['point'] == $point) {

                    wp_send_json_success(array(
                        'message' => 'Для того, щоб опублікувати точку, цю точку треба видалити з маршруту ' . get_field('title_route', $route->ID) . '!',
                        'url' => ''
                    ));
                    die();
                }
            }
        }


        $point_edit = array();
        $point_edit['ID'] = $point;
        $point_edit['post_status'] = 'pending';

        wp_update_post(wp_slash($point_edit));
    }

    update_field('name_point', $name, $point);
    update_field('subtitle_point', $subtitle, $point);
    update_field('displyay_not_logged_users', $display_admins, $point);

    $values = array(
        'shyryna' => $lat,
        'dolgota' => $lng,

    );
    update_field('kordynaty_point', $values, $point);

    update_field('additional_point', $description, $point);


    if ($main_photo['name'] != '' and isset($_FILES['main-thumbnail'])) {
        $main_photo_upload = media_handle_upload('main-thumbnail', 0);
        if ($main_photo_upload && empty($main_photo_upload['error'])) {
            update_field('additional_image_point', $main_photo_upload, $point);
        }
    }

    update_field('additional_security', $aditional_security, $point);

    if ($photo_security['name'] != '') {
        $photo_security_upload = media_handle_upload('monument-information-thumbnail', 0);
        if ($photo_security_upload && empty($photo_security_upload['error'])) {
            update_field('image_security', $photo_security_upload, $point);
        }
    }

    if ($file_security['name'] != '') {
        $file_security_upload = media_handle_upload('monument-information-file', 0);
        if ($file_security_upload && empty($file_security_upload['error'])) {
            update_field('file_security', $file_security_upload, $point);
        }
    }

    $publications_count = $_POST['publications'];

    $publications = array();

    $old_publications = get_field('scientist_articles', $point);

    for ($i = 1; $i <= $publications_count; $i++) {
        $text = $_POST["publication-$i-description-ua"];
        $photo = $_FILES["publication-$i-photo"];
        $file = $_FILES["publication-$i-file"];

        if ($photo['name'] != '') {
            $photo_upload = media_handle_upload("publication-$i-photo", 0);
        } else {
            $image = $old_publications[$i - 1]['foto'];
            $photo_upload = pippin_get_image_id($image);
        }

        if ($file['name'] != '') {
            $file_upload = media_handle_upload("publication-$i-file", 0);
        } else {
            $file_old = $old_publications[$i - 1]['file'];
            $file_upload = pippin_get_image_id($file_old['url']);
        }

        $publication = array(
            'article' => $text,
            'foto' => $photo_upload,
            'file' => $file_upload
        );

        $publications[] = $publication;
    }

    update_field('scientist_articles', $publications, $point);


    if ($file_audio['name'] != '') {
        $file_audio_upload = media_handle_upload('audio', 0);
        if ($file_audio_upload && empty($file_audio_upload['error'])) {
            update_field('audio_file_point', $file_audio_upload, $point);
        }
    }

    if ($file_panorama['name'] != '') {
        $file_panorama_upload = media_handle_upload('panorama-photo', 0);
        if ($file_panorama_upload && empty($file_panorama_upload['error'])) {
            update_field('panorama_point', $file_panorama_upload, $point);
        }
    }

    if ($file_mtl['name'] != '' and $file_obj['name'] != '' and $file_texstue['name']) {
        $file_obj_upload = media_handle_upload('obj-3d-model', 0);
        $file_mtl_upload = media_handle_upload('mtl-3d-model', 0);
        $file_texstue_upload = media_handle_upload('texture-3d-model', 0);

        $values_3d = array(
            'fajl_mtl' => $file_mtl_upload,
            'fail_obj' => $file_obj_upload,
            'tekstura_svitlyna' => $file_texstue_upload,
        );

        update_field('3d_image', $values_3d, $point);
    }


    $old_photos_videos = get_field('photo_video_point', $point);
    $old_photos = array();
    $old_videos = array();

    foreach ($old_photos_videos as $item) {
        if ($item['photo_or_video'] == true) {
            $old_photos[] = $item['photo'];
        } else {
            $old_videos[] = $item['video'];
        }
    }

    $photos_videos = array();
    $photos_gallery = $_POST['photos_gallery'];

    if ($photos_gallery > 0) {
        for ($i = 1; $i <= $photos_gallery; $i++) {
            $photo = $_FILES["gallery-photo-$i"];
            if ($photo['name'] != '') {
                $photo_upload = media_handle_upload("gallery-photo-$i", 0);

                if ($photo_upload && empty($photo_upload['error'])) {
                    $arr = array(
                        'photo' => $photo_upload,
                        'photo_or_video' => 1
                    );
                    $photos_videos[] = $arr;
                }
            } else {

                $old_photo_object = $old_photos[$i - 1];
                $old_photo = pippin_get_image_id($old_photo_object['url']);
                $arr = array(
                    'photo' => $old_photo,
                    'photo_or_video' => 1
                );
                $photos_videos[] = $arr;
            }
        }
    } else {
        $photos_videos = [];
    }

    $videos_gallery = $_POST['videos_gallery'];
    if ($videos_gallery > 0) {
        for ($i = 1; $i <= $videos_gallery; $i++) {
            $video = $_POST["link-to-video-$i"];
            if ($video != '') {
                $arr = array(
                    'video' => esc_url($video),
                    'photo_or_video' => 0
                );
                $photos_videos[] = $arr;
            }
        }
    }

    update_field('photo_video_point', $photos_videos, $point);

    $other_resources = $_POST['other_resources'];
    $old_other_resources = get_field('galereya_photo_temi', $point);

    $other_resources_arr = array();
    if ($other_resources > 0) {
        for ($i = 1; $i <= $other_resources; $i++) {
            $resource = $_FILES["other-resource-$i"];
            if ($resource['name'] != '') {
                $resource_upload = media_handle_upload("other-resource-$i", 0);

                if ($resource_upload && empty($resource_upload['error'])) {
                    $other_resources_arr[] = $resource_upload;
                }
            } else {
                $image = $old_other_resources[$i - 1]['url'];
                $other_resources_arr[] = pippin_get_image_id($image);
            }
        }
    }

    if (!empty($other_resources_arr)) {
        update_field('galereya_photo_temi', $other_resources_arr, $point);
    } else {
        update_field('galereya_photo_temi', array(), $point);
    }


    //translate to english language ( polylang )
    //don't delete please, this functional edit english version point
    /*global $polylang;

    $pointEn = pll_get_post($point, 'en');

    //copy fields ukl to en
    $name = $_POST['title-en'];
    $subtitle = $_POST['subtitle-en'];
    $display_admins = false;
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $description = $_POST['description-en'];
    $main_photo = $_FILES['main-thumbnail'];
    $aditional_security = $_POST['monument-information-description-en'];
    $photo_security = $_FILES['monument-information-thumbnail'];
    $file_security = $_FILES['monument-information-file'];
    $file_audio = $_FILES['audio'];
    $file_panorama = $_FILES['panorama-photo'];
    $file_mtl = $_FILES['mtl-3d-model'];
    $file_obj = $_FILES['obj-3d-model'];

    update_field('name_point', $name, $pointEn);
    update_field('subtitle_point', $subtitle, $pointEn);
    update_field('displyay_not_logged_users', $display_admins, $pointEn);

    $values = array(
        'shyryna' => $lat,
        'dolgota' => $lng,

    );
    update_field('kordynaty_point', $values, $pointEn);

    update_field('additional_point', $description, $pointEn);


    if ($main_photo['name'] != '' and isset($_FILES['main-thumbnail'])) {
        $main_photo_upload = media_handle_upload('main-thumbnail', 0);
        if ($main_photo_upload && empty($main_photo_upload['error'])) {
            update_field('additional_image_point', $main_photo_upload, $pointEn);
        }
    }

    update_field('additional_security', $aditional_security, $pointEn);

    if ($photo_security['name'] != '') {
        $photo_security_upload = media_handle_upload('monument-information-thumbnail', 0);
        if ($photo_security_upload && empty($photo_security_upload['error'])) {
            update_field('image_security', $photo_security_upload, $pointEn);
        }
    }

    if ($file_security['name'] != '') {
        $file_security_upload = media_handle_upload('monument-information-file', 0);
        if ($file_security_upload && empty($file_security_upload['error'])) {
            update_field('file_security', $file_security_upload, $pointEn);
        }
    }

    $publications_count = $_POST['publications'];

    $publications = array();
    $old_publications = get_field('scientist_articles', $pointEn);

    for ($i = 1; $i <= $publications_count; $i++) {
        $text = $_POST["publication-$i-description-en"];
        $photo = $_FILES["publication-$i-photo"];
        $file = $_FILES["publication-$i-file"];

        if ($photo['name'] != '') {
            $photo_upload = media_handle_upload("publication-$i-photo", 0);
        } else {
            $image = $old_publications[$i - 1]['foto'];
            $photo_upload = pippin_get_image_id($image);
        }

        if ($file['name'] != '') {
            $file_upload = media_handle_upload("publication-$i-file", 0);
        } else {
            $file_old = $old_publications[$i - 1]['file'];
            $file_upload = pippin_get_image_id($file_old['url']);
        }

        $publication = array(
            'article' => $text,
            'foto' => $photo_upload,
            'file' => $file_upload
        );

        $publications[] = $publication;
    }

    update_field('scientist_articles', $publications, $pointEn);


    if ($file_audio['name'] != '') {
        $file_audio_upload = media_handle_upload('audio', 0);
        if ($file_audio_upload && empty($file_audio_upload['error'])) {
            update_field('audio_file_point', $file_audio_upload, $pointEn);
        }
    }

    if ($file_panorama['name'] != '') {
        $file_panorama_upload = media_handle_upload('panorama-photo', 0);
        if ($file_panorama_upload && empty($file_panorama_upload['error'])) {
            update_field('panorama_point', $file_panorama_upload, $pointEn);
        }
    }

    if ($file_mtl['name'] != '' and $file_obj['name'] != '') {
        $file_obj_upload = media_handle_upload('obj-3d-model', 0);
        $file_mtl_upload = media_handle_upload('mtl-3d-model', 0);

        $values_3d = array(
            'fajl_mtl' => $file_mtl_upload,
            'fail_obj' => $file_obj_upload
        );

        update_field('3d_image', $values_3d, $pointEn);
    }

    if (!empty($photos_videos)) {
        update_field('photo_video_point', $photos_videos, $pointEn);
    }

    if (!empty($other_resources_arr)) {
        update_field('galereya_photo_temi', $other_resources_arr, $pointEn);
    } else {
        update_field('galereya_photo_temi', array(), $pointEn);
    }*/

    $url_return = home_url() . '?visitid=' . $point;
    wp_send_json_success(array(
        'status' => 'edit',
        'point' => $point,
        'url' => $url_return,
        'message' => 'Точка була відредагована'
    ));
}