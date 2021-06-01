<?php

add_theme_support('menus');


if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title' => 'Налаштування теми',
        'menu_title' => 'Налаштування теми',
        'menu_slug' => 'theme-general-settings',
        'capability' => 'edit_posts',
        'redirect' => false,
    ));
}

add_filter('excerpt_more', function ($more) {
    return '';
});

// Инциализация глобальной перемены
defined('MAP_URI') or define('MAP_URI', get_template_directory_uri());

// Убираем все статусы кроме Publish в выборе точек
function relationship_options_filter($options, $field, $the_post)
{
    $options['post_status'] = array('publish');
    return $options;
}

add_filter('acf/fields/post_object/query/name=point', 'relationship_options_filter', 10, 3);

/*
 *  Поключение файлов
 */
// Подключаем фукнкционал блокировки
include 'inc/functional_block.php';
// Подключаем кастом пост тайпы
include 'inc/cpt.php';
// Подключаем скрипты и стили
include 'inc/styles_scripts.php';
// Подключаем функционал лимита
include 'inc/limit.php';
// Подключаем функционал для дополнительной роли
include 'inc/admin_user.php';

// render block search filter
function render_search_filter($taxonomy_name = "")
{
    $administrativ_podil_taxonomy = get_terms([
        'taxonomy' => $taxonomy_name,
        'hide_empty' => false,
    ]);
    $taxonomy = get_taxonomy($taxonomy_name);
    if (!empty($administrativ_podil_taxonomy)):
        ?>
        <div class="b-filter">
            <div class="b-filter__title"><?php echo $taxonomy->label; ?></div>
            <div class="b-filter__list">
                <?php foreach ($administrativ_podil_taxonomy as $cat): ?>
                    <label>
                        <input type="checkbox" name="<?php echo $cat->taxonomy . '_' . $cat->slug; ?>">
                        <span><?php echo $cat->name; ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif;
}

// Подключаем функционал точек ( данные )
include 'inc/data_points.php';

//Кастомизации админки и убирание доступа в админку для юзера
include 'inc/customize_admin_for_user.php';

// Тестовые репорты для проверки, в будущем переработать на отправление имейла и внедрения репортов
//include 'inc/report_emails.php';

// Функционал проверки роли
function check_user_role($roles, $user_id = null)
{
    if ($user_id) $user = get_userdata($user_id);
    else $user = wp_get_current_user();
    if (empty($user)) return false;
    foreach ($user->roles as $role) {
        if (in_array($role, $roles)) {
            return true;
        }
    }
    return false;
}

// retrieves the attachment ID from the file URL
function pippin_get_image_id($image_url)
{
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url));
    return $attachment[0];
}

//Email отправка
include 'inc/emails.php';

//CRUD для точек
include 'inc/crud_points.php';

//  Взятие всех точек
include 'inc/get_points.php';

//CRUD для маршрутов
include 'inc/crud_routes.php';

// Взятие всех маршрутов
include 'inc/get_routes.php';

// Last key for array
if (!function_exists("array_key_last")) {
    function array_key_last($array)
    {
        if (!is_array($array) || empty($array)) {
            return NULL;
        }

        return array_keys($array)[count($array) - 1];
    }
}

// get roles from user ( custom function )
function get_current_role($id_user)
{
    $user = get_userdata($id_user);
    return $user->roles;
}

// get text for description meta
