<?php
/*
 * Template Name: recover page
 */
if (!$_GET['recover']){
    wp_die('Вам заборонено переглядати цей функціонал, будь ласка зверніться до розробників або адміністратора сайту');
}
$string = file_get_contents(get_template_directory_uri() . '/jsons/points.json');

$points = json_decode($string, true);
$start = $_GET['start'];
$end = $_GET['end'];
//var_dump($points['data']['data']);
foreach ($points['data']['data'] as $key => $point) {
    if ($key < $start) {
        continue;
    }
    if ($key > $end) {
        die();
    }
    $title_uk = $point['title'];
    $title_en = $point['title_en'];

    $subtitle = $point['subtitle'];
    $subtitle_en = $point['subtitle_en'];

    $description = $point['description'];
    $description_en = $point['description_en'];

    $main_img = $point['main_img'];
    $image_security = $point['image_security'];

    $view_3d = $point['3d'];
    $file_mtl = $view_3d['file_mtl'];
    $file_obj = $view_3d['file_obj'];
    $tekstura = $view_3d['tekstura'];

    $panorama = $point['panorama']['file_panorama'];

    $lang = $point['lat'];
    $lat = $point['lang'];

    $audio = $point['audio'];

    $file_kml = $point['file_kml'];

    $tags = $point['tags'];

    $image_security = $point['image_security'];

    $description_security = $point['description_security'];

    $file_security = $point['file_security'];
    $file_security_url = $file_security['url'];
    $file_security_name = $file_security['name'];

    $description_security_en = $point['description_security_en'];

    $file_security_en = $point['file_security_en'];
    $file_security_url_en = $file_security_en['url'];
    $file_security_name_en = $file_security_en['name'];

    //need fix
    $scientist_articles = $point['scientist_articles'];

    $gallery = $point['gallery'];
    $images_gallery = [];
    $videos_gallery = [];
    foreach ($gallery['images'] as $image) {
        $images_gallery[] = $image['url'];
    }
    foreach ($gallery['videos'] as $video) {
        $videos_gallery[] = $video;
    }

    $do_temi = $point['do_temi'];

    $pohovannya = $point['pohovannya'];


    $args = array(
        'post_title' => $title_uk,
        'post_type' => 'points',
        'post_status' => 'publish'
    );

    $pointIdUk = wp_insert_post(wp_slash($args), true);

    global $polylang;
    $args = array(
        'post_title' => $title_en,
        'post_status' => 'publish',
        'post_type' => 'points'
    );

    $pointIdEn = wp_insert_post(wp_slash($args), true);
    $polylang->model->set_post_language($pointIdEn, 'en');
    $polylang->model->save_translations('post', $pointIdUk, array('en' => $pointIdEn));

    /*
     * Ukr version
     */
    update_field('name_point', $title_uk, $pointIdUk);
    update_field('subtitle_point', $subtitle, $pointIdUk);
    update_field('displyay_not_logged_users', false, $pointIdUk);

    $values = array(
        'shyryna' => $lat,
        'dolgota' => $lang,

    );
    update_field('kordynaty_point', $values, $pointIdUk);

    update_field('additional_point', $description, $pointIdUk);

    $main_img_id = pippin_get_image_id($main_img);

    if ($main_img_id) {
        update_field('additional_image_point', $main_img_id, $pointIdUk);
    }

    update_field('additional_security', $description_security, $pointIdUk);

    $image_security_id = pippin_get_image_id($image_security);
    if ($image_security_id) {
        update_field('image_security', $image_security_id, $pointIdUk);
    }

    $file_security_id = pippin_get_image_id($image_security);
    if ($file_security_id) {
        update_field('file_security', $file_security_id, $pointIdUk);
    }


    $publications = array();
    foreach ($scientist_articles as $article) {
        $text = $article['text'];
        $photo = $article['image'];
        $file = $article['file_url'];

        $article_img_id = pippin_get_image_id($photo);
        if ($article_img_id) {
            $photo_upload = $article_img_id;
        } else {
            $photo_upload = '';
        }

        $article_file_id = pippin_get_image_id($file);
        if ($article_file_id) {
            $file_upload = $article_file_id;
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

    update_field('scientist_articles', $publications, $pointIdUk);

    $audio_id = pippin_get_image_id($audio);
    if ($audio_id) {
        update_field('audio_file_point', $audio_id, $pointIdUk);
    }

    $panorama_id = pippin_get_image_id($panorama);
    if ($panorama_id) {
        update_field('panorama_point', $panorama_id, $pointIdUk);
    }

    $file_mtl_id = pippin_get_image_id($file_mtl);
    $file_obj_id = pippin_get_image_id($file_obj);
    $tekstura_id = pippin_get_image_id($tekstura);
    if ($file_mtl_id and $file_obj_id) {

        $values_3d = array(
            'fajl_mtl' => $file_mtl_id,
            'fail_obj' => $file_obj_id,
            'tekstura_svitlyna' => $tekstura_id,
        );

        update_field('3d_image', $values_3d, $pointIdUk);
    }

    $photos_videos = array();
    $photos_gallery = $images_gallery;
    if (!empty($photos_gallery)) {
        foreach ($photos_gallery as $photo) {
            $photo_id = pippin_get_image_id($photo);
            if ($photo_id) {
                $arr = array(
                    'photo' => $photo_id,
                    'photo_or_video' => 1
                );
                $photos_videos[] = $arr;
            }
        }
    }


    if ($videos_gallery > 0) {
        foreach ($videos_gallery as $video) {
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
        update_field('photo_video_point', $photos_videos, $pointIdUk);
    }

    $other_resources = $do_temi;
    $other_resources_arr = array();
    if ($other_resources > 0) {
        foreach ($other_resources as $resource) {
            $other_resources_id = pippin_get_image_id($resource['url']);
            if ($other_resources_id) {
                $other_resources_arr[] = $other_resources_id;
            }
        }
    }

    if (!empty($other_resources_arr)) {
        update_field('galereya_photo_temi', $other_resources_arr, $pointIdUk);
    }

    if (!empty($pohovannya)) {
        update_field('3d_pohovanya', array('0'), $pointIdUk);
    }

    /*
     * End ukr version
     */
    update_field('name_point', $title_en, $pointIdEn);
    update_field('subtitle_point', $subtitle_en, $pointIdEn);
    update_field('displyay_not_logged_users', false, $pointIdEn);

    $values = array(
        'shyryna' => $lat,
        'dolgota' => $lang,

    );
    update_field('kordynaty_point', $values, $pointIdEn);

    update_field('additional_point', $description_en, $pointIdEn);

    $main_img_id = pippin_get_image_id($main_img);

    if ($main_img_id) {
        update_field('additional_image_point', $main_img_id, $pointIdEn);
    }

    update_field('additional_security', $description_security_en, $pointIdEn);

    $image_security_id = pippin_get_image_id($image_security);
    if ($image_security_id) {
        update_field('image_security', $image_security_id, $pointIdEn);
    }

    $file_security_id = pippin_get_image_id($image_security);
    if ($file_security_id) {
        update_field('file_security', $file_security_id, $pointIdEn);
    }


    $publications = array();
    foreach ($scientist_articles as $article) {
        $text = $article['eng_text'];
        $photo = $article['image'];
        $file = $article['file_url'];

        $article_img_id = pippin_get_image_id($photo);
        if ($article_img_id) {
            $photo_upload = $article_img_id;
        } else {
            $photo_upload = '';
        }

        $article_file_id = pippin_get_image_id($file);
        if ($article_file_id) {
            $file_upload = $article_file_id;
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

    update_field('scientist_articles', $publications, $pointIdEn);

    $audio_id = pippin_get_image_id($audio);
    if ($audio_id) {
        update_field('audio_file_point', $audio_id, $pointIdEn);
    }

    $panorama_id = pippin_get_image_id($panorama);
    if ($panorama_id) {
        update_field('panorama_point', $panorama_id, $pointIdEn);
    }

    $file_mtl_id = pippin_get_image_id($file_mtl);
    $file_obj_id = pippin_get_image_id($file_obj);
    $tekstura_id = pippin_get_image_id($tekstura);
    if ($file_mtl_id and $file_obj_id and $tekstura_id) {

        $values_3d = array(
            'fajl_mtl' => $file_mtl_id,
            'fail_obj' => $file_obj_id,
            'tekstura_svitlyna' => $tekstura_id,
        );

        update_field('3d_image', $values_3d, $pointIdEn);
    }

    $photos_videos = array();
    $photos_gallery = $images_gallery;
    if (!empty($photos_gallery)) {
        foreach ($photos_gallery as $photo) {
            $photo_id = pippin_get_image_id($photo);
            if ($photo_id) {
                $arr = array(
                    'photo' => $photo_id,
                    'photo_or_video' => 1
                );
                $photos_videos[] = $arr;
            }
        }
    }


    if ($videos_gallery > 0) {
        foreach ($videos_gallery as $video) {
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
        update_field('photo_video_point', $photos_videos, $pointIdEn);
    }

    $other_resources = $do_temi;
    $other_resources_arr = array();
    if ($other_resources > 0) {
        foreach ($other_resources as $resource) {
            $other_resources_id = pippin_get_image_id($resource);
            if ($other_resources_id) {
                $other_resources_arr[] = $other_resources_id;
            }
        }
    }

    if (!empty($other_resources_arr)) {
        update_field('galereya_photo_temi', $other_resources_arr, $pointIdEn);
    }

    if (!empty($pohovannya)) {
        update_field('3d_pohovanya', array('0'), $pointIdEn);
    }

    //cats update
    $categories = explode(',', $point['categories']);
    $cats_administrativ = [];
    $cats_cat_archeology = [];
    $cats_cat_pamiatka = [];
    $cats_chronology = [];
    $cats_view_pamiatk = [];

    foreach ($categories as $category) {
        $aministrativ = 'administrativ_';
        $pamiatka = 'cat_pamiatka_';
        $archeology = 'cat_archeology_';
        $view_pamiatk = 'view_pamiatk_';
        $chronology = 'chronology_';

        if (strpos($category, $aministrativ) !== false) {
            $category = str_replace($aministrativ, "", $category);
            $cat_id = get_term_by('slug',$category,'administrativ');
            $cats_administrativ[] = $cat_id->term_id;
        }


        if (strpos($category, $archeology) !== false) {
            $category = str_replace($archeology, "", $category);
            $cat_id = get_term_by('slug',$category,'cat_archeology');
            $cats_cat_archeology[] = $cat_id->term_id;
        }


        if (strpos($category, $pamiatka) !== false) {
            $category = str_replace($pamiatka, "", $category);
            $cat_id = get_term_by('slug',$category,'cat_pamiatka');
            $cats_cat_pamiatka[] = $cat_id->term_id;
        }


        if (strpos($category, $view_pamiatk) !== false) {
            $category = str_replace($view_pamiatk, "", $category);
            $cat_id = get_term_by('slug',$category,'view_pamiatk');
            $cats_view_pamiatk[] = $cat_id->term_id;
        }


        if (strpos($category, $chronology) !== false) {
            $category = str_replace($chronology, "", $category);
            $cat_id = get_term_by('slug',$category,'chronology');
            $cats_chronology[] = $cat_id->term_id;
        }
    }


    update_field('administrativ_taxonomy_point',$cats_administrativ,$pointIdUk);
    update_field('administrativ_taxonomy_point',$cats_administrativ,$pointIdEn);


    update_field('cat_pamiatka_taxonomy_point',$cats_cat_pamiatka,$pointIdUk);
    update_field('cat_pamiatka_taxonomy_point',$cats_cat_pamiatka,$pointIdEn);


    update_field('view_pamiatk_taxonomy_point',$cats_view_pamiatk,$pointIdUk);
    update_field('view_pamiatk_taxonomy_point',$cats_view_pamiatk,$pointIdEn);


    update_field('cat_archeology_taxonomy_point',$cats_cat_archeology,$pointIdUk);
    update_field('cat_archeology_taxonomy_point',$cats_cat_archeology,$pointIdEn);


    update_field('chronology_taxonomy_point',$cats_chronology,$pointIdUk);
    update_field('chronology_taxonomy_point',$cats_chronology,$pointIdEn);

}