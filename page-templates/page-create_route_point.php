<?php
/*
 * Template Name: Temp page for user
 */
?>
<?php acf_form_head(); ?>
<?php acf_enqueue_uploader(); ?>
<?php get_header(); ?>
<?php if (!is_user_logged_in()): ?>
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <p class="limit hidden"><?php echo limit_user_points(); ?></p>
            <?php while (have_posts()) : the_post(); ?>
                <?php acf_form(array(
                    'post_id' => 'new_post',
                    'post_title' => true,
                    'new_post' => array(
                        'post_type' => 'points',
                        'post_status' => 'pending'
                    ),
                    'submit_value' => 'Create new post'
                )); ?>
            <?php endwhile; ?>
        </div><!-- #content -->
    </div><!-- #primary -->

<?php else: ?>
    <h2>Please authorize in site</h2>
    <p><a href="<?php echo wp_login_url(); ?>"> SIGN IN</a></p>
    <?php
    if (function_exists('wptelegram_login')) {
        wptelegram_login();
    }
    ?>
<?php endif; ?>
<?php get_footer(); ?>
