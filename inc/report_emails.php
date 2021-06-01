<?php
// Функционал для создания репортов от email
function render_report()
{
    $reports = get_posts(array(
        'post_type' => 'report',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    if (count($reports) <= 3) {
        for ($i = 1; $i <= 3; $i++) {
            $args = array(
                'post_title' => 'REPORT',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'report'
            );
            $post_id = wp_insert_post($args);
            update_field('ymya_zapit',"REPORT$i",  $post_id);
            update_field('email_zapit',"EMAIL$i",  $post_id);
            update_field('tekst_zapit',"TEXT$i",  $post_id);
        }
    }
}

add_action('init',render_report());
