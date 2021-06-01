<!-- Wordpress loop -->
<!-- In data-points put marker's wordpress id's -->

<?php
$routes = get_posts(array(
    'post_type' => 'routes',
    'post_status' => 'publish',
    'numberposts' => -1
));
foreach ($routes as $route):

    $points = get_field('points_route', $route->ID);
    $count = array_key_last($points);
    if (empty($points)) {
        continue;
    }
    $points_string = '';
    foreach ($points as $key => $point) {
        if ($key == $count) {
            $points_string .= 'marker-wordpress-id-' . $point['point'];
        } else {
            $points_string .= 'marker-wordpress-id-' . $point['point'] . ', ';
        }
    }

    $kartynka_route = get_field('kartynka_route', $route->ID);
    if ($kartynka_route == '') {
        $kartynka_route = get_template_directory_uri() . '/html_templates/build/images/to-theme-1.png';
    }

    $bg_route = get_field('zadnyj_fon_route', $route->ID);
    if ($bg_route == '') {
        $bg_route = '';
    }

    ?>
    <div class="way"
         data-points="<?php echo $points_string; ?>"
         data-way-id="<?php echo $route->ID; ?>"
    >

        <div class="way__title">
            <h4>Маршрут</h4>
        </div>
        <!-- Background image url put from wordpress -->
        <div class="way__content"
             style="background-image: url('<?php echo $bg_route; ?>')">
            <img src="<?php echo $kartynka_route; ?>"
                 alt="image">
            <h3>
                <?php the_field('title_route', $route->ID); ?>
                <span><?php the_field('subtitle_route', $route->ID); ?></span>
                <?php
                $virtual_route = get_field('virtualnyj_marshrut', $route->ID);
                $real_route = get_field('realnyj_marshrut', $route->ID);
                ?>
                <?php if ($virtual_route): ?>
                    <span> Вiртуальний</span>
                <?php endif; ?>

                <?php if ($real_route): ?>
                    <span>Реальний</span>
                <?php endif; ?>
            </h3>
        </div>
    </div>
<?php endforeach; ?>
<?php wp_reset_postdata(); ?>
<!-- /Wordpress loop -->