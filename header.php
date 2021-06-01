<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta property="og:image" content="<?php echo get_template_directory_uri()?>/html_templates/build/images/logo_fb.jpg" />
    <meta property="og:type" content="article" />
    <meta property="og:description" content="<?php bloginfo('description');?>" />
    <meta property="description" content="<?php bloginfo('description');?>" />
    <meta property="og:url" content="<?php echo home_url(); ?>" />
    <meta property="og:title" content="<?php echo get_bloginfo( 'name' ); ?>" />
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <title><?php echo get_bloginfo( 'name' ); ?></title>

    <?php wp_head(); ?>
</head>
<?php
$logged = is_user_logged_in() == true ? 'user-is-logins' : '';
?>
<body <?php body_class($logged); ?> data-cur-user-id="<?php echo get_current_user_id(); ?>">
<div class="intro">
    <div class="intro__scene-1">
        <div class="intro-map"></div>
        <div class="text-1"><span class="text-before"></span>Інноваційний культурний продукт</div>
        <div class="kazan">
            <div class="codpa"></div>
            <div class="b-blue"></div>
        </div>
        <div class="text-2">Віртуальні мандрівки</div>
        <div class="text-3">пам'ятками археології</div>
        <div class="text-4">та історії Полтавщини</div>
    </div>
    <div class="intro__scene-2"></div>
</div>
<header class="header">
    <div class="container">
        <nav class="main-nav">
            <ul>
                <li>
                    <a href="#" class="login-btn">
                        <span>Вхід</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo get_home_url(); ?>">
                        <span>На головну</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-target="content-popup--write-to-us">
                        <span>Напишіть нам</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-target="content-popup--subscribe">
                        <span>Підписатись на оновлення</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-target="content-popup--about-project">
                        <span>Про проєкт</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-target="content-popup--privacy-policy">
                        <span>Умови конфіденційності</span>
                    </a>
                </li>
                <li>
                    <a href="#" data-target="content-popup--rules-of-uses">
                        <span>Правила використання</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url('www.codpa.org.ua'); ?>" target="_blank">
                        <span>Наш сайт</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo esc_url('www.facebook.com/poltavaCODPA'); ?>" target="_blank">
                        <span>Facebook</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>