<?php
/*
 * Get points
 */
add_action('wp_ajax_nopriv_get_points', 'get_points_endpoint');
add_action('wp_ajax_get_points', 'get_points_endpoint');
function get_points_endpoint()
{

    check_ajax_referer('myajax-nonce', 'nonce_code');

    $args = array(
        'post_type' => 'points',
        'numberposts' => -1,
        'post_status' => 'publish,pending,private',
        'lang' => 'uk'
    );
    $points = get_posts($args);

    $point_json = array();

    foreach ($points as $key => $point):

        $coordinates = get_field('kordynaty_point', $point->ID);
        $lang = $coordinates['shyryna'];
        $lat = $coordinates['dolgota'];

        if (!$lang or !$lat) {
            continue;
        }

        if (check_user_role(array('administrator', 'admin', 'super_user'), $point->post_author)) {
            $user = 'admin';
        } elseif (check_user_role(array('subscriber'), $point->post_author)) {
            $user = 'user';
        }

        if ($point->post_status == 'pending') {
            if ($user != 'admin' or $point->post_author != get_current_user_id()) {
                continue;
            }
        }

        if ($point->post_status == 'pending') {
            $user = 'unactive';
        }

        $userCap = $user;

        $credentionalsToEdit = false;

        $cur_user_id = get_current_user_id();
        if (check_user_role(array('administrator', 'admin', 'super_user'), get_current_user_id())) {
            $credentionalsToEdit = true;
        } elseif ($point->post_status == 'private' and $point->post_author == get_current_user_id()) {
            $credentionalsToEdit = true;
        }

        $cats = render_categories($point->ID);
        $tags = get_field('tags_point', $point->ID);
        $tags_string = '';

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                if (end($tags) === $tag) {
                    $tags_string .= $tag->name;
                } else {
                    $tags_string .= $tag->name . ', ';
                }
            }
        }

        $audio = get_field('audio_file_point', $point->ID);
        $title = get_field('name_point', $point->ID);
        $subTitle = get_field('subtitle_point', $point->ID);
        $check_for_admin = get_field('displyay_not_logged_users', $point->ID);

        if ($check_for_admin and check_user_role(array('administrator', 'admin')) == false) {
            continue;
        }

        $description = get_field('additional_point', $point->ID);
        $image_description = get_field('additional_image_point', $point->ID);

        $description_security = get_field('additional_security', $point->ID);
        $image_security = get_field('image_security', $point->ID);
        $file_security = get_field('file_security', $point->ID);

        $scientist_articles = get_field('scientist_articles', $point->ID);

        $photo_video_point = get_field('photo_video_point', $point->ID);
        $images = array();
        $videos = array();

        if (isset($photo_video_point) and !empty($photo_video_point)) {
            foreach ($photo_video_point as $item) {
                $check = $item['photo_or_video'];
                if ($check) {
                    $images[] = array(
                        'url' => $item['photo']['url'],
                        'description' => $item['photo']['description']
                    );
                } else {
                    $videos[] = $item['video'];
                }
            }
        }

        $do_temi = get_field('galereya_photo_temi', $point->ID);

        $link_panorama = get_field('panorama_point', $point->ID);
        $view_3d = get_field('3d_image', $point->ID);
        $file_obj = $view_3d['fajl_obj'];
        $file_mtl = $view_3d['fajl_mtl'];
        $tekstura_3d = $view_3d['tekstura_svitlyna'];
        $file_kml = $coordinates['fajl_kml'];

        //english version
        $eng_post = pll_get_post($point->ID, 'en');
        $title_en = get_field('name_point', $eng_post);
        $subTitle_en = get_field('subtitle_point', $eng_post);
        $description_en = get_field('additional_point', $eng_post);
        $description_security_en = get_field('additional_security', $eng_post);
        $file_security_en = get_field('file_security', $eng_post);
        $scientist_articles_en = get_field('scientist_articles', $eng_post);

        //scientist articles
        $articles = array();
        if (!empty($scientist_articles)) {
            foreach ($scientist_articles as $key => $article) {
                $file_name = $article['file']['filename'];
                $file_url = $article['file']['url'];
                $image = $article['foto'];
                $text = $article['article'];

                $articles[] = array(
                    'file_name' => $file_name,
                    'file_url' => $file_url,
                    'image' => $image,
                    'text' => $text,
                    'eng_text' => $scientist_articles_en[$key]['article']
                );
            }
        }

        //do temi
        $do_temi_array = array();
        if (!empty($do_temi)) {
            foreach ($do_temi as $item) {

                $do_temi_array[] = array(
                    'url' => $item['url'],
                    'filename' => $item['filename']
                );
            }
        }

        //credentionals to delete
        $credentionalsToDelete = false;
        if ($point->post_author == get_current_user_id() or $credentionalsToEdit == true) {
            $credentionalsToDelete = true;
        }

        $pohovannya = get_field('3d_pohovanya', $point->ID);

        $pohovannya_array = array();
        if ($pohovannya == 'first') {
            $pohovannya_array[] = array(
                'url' => get_home_url(null, '', 'https') . '/Pano_Rodove_Pohovannya/index.html',
                'name' => 'Родине поховання'
            );
        } elseif ($pohovannya == 'second') {
            $pohovannya_array[] = array(
                'url' => get_home_url(null, '', 'https') . '/voinske-pokhovannya/index.html',
                'name' => 'Воїнське поховання'
            );
        }


        $point_json[] = array(
            'id_wp' => $point->ID,
            'user_status' => $userCap,
            'categories' => $cats,
            'tags' => $tags_string,
            'file_kml' => $file_kml,
            'audio' => $audio,
            '3d' => array(
                'file_obj' => $file_obj,
                'file_mtl' => $file_mtl,
                'tekstura' => $tekstura_3d,
                'url_to_3d_view' => get_home_url() . '/3d-view?3model_obj=' . $file_obj . '&3model_mtl=' . $file_mtl,
            ),
            'panorama' => array(
                'file_panorama' => $link_panorama,
                'url_to_panorama' => get_home_url() . '/panorama?link_panorama=' . $link_panorama
            ),
            'lang' => $lang,
            'lat' => $lat,
            'title' => $title,
            'title_en' => $title_en,
            'subtitle' => $subTitle,
            'subtitle_en' => $subTitle_en,
            'description' => $description,
            'description_en' => $description_en,
            'main_img' => $image_description,
            'image_security' => $image_security,
            'description_security' => $description_security,
            'file_security' => array(
                'url' => $file_security['url'],
                'name' => $file_security['filename']
            ),
            'description_security_en' => $description_security_en,
            'file_security_en' => array(
                'url' => $file_security_en['url'],
                'name' => $file_security_en['filename']
            ),
            'scientist_articles' => $articles,
            'gallery' => array(
                'images' => $images,
                'videos' => $videos
            ),
            'do_temi' => $do_temi_array,
            'credentionalsToEdit' => $credentionalsToEdit,
            'credentionalsToDelete' => $credentionalsToDelete,
            'pohovannya' => $pohovannya_array,
            'created_by' => $point->post_author,
            'status_point' => $point->post_status
        );

    endforeach;

    $result = array(
        'data' => $point_json
    );

    wp_send_json_success($result);
    wp_die();

}