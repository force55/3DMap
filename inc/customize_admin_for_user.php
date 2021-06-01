<?php
// Remove update notifications
function hide_update_notice_to_all_but_admin_users()
{
    if (!current_user_can('update_core')) {
        remove_action('admin_notices', 'update_nag', 3);
    }
}

add_action('admin_head', 'hide_update_notice_to_all_but_admin_users', 1);

//Редирект для обычного юзера
function user_admin_redirect()
{
    if (is_admin() && !defined('DOING_AJAX') && (current_user_can('subscriber') || current_user_can('contributor'))) {
        wp_redirect(home_url());
        exit;
    }
}

add_action('init', 'user_admin_redirect');

// Update CSS within in Admin
function admin_style()
{
    if (current_user_can('super_user')) {
        wp_enqueue_style('admin-styles', get_template_directory_uri() . '/custom_admin.css');
    }
}

add_action('admin_enqueue_scripts', 'admin_style');

//убираем админ бар для юзера
if (current_user_can('subscriber')) {
    add_filter('show_admin_bar', '__return_false');
}

// Убираем элементы с админ панели
add_action('admin_init', 'my_remove_menu_pages');
function my_remove_menu_pages()
{
    if (current_user_can('super_user')) {
        remove_menu_page('upload.php');
        remove_menu_page('index.php'); //dashboard
    }
}

// Убираем доступ к странице dashboard
add_action('admin_init', 'remove_dashboard_page');
function remove_dashboard_page()
{
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $check_link =  admin_url();
    if (current_user_can('super_user' ) and $actual_link == $check_link ) {
        wp_redirect( admin_url() .'/edit.php?post_type=page');
    }
}

// убираем колонку комментариев
function remove_pages_count_columns($defaults) {
    unset($defaults['comments']);
    return $defaults;
}
add_filter('manage_pages_columns', 'remove_pages_count_columns');

// Меняем лого на странице логина
function my_login_logo()
{
    $image = get_template_directory_uri().'/logo.png';
    echo '<style type=text/css>' .
        'h1 a {
            background-image:url('.$image.') !important;
            height:125px !important;
            width:105px !important;
            background-size:100% !important;
            line-height:inherit !important;
        }' .
        '</style>';
}

add_action('login_head', 'my_login_logo');

//Убираем кнопку добавить с админ бара
add_action( 'admin_bar_menu', function( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'new-content' );
},999);