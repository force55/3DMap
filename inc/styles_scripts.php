<?php
function replace_core_jquery_version()
{
    wp_deregister_script('jquery');
    $path = get_stylesheet_directory_uri() . '/html_templates/build';

    wp_register_script('jquery', $path . "/js/vendors/jquery.min.js", array(), '3.5.1');
}

add_action('wp_enqueue_scripts', 'replace_core_jquery_version');

function theme_name_scripts()
{
    $path = get_stylesheet_directory_uri() . '/html_templates/build';
    // Подключаем стили
    wp_enqueue_style('style-theme', get_stylesheet_uri());
    wp_enqueue_style('style', $path . '/css/style.min.css');

    // Подключаем скрипты
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui', $path . '/js/vendors/jquery-ui.min.js', null, null, true);
    wp_enqueue_script('anime', $path . '/js/vendors/anime.min.js', null, null, true);
    wp_enqueue_script('geocoordsparser', $path . '/js/vendors/geocoordsparser.js', null, null, true);
    wp_enqueue_script('maptalks', $path . '/js/vendors/maptalks.min.js', null, null, true);
    wp_enqueue_script('maptalks-format', $path . '/js/vendors/maptalks.formats.min.js', null, null, true);
    wp_enqueue_script('slick', $path . '/js/vendors/slick.min.js', null, null, true);
    wp_enqueue_script('simplebar', $path . '/js/vendors/simplebar.min.js', null, null, true);
    wp_enqueue_script('custom-script', $path . '/js/script.min.js', null, null, true);
//    wp_enqueue_script('CustomScrollbar', $path . '/js/vendors/jquery.mCustomScrollbar.concat.min.js', null, null, true);
    wp_enqueue_script('aframe-master.min', $path . '/js/vendors/aframe-master.min.js', null, null, false);
    wp_enqueue_script('aframe-orbit-controls-component', $path . '/js/vendors/aframe-orbit-controls-component.min.js', null, null, false);
    wp_enqueue_script('script-theme_custom', get_template_directory_uri() . '/custom_js.js', null, null, true);

    wp_localize_script('script-theme_custom', 'myajax',
        array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('myajax-nonce')
        )
    );
}

add_action('wp_enqueue_scripts', 'theme_name_scripts');
