<?php
//get all points
function get_points()
{
    $points = get_posts([
        'post_type' => 'points',
        'posts_per_page' => -1
    ]);

    $result = array();

    foreach ($points as $point) {

        $coordinates = get_field('kordynaty_point', $point->ID);
        if (empty($coordinates) or !$coordinates) {
            // continue;
        }

        $passport = get_field('pasport_point', $point->ID);
        $links = get_field('urls_point', $point->ID);
        $links_result = array();

        if (!empty($links) && isset($links)) {
            foreach ($links as $link) {
                $links_result[] = $link['link'];
            }
        }

        $photo_video_point = get_field('photo_video_point', $point->ID);
        $photo_video_point_result = array();

        if (!empty($photo_video_point) && isset($photo_video_point)) {
            foreach ($photo_video_point as $item) {
                if ($item['photo_or_video']) {
                    $object = array(
                        'status' => 'photo',
                        'url' => $item['photo']
                    );
                } else {
                    $object = array(
                        'status' => 'video',
                        'url' => $item['video']
                    );
                }
                $photo_video_point_result[] = (object)$object;
            }
        }

        $scientist_articles = get_field('scientist_articles', $point->ID);
        $scientist_articles_result = array();

        if (!empty($scientist_articles) && isset($scientist_articles)) {
            foreach ($scientist_articles as $article) {
                $scientist_articles_result[] = $article['article'];
            }
        }

        $articles_security = get_field('articles_security', $point->ID);
        $articles_security_result = array();

        if (!empty($articles_security) && isset($articles_security)) {
            foreach ($articles_security as $article) {
                $object = array(
                    'text' => $article['text'],
                    'file' => $article['text']
                );

                $articles_security_result[] = (object)$object;
            }
        }


        $administrativ_taxonomy = get_field('administrativ_taxonomy_point', $point->ID);
        $administrativ_taxonomy_result = array();

        $cat_pamiatka_taxonomy = get_field('cat_pamiatka_taxonomy_point', $point->ID);
        $cat_pamiatka_taxonomy_result = array();

        $view_pamiatk_taxonomy = get_field('view_pamiatk_taxonomy_point', $point->ID);
        $view_pamiatk_taxonomy_result = array();

        $cat_archeology_taxonomy = get_field('cat_archeology_taxonomy_point', $point->ID);
        $cat_archeology_taxonomy_result = array();

        $chronology_taxonomy = get_field('chronology_taxonomy_point', $point->ID);
        $chronology_taxonomy_result = array();

        if (!empty($administrativ_taxonomy) && $administrativ_taxonomy) {
            foreach ($administrativ_taxonomy as $item) {
                $administrativ_taxonomy_result[] = 'administrativ_' . $item->slug;
            }
        }

        if (!empty($cat_pamiatka_taxonomy) && $cat_pamiatka_taxonomy) {
            foreach ($cat_pamiatka_taxonomy as $item) {
                $cat_pamiatka_taxonomy_result[] = 'cat_pamiatka_' . $item->slug;
            }
        }

        if (!empty($view_pamiatk_taxonomy) && $view_pamiatk_taxonomy) {
            foreach ($view_pamiatk_taxonomy as $item) {
                $view_pamiatk_taxonomy_result[] = 'view_pamiatk_' . $item->slug;
            }
        }

        if (!empty($cat_archeology_taxonomy) && $cat_archeology_taxonomy) {
            foreach ($cat_archeology_taxonomy as $item) {
                $cat_archeology_taxonomy_result[] = 'cat_archeology_' . $item->slug;
            }
        }

        if (!empty($chronology_taxonomy) && $chronology_taxonomy) {
            foreach ($chronology_taxonomy as $item) {
                $chronology_taxonomy_result[] = 'chronology_' . $item->slug;
            }
        }

        $point = array(
            'coordinates' =>
                [
                    'lat' => $coordinates['shyryna'],
                    'lang' => $coordinates['dolgota']
                ],
            'name_point' => get_field('name_point', $point->ID),
            'dovidka' => get_field('additional_point', $point->ID),
            'passport' =>
                [
                    'name' => $passport['name_pasport'],
                    'image' => $passport['image_pasport']
                ],
            'files' => get_field('files_point', $point->ID),
            'links' => $links_result,
            'photos_video' => $photo_video_point_result,
            'scientist_articles' => $scientist_articles_result,
            'articles_security' => $articles_security_result,
            '3d_image' => get_field('3d_image', $point->ID),
            'panorama_image' => get_field('panorama_point', $point->ID),
            'audio_file' => get_field('audio_file_point', $point->ID),
            'graphic_image' => get_field('graphic_image_point', $point->ID),
            'displyay_not_logged_users' => get_field('displyay_not_logged_users', $point->ID),
            'tags' => get_field('tags_point', $point->ID),
            'administrativ_taxonomy' => $administrativ_taxonomy_result,
            'cat_pamiatka_taxonomy' => $cat_pamiatka_taxonomy_result,
            'view_pamiatk_taxonomy' => $view_pamiatk_taxonomy_result,
            'cat_archeology_taxonomy' => $cat_archeology_taxonomy_result,
            'chronology_taxonomy' => $chronology_taxonomy_result

        );
        $result[] = (object)$point;
    }

    return json_encode((object)$result);
}

function render_categories($id)
{
    $administrativ_taxonomy = get_field('administrativ_taxonomy_point', $id);

    $cat_pamiatka_taxonomy = get_field('cat_pamiatka_taxonomy_point', $id);

    $view_pamiatk_taxonomy = get_field('view_pamiatk_taxonomy_point', $id);

    $cat_archeology_taxonomy = get_field('cat_archeology_taxonomy_point', $id);

    $chronology_taxonomy = get_field('chronology_taxonomy_point', $id);

    $allTaxonomies = array_merge(
        (array)$administrativ_taxonomy,
        (array)$cat_archeology_taxonomy,
        (array)$cat_pamiatka_taxonomy,
        (array)$chronology_taxonomy,
        (array)$view_pamiatk_taxonomy
    );

    $result = '';
    foreach ($allTaxonomies as $taxonomy){

        if (empty($taxonomy)){
            continue;
        }

        if (end($allTaxonomies) != $taxonomy) {
            $result .= $taxonomy->taxonomy . '_' . $taxonomy->slug .',';
        }else{
            $result .= $taxonomy->taxonomy . '_' . $taxonomy->slug;
        }
    }

    return $result;

}