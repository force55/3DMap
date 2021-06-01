<?php
$routes = get_posts(array(
    'post_type' => 'routes',
    'post_status' => 'publish',
    'numberposts' => -1,
));
?>
<div class="ways-window">
    <div class="ways-window__title">
        <img src="<?php echo get_template_directory_uri() ?>/html_templates/build/images/window-ways-icon.png"
             alt="icon">
        <h4>Археологічні маршрути</h4>
    </div>
    <div class="ways-window__ways">
        <ul></ul>
    </div>
</div>