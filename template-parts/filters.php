<div class="filters">
    <form action="#" class="filter-form">
        <?php
        render_search_filter('administrativ');
        render_search_filter('cat_pamiatka');
        render_search_filter('view_pamiatk');
        ?>
        <div class="ways-window">
            <div class="ways-window__title">
                <img src="<?php echo get_template_directory_uri()?>/html_templates/build/images/window-ways-icon.png" alt="icon">
                <h4>Археологічні маршрути</h4>
            </div>
            <div class="ways-window__ways">
                <ul></ul>
            </div>
        </div>
        <?php
        render_search_filter('cat_archeology');
        render_search_filter('chronology');
        ?>
    </form>
</div>