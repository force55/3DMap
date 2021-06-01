<div class="login-window">
    <div class="login-window__bg"></div>
    <div class="login-form-container">
        <h3>ОСОБИСТИЙ КАБІНЕТ</h3>
        <form action="" class="login-form">
            <?php if (!is_user_logged_in()):?>
            <a href="#" class="enter-btn enter-btn--email"><i class="icon icon--email"></i>увійти через
                Gmail</a>
            <a href="#" class="enter-btn enter-btn--facebook"><i class="icon icon--facebook"></i>увійти через
                Facebook</a>
            <a href="#" class="enter-btn enter-btn--telegram"><i class="icon icon--telegram"></i>увійти через
                Telegram
                <div>
                <?php
                echo do_shortCode( '[wptelegram-login button_style="large" show_user_photo="0" show_if_user_is="0"]' );
                ?>
                </div>
            </a>
            <?php endif;?>
            <?php if (is_user_logged_in()):?>
                <a href="<?php echo wp_logout_url(home_url()); ?>" class="logout enter-btn" id="logout">Вийти</a>
            <?php endif?>
        </form>
        <div class="hidden-buttons">
            <!-- Hidden buttons -->
            <div class="telegram">

            </div>
            <div class="gmail">
                <a href="<?php echo home_url()?>/wp-login.php?loginSocial=google" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="google" data-popupwidth="600" data-popupheight="600">
                    Click here to login or register
                </a>
            </div>
            <div class="fb">
                <a href="https://map.smplfy.eu/wp-login.php?loginSocial=facebook" data-plugin="nsl" data-action="connect" data-redirect="current" data-provider="facebook" data-popupwidth="475" data-popupheight="175">
                    Click here to login or register
                </a>
            </div>
        </div>
    </div>
</div>