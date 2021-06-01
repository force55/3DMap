<?php

// Сохранение меты
add_action('personal_options_update', 'functional_block_user');
add_action('edit_user_profile_update', 'functional_block_user');

function functional_block_user($user_id)
{
    if (isset($_POST['block']) and $_POST['block'] == 'true') {
        update_user_meta($user_id, 'block_user', true);
    } else {
        update_user_meta($user_id, 'block_user', false);
    }
}

// Проверка на админа
if (current_user_can('administrator') or current_user_can('super_user')) {
// Добавление таблицы
    add_action('show_user_profile', 'add_block_form');
    add_action('edit_user_profile', 'add_block_form');

    function add_block_form($user)
    {
        ?>
        <h3><?php _e('Статус Блокировки'); ?></h3>
        <table class="form-table">
            <tr>
                <td><input type="radio" name="block"
                           value="true" <?php if (true == esc_attr(get_the_author_meta('block_user', $user->ID))) echo 'checked="checked"'; ?>
                           class="radio"/>Блокировать<br/>
                    <input type="radio" name="block" value="false"
                        <?php if (false == esc_attr(get_the_author_meta('block_user', $user->ID))) echo 'checked="checked"'; ?>
                           class="radio"/>Разблокировать<br/>
                </td>
            </tr>
        </table>
        <?php
    }
}

// Проверка на блокированный аккаунт
add_action('wp_login', 'check_block_user', 10, 2);
add_action('init', 'check_block_user');
function check_block_user()
{
    $user = wp_get_current_user();
    $block = get_user_meta($user->ID, 'block_user');

    if (isset($block[0]) && $block[0] == '1') {
        $error = new WP_Error();
        $user = new WP_Error('authentication_failed', 'Ваш аккаунт заблокирован, вернуться на <a href="' . home_url() . '">главную</a>');
        wp_logout();
        wp_die($user);

    }
}