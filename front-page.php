<?php
get_header();
?>
    <main class="main-content">
        <div class="map-disable-overlay"></div>
        <div class="bg-overlay"></div>
        <div class="sky-overlay"></div>
        <div class="bg-marker-overlay"></div>
        <div class="container">
            <?php get_template_part('template-parts/service-nav'); ?>
            <div class="inform-panel">
                <form action="#" class="search-form">
                    <div class="autocomplete-field">
                        <input type="text" placeholder="..пошук..." name="searchfield">
                    </div>
                    <button type="submit"></button>
                </form>
                <?php get_template_part('template-parts/filters'); ?>
            </div>
            <div class="inform-panel--ways">
                <?php get_template_part('template-parts/ways'); ?>
            </div>
            <div class="user-control-panel">
                <?php get_template_part('template-parts/my-acount'); ?>
            </div>
            <!-- MARKERS POPUPS -->
            <div class="markers-popups">
                <?php //get_template_part('template-parts/markers'); ?>
            </div>
            <!-- /MARKERS POPUPS -->
            <div class="marker-popup--editable ua">
                <?php get_template_part('template-parts/point', 'edit') ?>
            </div>
            <!-- Popup content -->
            <div class="content-popups">
                <div class="content-popup content-popup--rules-of-uses">
                    <a href="#" class="close-btn"></a>
                    <div class="content-popup__content">
                        <?php get_template_part('template-parts/modal', 'rules-of-uses'); ?>
                    </div>
                </div>
                <div class="content-popup content-popup--privacy-policy">
                    <a href="#" class="close-btn"></a>
                    <div class="content-popup__content">
                        <?php get_template_part('template-parts/modal', 'privacy-policy'); ?>
                    </div>
                </div>
                <div class="content-popup content-popup--about-project">
                    <a href="#" class="close-btn"></a>
                    <div class="content-popup__content">
                        <?php get_template_part('template-parts/modal', 'about-project'); ?>
                    </div>
                </div>
                <div class="content-popup content-popup--subscribe">
                    <a href="#" class="close-btn"></a>
                    <div class="content-popup__content">
                        <?php get_template_part('template-parts/modal', 'subscribe') ?>
                    </div>
                </div>
                <div class="content-popup content-popup--write-to-us">
                    <a href="#" class="close-btn"></a>
                    <div class="content-popup__content">
                        <?php get_template_part('template-parts/modal', 'write-to-us'); ?>
                    </div>
                </div>
            </div>
            <!-- /Popup content -->
            <!-- List of ways -->
            <div class="list-of-ways">
                <div class="list-of-ways__flex">
                    <div class="ways-container">
                        <?php //get_template_part('template-parts/list_of_ways'); ?>
                    </div>
                </div>
            </div>
            <!-- /List of ways -->
            <!-- Edit route popup -->
            <div class="edit-route-popup">
                <a href="#" class="close-edit-route-popup"></a>
                <h4>Редагувати маршрут</h4>
                <?php get_template_part('template-parts/route', 'edit'); ?>
            </div>
            <!-- /Edit route popup -->
            <!-- Route details -->
            <div class="route-details">
                <div class="route-details__window">
                    <div class="route-title">
                        <h2>
                            <span class="index-of-route"></span>
                            <span class="title-of-route"></span>
                        </h2>
                    </div>
                    <div class="inner-content">
                        <div class="buttons-panel">
                            <div>
                                <a href="#" class="lang-btn lang-btn--ua">UA</a>
                                <a href="#" class="lang-btn lang-btn--en">EN</a>
                                <a href="#" class="audio-btn" data-audio-src=""></a>
                            </div>
                            <a href="#" class="close"></a>
                        </div>
                        <div class="content">
                            <img src="" alt="route main photo">
                            <p class="route-details-ua"></p>
                            <p class="route-details-en"></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Route details -->
        </div>
        <div class="global-audio">
            <a href="#" class="close"></a>
            <audio src="" controls></audio>
        </div>
        <div class="panorama-container">
            <a href="#" class="close-panorama-btn"></a>
            <iframe src=""></iframe>
        </div>
        <div class="model-previewer-container">
            <a href="#" class="close-model-previewer-btn"></a>
            <iframe src=""></iframe>
        </div>
        <div class="pohovannya-previewer-container">
            <a href="#" class="close-pohovannya-previewer-btn"></a>
            <iframe src=""></iframe>
        </div>
        <div class="slider-container">
            <div class="slider-container__bg"></div>
            <div class="slider-container__carousel">
                <a href="#" class="close-carousel-btn"></a>
                <div class="carousel"></div>
            </div>
        </div>
        <div class="current-way-points">
            <div class="current-way-points__title">
                <h4>Перелік точок маршруту</h4>
                <a href="#" class="exit-from-routes-mode-btn">Вийти з маршруту</a>
            </div>
            <ul>
                <!-- Past from javascript -->
            </ul>
            <div class="edit-and-add-to-favorite">
                <a href="#" id="rm-route-from-favorite">Видалити з улюблених</a>
                <a href="#" id="add-route-to-favorite">Додати в улюблені</a>
                <a href="#" id="edit-route">Редагувати</a>
            </div>
        </div>
        <div class="inform-window">
            <h4></h4>
            <a href="#" class="ok-btn">ok</a>
        </div>
        <?php get_template_part('template-parts/modal', 'login'); ?>
        <div id="map"></div>
    </main>
<?php
get_footer();