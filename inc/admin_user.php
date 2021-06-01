<?php
// Удаляем роль при деактивации нашей темы
add_action( 'switch_theme', 'deactivate_my_theme' );
function deactivate_my_theme() {
    remove_role( 'super_user' );
}

// Добавляем роль при активации нашей темы
add_action( 'after_switch_theme', 'activate_my_theme' );
function activate_my_theme() {
    add_role( 'super_user', 'Админ',
        array(
            'read'         => true,  // true разрешает эту возможность
            'edit_posts'   => true,  // true разрешает редактировать посты
            'upload_files' => true,  // может загружать файлы
        )
    );
}
