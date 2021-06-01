<?php
add_action('init', 'custom_post_types');
function custom_post_types(){
    register_post_type('points', array(
        'labels'             => array(
            'name'               => 'Точки', // Основное название типа записи
            'singular_name'      => 'Точка', // отдельное название записи типа Book
            'add_new'            => 'Додати нову',
            'add_new_item'       => 'Додати нову точку',
            'edit_item'          => 'Редагувати точку',
            'new_item'           => 'Нова точка',
            'view_item'          => 'Переглянути точку',
            'search_items'       => 'Знайти точку',
            'not_found'          => 'Точку не знайдено',
            'parent_item_colon'  => '',
            'menu_name'          => 'Точки'

        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title','author','revisions')
    ) );

    register_post_type('routes', array(
        'labels'             => array(
            'name'               => 'Маршрути', // Основное название типа записи
            'singular_name'      => 'Маршрут', // отдельное название записи типа Book
            'add_new'            => 'Додати новий',
            'add_new_item'       => 'Додати новий Маршрут',
            'edit_item'          => 'Редагувати Маршрут',
            'new_item'           => 'Новий Маршрут',
            'view_item'          => 'Переглянути Маршрут',
            'search_items'       => 'Знайти Маршрут',
            'not_found'          => 'Маршрут не знайдено',
            'parent_item_colon'  => '',
            'menu_name'          => 'Маршрути'

        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title','author','revisions')
    ) );

    register_post_type('report', array(
        'labels'             => array(
            'name'               => 'Запити', // Основное название типа записи
            'singular_name'      => 'Запит', // отдельное название записи типа Book
            'add_new'            => 'Додати новий',
            'add_new_item'       => 'Додати новий запит',
            'edit_item'          => 'Редагувати запит',
            'new_item'           => 'Новий запит',
            'view_item'          => 'Переглянути запит',
            'search_items'       => 'Знайти запит',
            'not_found'          => 'Запит не знайдено',
            'parent_item_colon'  => '',
            'menu_name'          => 'Запити'

        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => true,
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title')
    ) );
}

add_action( 'init', 'create_tag_taxonomies', 0 );

//create two taxonomies, genres and tags for the post type "tag"
function create_tag_taxonomies()
{
    // Add new taxonomy, NOT hierarchical (like tags)
    $labels = array(
        'name' => _x( 'Tags', 'taxonomy general name' ),
        'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Tags' ),
        'popular_items' => __( 'Popular Tags' ),
        'all_items' => __( 'All Tags' ),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __( 'Edit Tag' ),
        'update_item' => __( 'Update Tag' ),
        'add_new_item' => __( 'Add New Tag' ),
        'new_item_name' => __( 'New Tag Name' ),
        'separate_items_with_commas' => __( 'Separate tags with commas' ),
        'add_or_remove_items' => __( 'Add or remove tags' ),
        'choose_from_most_used' => __( 'Choose from the most used tags' ),
        'menu_name' => __( 'Tags' ),
    );

    register_taxonomy('tag','points',array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array( 'slug' => 'tag' ),
    ));
}
// хук для регистрации
add_action( 'init', 'create_taxonomy_administrativ' );
function create_taxonomy_administrativ(){

    register_taxonomy( 'administrativ', [ 'points' ], [
        'label'                 => 'Райони', // определяется параметром $labels->name
        'labels'                => [
            'name'              => 'Адміністративно-територіальний поділ',
            'singular_name'     => 'Район',
            'search_items'      => 'Пошук районiв',
            'all_items'         => 'Усi райони',
            'view_item '        => 'Переглянути район',
            'parent_item'       => 'Батькiвськiй район',
            'parent_item_colon' => 'Батькiвськiй район:',
            'edit_item'         => 'Редагувати район',
            'update_item'       => 'Оновоти район',
            'add_new_item'      => 'Додати новий район',
            'new_item_name'     => 'Имя нового района',
            'menu_name'         => 'Адміністративно-територіальний поділ',
        ],
        'description'           => 'Адміністративно-територіальний поділ', // описание таксономии
        'public'                => true,
        'hierarchical'          => true,
        'show_ui'       => true,
        'query_var'     => true,
    ] );
}
// хук для регистрации
add_action( 'init', 'create_taxonomy_cat_pamiatka' );
function create_taxonomy_cat_pamiatka(){

    register_taxonomy( 'cat_pamiatka', [ 'points' ], [
        'label'                 => 'Категорiя пам’ятки', // определяется параметром $labels->name
        'labels'                => [
            'name'              => 'Категорiя пам’ятки',
            'singular_name'     => 'Категорiя',
            'search_items'      => 'Пошук категорії',
            'all_items'         => 'Усi категорії',
            'view_item '        => 'Переглянути Категорiю',
            'parent_item'       => 'Батькiвськя Категорiя',
            'parent_item_colon' => 'Батькiвськя Категорiя:',
            'edit_item'         => 'Редагувати Категорiю',
            'update_item'       => 'Оновоти Категорiю',
            'add_new_item'      => 'Додати нову Категорiю',
            'new_item_name'     => 'Ім\'я нової категорії',
            'menu_name'         => 'Категорiя пам’ятки',
        ],
        'description'           => 'Категория пам’ятки', // описание таксономии
        'public'                => true,
        'hierarchical'          => true,
        'show_ui'       => true,
        'query_var'     => true,
    ] );
}
// хук для регистрации
add_action( 'init', 'create_taxonomy_view_pamiatka' );
function create_taxonomy_view_pamiatka(){

    register_taxonomy( 'view_pamiatk', [ 'points' ], [
        'label'                 => 'Вид пам’ятки', // определяется параметром $labels->name
        'labels'                => [
            'name'              => 'Вид пам’ятки',
            'singular_name'     => 'Категорiя',
            'search_items'      => 'Пошук категорії',
            'all_items'         => 'Усi категорії',
            'view_item '        => 'Переглянути Категорiю',
            'parent_item'       => 'Батькiвськя Категорiя',
            'parent_item_colon' => 'Батькiвськя Категорiя:',
            'edit_item'         => 'Редагувати Категорiю',
            'update_item'       => 'Оновоти Категорiю',
            'add_new_item'      => 'Додати нову Категорiю',
            'new_item_name'     => 'Ім\'я нової категорії',
            'menu_name'         => 'Вид пам’ятки',
        ],
        'description'           => 'Вид пам’ятки', // описание таксономии
        'public'                => true,
        'hierarchical'          => true,
        'show_ui'       => true,
        'query_var'     => true,
    ] );
}
// хук для регистрации
add_action( 'init', 'create_taxonomy_cat_archeology' );
function create_taxonomy_cat_archeology(){

    register_taxonomy( 'cat_archeology', [ 'points' ], [
        'label'                 => 'Тип (археологія)', // определяется параметром $labels->name
        'labels'                => [
            'name'              => 'Тип (археологія)',
            'singular_name'     => 'Категорiя',
            'search_items'      => 'Пошук категорії',
            'all_items'         => 'Усi категорії',
            'view_item '        => 'Переглянути Категорiю',
            'parent_item'       => 'Батькiвськя Категорiя',
            'parent_item_colon' => 'Батькiвськя Категорiя:',
            'edit_item'         => 'Редагувати Категорiю',
            'update_item'       => 'Оновоти Категорiю',
            'add_new_item'      => 'Додати нову Категорiю',
            'new_item_name'     => 'Ім\'я нової категорії',
            'menu_name'         => 'Тип (археологія)',
        ],
        'description'           => 'Тип (археологія)', // описание таксономии
        'public'                => true,
        'hierarchical'          => true,
        'show_ui'       => true,
        'query_var'     => true,
    ] );
}
// хук для регистрации
add_action( 'init', 'create_taxonomy_cat_chonologyy' );
function create_taxonomy_cat_chonologyy(){

    register_taxonomy( 'chronology', [ 'points' ], [
        'label'                 => 'Хронологія', // определяется параметром $labels->name
        'labels'                => [
            'name'              => 'Хронологія',
            'singular_name'     => 'Категорiя',
            'search_items'      => 'Пошук категорії',
            'all_items'         => 'Усi категорії',
            'view_item '        => 'Переглянути Категорiю',
            'parent_item'       => 'Батькiвськя Категорiя',
            'parent_item_colon' => 'Батькiвськя Категорiя:',
            'edit_item'         => 'Редагувати Категорiю',
            'update_item'       => 'Оновоти Категорiю',
            'add_new_item'      => 'Додати нову Категорiю',
            'new_item_name'     => 'Ім\'я нової категорії',
            'menu_name'         => 'Хронологія',
        ],
        'description'           => 'Хронологія', // описание таксономии
        'public'                => true,
        'hierarchical'          => true,
        'show_ui'       => true,
        'query_var'     => true,
    ] );
}