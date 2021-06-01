<?php

// Сохранение меты
add_action('personal_options_update', 'functional_limit_user');
add_action('edit_user_profile_update', 'functional_limit_user');

function functional_limit_user($user_id)
{
    if (isset($_POST['limit'])) {
        update_user_meta($user_id, 'limit_user', $_POST['limit']);
    }
}

// Проверка на админа
if (current_user_can('administrator') or current_user_can('super_user')) {
// Добавление таблицы
    //add_action('show_user_profile', 'add_limit_form');
    //add_action('edit_user_profile', 'add_limit_form');

    function add_limit_form($user)
    {
        $limit = get_the_author_meta('limit_user', $user->ID);
        if (!$limit) {
            $limit = 10;
        }
        ?>
        <h3><?php _e('Лимит на точки'); ?></h3>
        <table class="form-table">
            <tr>
                <td><input type="number" min="0" max="1000" name="limit"
                           value="<?php echo $limit ?>"
                           class="radio"/>Задать лимит<br/>
                </td>
            </tr>
        </table>
        <?php
    }
}

// Проверка на лимит ( хук на сохранение точки )
//add_action('save_post_points', 'save_point_callback', 10, 3);
function save_point_callback($post_id, $post, $updated)
{
    if ($post->post_type != 'points') {
        return;
    }

    $user = wp_get_current_user();
    $limit = get_user_meta($user->ID, 'limit_user');
    $points = get_posts(array(
        'post_type' => 'points',
        'posts_per_page' => -1,
        'author' => $user->ID,
        'post_status' => 'any'
    ));


    if (isset($limit[0]) and $limit[0] < count($points)) {
        update_option('my_notifications', json_encode(array('error', 'Ваш лимит закончился, обратитесь к администратору.')));
        # And redirect
        $delete = wp_delete_post($post->ID,true);
        if (current_user_can('administrator')) {
            header('Location: ' . get_admin_url(null, 'edit.php?post_type=points'));
            exit;
        }else{
            return $_POST['error_limit'] = true;
        }
    }
}

/**
 *   Выводим нашу кастомную нотификацию
 */
function my_notification()
{
    $notifications = get_option('my_notifications');

    if (!empty($notifications)) {
        $notifications = json_decode($notifications);
        #notifications[0] = (string) Type of notification: error, updated or update-nag
        #notifications[1] = (string) Message
        #notifications[2] = (boolean) is_dismissible?
        switch ($notifications[0]) {
            case 'error': # red
            case 'updated': # green
            case 'update-nag': # ?
                $class = $notifications[0];
                break;
            default:
                # Defaults to error just in case
                $class = 'error';
                break;
        }

        $is_dismissable = '';
        if (isset($notifications[2]) && $notifications[2] == true)
            $is_dismissable = 'is_dismissable';

        echo '<div class="' . $class . ' notice ' . $is_dismissable . '">';
        echo '<p>' . $notifications[1] . '</p>';
        echo '</div>';

        # Let's reset the notification
        update_option('my_notifications', false);
    }
}
add_action('admin_notices', 'my_notification');


function limit_user_points(){
    $user = wp_get_current_user();
    $limit = get_user_meta($user->ID, 'limit_user');
    $points = get_posts(array(
        'post_type' => 'points',
        'posts_per_page' => -1,
        'author' => $user->ID,
        'post_status' => 'any'
    ));

    $access = 'no limit';

    if (isset($limit[0]) and $limit[0] <= count($points)){
        $access = 'limit';
    }
    return $access;
}