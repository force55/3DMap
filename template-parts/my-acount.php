<h3>Особистий кабінет</h3>
<?php
$favoriutes_routes = false;
$args = array(
    'post_type' => 'routes',
    'numberposts' => -1,
    'post_status' => 'publish,private',
    'meta_query' => array(
        array(
            'key' => 'vaforite_route_' . get_current_user_id(),
            'value' => '1',
            'compare' => '=',
        )
    )
);

$favoriutes_routes = get_posts($args);
?>
<div class="favorite-routes">
    <h4>Улюблені маршрути</h4>
    <div class="favorite-routes__list">
        <ul>
            <?php
            foreach ($favoriutes_routes as $route):
                $points = get_field('points_route', $route->ID);
                $points_string = '';
                $count = array_key_last($points);
                foreach ($points as $key => $point) {
                    if ($key == $count) {
                        $points_string .= 'marker-wordpress-id-' . $point['point'];
                    } else {
                        $points_string .= 'marker-wordpress-id-' . $point['point'] . ', ';
                    }
                }

                $edit = '';
                if ($route->post_status != 'publish' and $route->post_author == get_current_user_id()) {
                    $edit = '<a href="#" class="edit-route">Редагувати</a>';
                }

                ?>
                <li>
                    <?php //echo $edit;
                    ?>
                    <a href="#" data-points="<?php echo $points_string; ?>" data-way-id="<?php echo $route->ID; ?>"
                       data-wp-id="<?php echo $route->ID; ?>">
                        <?php the_field('title_route', $route->ID); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php
$args = array(
    'post_type' => 'routes',
    'numberposts' => -1,
    'author' => get_current_user_id(),
    'post_status' => 'publish,private',
    'lang' => 'uk'
);

$my_routes = get_posts($args);
?>
<div class="user-routes">
    <h4>Власні маршрути</h4>
    <div class="user-routes__list">
        <ul>
            <?php foreach ($my_routes as $route):
                $points = get_field('points_route', $route->ID);

                if (empty($points)) {
                    continue;
                }
                $points_string = '';
                $count = array_key_last($points);
                foreach ($points as $key => $point) {
                    if ($key == $count) {
                        $points_string .= 'marker-wordpress-id-' . $point['point'];
                    } else {
                        $points_string .= 'marker-wordpress-id-' . $point['point'] . ', ';
                    }
                }

                $edit = '';
                if ($route->post_status != 'publish') {
                    $edit = '<a href="#" class="edit-route">Редагувати</a>';
                }


                ?>
                <li>
                    <?php //echo $edit;
                    ?>
                    <a href="#" data-points="<?php echo $points_string; ?>" data-way-id="<?php echo $route->ID; ?>"
                       data-wp-id="<?php echo $route->ID; ?>">
                        <?php the_field('title_route', $route->ID); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<div class="decorator">
    <img src="<?php echo get_template_directory_uri() ?>/html_templates/build/images/routes-decorator.png"
         alt="routes decorator">
</div>
<form action="#" class="purpose-route-form">
    <h4 class="create-route-title">
        <span>Створити</span>
        <span>власний</span>
        <span>маршрут</span>
    </h4>
    <div class="fields-group">
        <div class="fields">
            <input type="text" id="route-name" name="route-name" placeholder="Назва..................">
            <textarea name="route-description-ua">Опис Українською..................</textarea>
            <textarea name="route-description-en">Опис Англійською..................</textarea>
            <!-- Options will be generating via javascript -->
            <div class="selects">
                <div>
                    <select name="route-coordinate-1">
                        <option value="0" selected disabled>Додати точку..................</option>
                    </select>
                </div>
            </div>
        </div>
        <a href="#" class="add-more-point">+</a>
        <button type="submit" id="submit_route">Зберегти</button>
        <input type="checkbox" name="purpose-to-publication" id="purpose-to-publication">
        <label for="purpose-to-publication" class="info">Запропонувати до публікації на мапі</label>
    </div>
</form>
<a href="#" class="create-object-btn">Створити об’єкт</a>