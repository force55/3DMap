<!-- Wordpress loop -->
<!-- Marker popup id must be popup-${index} - index must be assigned via wordpress loop, so the
     next popup id must be popup-1 -->
<!-- /Wordpress loop -->
<?php
$args = array(
    'post_type' => 'points',
    'numberposts' => -1,
    'post_status' => 'publish,pending',
    'lang' => 'uk'
);
$points = get_posts($args);
foreach ($points as $key => $point):

    $coordinates = get_field('kordynaty_point', $point->ID);
    $lang = $coordinates['shyryna'];
    $lat = $coordinates['dolgota'];

    if (!$lang or !$lat) {
        continue;
    }

    if (check_user_role(array('administrator', 'admin','super_user'), $point->post_author)) {
        $user = 'admin';
    } elseif (check_user_role(array('subscriber'), $point->post_author)) {
        $user = 'user';
    }

    if ($point->post_status == 'pending') {
        $user = 'unactive';
    }

    $userCap = $user;

    $credentionalsToEdit = false;

    $cur_user_id = get_current_user_id();
    if (check_user_role(array('administrator', 'admin','super_user'), get_current_user_id())) {
        $credentionalsToEdit = true;
    }

//    wp_die(var_dump($credentionalsToEdit));

    $cats = render_categories($point->ID);
    $tags = get_field('tags_point', $point->ID);
    $tags_string = '';


    foreach ($tags as $tag) {
        if (end($tags) === $tag) {
            $tags_string .= 'tag_' . $tag->slug;
        } else {
            $tags_string .= 'tag_' . $tag->slug . ', ';
        }
    }

    $audio = get_field('audio_file_point', $point->ID);
    $title = get_field('name_point', $point->ID);
    $subTitle = get_field('subtitle_point', $point->ID);
    $check_for_admin = get_field('displyay_not_logged_users', $point->ID);

    if ($check_for_admin and check_user_role(array('administrator', 'admin')) == false) {
        continue;
    }

    $description = get_field('additional_point', $point->ID);
    $image_description = get_field('additional_image_point', $point->ID);

    $description_security = get_field('additional_security', $point->ID);
    $image_security = get_field('image_security', $point->ID);
    $file_security = get_field('file_security', $point->ID);

    $scientist_articles = get_field('scientist_articles', $point->ID);

    $photo_video_point = get_field('photo_video_point', $point->ID);
    $images = array();
    $videos = array();

    if (isset($photo_video_point) and !empty($photo_video_point)) {
        foreach ($photo_video_point as $item) {
            $check = $item['photo_or_video'];
            if ($check) {
                $images[] = $item['photo'];
            } else {
                $videos[] = $item['video'];
            }
        }
    }

    $do_temi = get_field('galereya_photo_temi', $point->ID);

    $link_panorama = get_field('panorama_point', $point->ID);
    $view_3d = get_field('3d_image', $point->ID);
    $file_obj = $view_3d['fajl_obj'];
    $file_mtl = $view_3d['fajl_mtl'];
    $file_kml = $coordinates['fajl_kml'];

    //english version
    $eng_post = pll_get_post($point->ID, 'en');
    $title_en = get_field('name_point', $eng_post);
    $subTitle_en = get_field('subtitle_point', $eng_post);
    $description_en = get_field('additional_point', $eng_post);
    $description_security_en = get_field('additional_security', $eng_post);
    $file_security_en = get_field('file_security', $eng_post);
    $scientist_articles_en = get_field('scientist_articles', $eng_post);
    ?>
    <div class="marker-popup ua" id="popup-<?php echo $key; ?>"
         data-wp-id="marker-wordpress-id-<?php echo $point->ID; ?>"
         data-lng="<?php echo $lat; ?>"
         data-lat="<?php echo $lang; ?>"
         data-tag-status="<?php echo $userCap; ?>"
         data-categories="<?php echo $cats; ?>"
         data-tags="<?php echo $tags_string; ?>"
         data-kml="<?php echo $file_kml; ?>"
    >
        <div class="marker-popup__title">
            <div class="service-btn-container">
                <?php if ($audio): ?>
                    <a href="#" class="listen-btn">
                        <audio controls>
                            <source src="<?php echo $audio; ?>" type="audio/mpeg">
                        </audio>
                    </a>
                <?php endif; ?>
                <a href="#" class="ua-btn">UA</a>
                <a href="#" class="en-btn">EN</a>
            </div>
            <!-- Title and subtitle from wordpress -->
            <div class="ua">
                <h2><?php echo $title; ?></h2>
                <h3><?php echo $subTitle; ?></h3>
            </div>
            <div class="en">
                <h2><?php echo $title_en; ?></h2>
                <h3><?php echo $subTitle_en; ?></h3>
            </div>
            <!-- /Title and subtitle from wordpress -->
            <div>
                <!-- Put url to model previewer page into href from wordpress -->
                <?php if ($file_obj and $file_mtl): ?>
                    <a href="<?php echo get_home_url() ?>/3d-view?3model_obj=<?php echo $file_obj; ?>&3model_mtl=<?php echo $file_mtl; ?>"
                       class="btn-3d"></a>
                <?php endif; ?>
                <!-- Put url to panorama page into href from wordpress -->
                <?php if ($link_panorama): ?>
                    <a href="<?php echo get_home_url() ?>/panorama?link_panorama=<?php echo $link_panorama; ?>"
                       class="btn-panorama"></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="marker-popup__content">
            <?php if ($image_description or $description): ?>
                <div class="marker-popup__description">
                    <?php if ($image_description): ?>
                        <div class="photo">
                            <img src="<?php echo $image_description; ?>" alt="opishya main">
                        </div>
                    <?php endif; ?>
                    <?php if ($description): ?>
                        <div class="text ua">
                            <p> <?php echo $description; ?></p>
                            <a href="#" class="read-more-btn">Читати далі</a>
                        </div>
                    <?php endif; ?>
                    <?php if ($description_en): ?>
                        <div class="text en">
                            <?php echo $description_en; ?>
                            <a href="#" class="read-more-btn">Read more</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if ($image_security or $description_security or $file_security): ?>
                <div class="marker-popup__information">
                    <div class="marker-popup__information__title">
                        <h3 class="ua">Пам’яткоохоронна інформація</h3>
                        <h3 class="en">Monument protection information</h3>
                    </div>
                    <div class="marker-popup__information__description">
                        <?php if ($image_security): ?>
                            <div class="photo">
                                <img src="<?php echo $image_security; ?>" alt="image_security">
                            </div>
                        <?php endif; ?>
                        <?php if ($description_security or $file_security): ?>
                            <div class="text ua">
                                <?php echo $description_security; ?>
                                <a href="<?php echo $file_security['url']; ?>" target="_blank">
                                    <?php echo $file_security['filename']; ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <?php if ($description_security_en or $file_security_en): ?>
                            <div class="text en">
                                <?php echo $description_security_en; ?>
                                <a href="<?php echo $file_security_en['url']; ?>" target="_blank">
                                    <?php echo $file_security_en['filename']; ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="mini-map"></div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($scientist_articles)): ?>
                <div class="marker-popup__publications">
                    <div class="marker-popup__publications__title">
                        <h3 class="ua">Наукові публікації</h3>
                        <h3 class="en">Scientific publications</h3>
                    </div>
                    <div class="marker-popup__publications__list">
                        <!-- Wordpress loop -->
                        <?php foreach ($scientist_articles as $key => $article): ?>
                            <div class="publication">
                                <div class="publication__thumbnail">
                                    <?php
                                    $file_name = $article['file']['filename'];
                                    $file_url = $article['file']['url'];
                                    $image = $article['foto'];
                                    $text = $article['article'];

                                    if ($image): ?>
                                        <img src="<?php echo $image; ?>" alt="pic 2">
                                    <?php endif; ?>
                                </div>
                                <!-- From wordpress -->
                                <div class="publication__description ua">
                                    <?php echo $text; ?>
                                    <?php if ($file_url and $file_name): ?>
                                        <a href="<?php echo $file_url; ?>" target="_blank"><?php echo $file_name; ?></a>
                                    <?php endif; ?>
                                </div>
                                <div class="publication__description en">
                                    <?php echo $scientist_articles_en[$key]['article']; ?>
                                    <?php if ($file_url and $file_name): ?>
                                        <a href="<?php echo $file_url; ?>" target="_blank"><?php echo $file_name; ?></a>
                                    <?php endif; ?>
                                </div>
                                <!-- /From wordpress -->
                            </div>
                        <?php endforeach; ?>
                        <!-- /Wordpress loop -->
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($images) or !empty($videos) or !empty($do_temi)): ?>
                <div class="marker-popup__media">
                    <div>
                        <?php if (!empty($images)): ?>
                            <div class="gallery">
                                <div class="gallery__title">
                                    <div>
                                        <h3 class="ua">Фотогалерея</h3>
                                        <h3 class="en">Gallery</h3>
                                    </div>
                                    <a href="#" class="show-more-btn">
                      <span class="ua">
                        Дивитися
                        більше
                      </span>
                                        <span class="en">
                        Show more
                      </span>
                                    </a>
                                </div>
                                <div class="gallery__photos">
                                    <!-- From wordpress -->
                                    <?php foreach ($images as $image): ?>
                                        <div>
                                            <img src="<?php echo $image['url']; ?>"
                                                 alt="">
                                            <p><?php var_dump($image['description']);?></p>
                                        </div>
                                    <?php endforeach; ?>
                                    <!-- /From wordpress -->
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if (!empty($videos)): ?>
                            <div class="video">
                                <div class="video__title">
                                    <h4 class="ua">Відео</h4>
                                    <h4 class="en">Video</h4>
                                    <a href="#" class="show-more-btn">
                      <span class="ua">
                        Дивитися
                        більше
                      </span>
                                        <span class="en">
                        Show more
                      </span>
                                    </a>
                                </div>
                                <div class="video__list">
                                    <?php foreach ($videos as $video): ?>
                                        <div>
                                            <iframe src="<?php echo $video; ?>"
                                                    allow="accelerometer; encrypted-media; gyroscope; picture-in-picture"
                                                    allowfullscreen></iframe>
                                        </div>
                                    <?php endforeach; ?>
                                    <!-- Wordpress loop -->
                                    <!--<div>
                                      <iframe src="https://www.youtube.com/embed/BHACKCNDMW8" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                    <div>
                                      <iframe src="https://www.youtube.com/embed/BHACKCNDMW8" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                    <div>
                                      <iframe src="https://www.youtube.com/embed/BHACKCNDMW8" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>-->
                                    <!-- /Wordpress loop -->
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($do_temi)): ?>
                            <div class="other-resources">
                                <div class="other-resources__title">
                                    <h4 class="ua">До теми</h4>
                                    <h4 class="en">To the topic</h4>
                                    <!-- Main thumbnail or first thumbnail for this field from wordpress -->
                                    <div class="first-source">
                                        <img src="<?php echo $do_temi[0]['url']; ?>"
                                             alt="<?php echo $do_temi[0]['filename']; ?>">
                                    </div>
                                    <!-- /Main thumbnail or first thumbnail for this field from wordpress -->
                                </div>
                                <div class="other-resources__sources">
                                    <!-- Wordpress loop -->
                                    <?php foreach ($do_temi as $item): ?>
                                        <div>
                                            <img src="<?php echo $item['url']; ?>"
                                                 alt="<?php echo $item['filename']; ?>">
                                        </div>
                                    <?php endforeach; ?>
                                    <!-- /Wordpress loop -->
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="marker-popup__edit-btn">
            <?php if ($credentionalsToEdit == true): ?>
                <a href="#" class="edit-point-btn">
                    <span class="ua">Редагувати</span>
                    <span class="en">Edit</span>
                </a>
                <a href="#" class="save-point-btn">
                    <span class="ua">Зберегти</span>
                    <span class="en">Save</span>
                </a>
            <?php endif; ?>
            <?php if ($point->post_author == get_current_user_id() or $credentionalsToEdit == true): ?>
                <a href="#" class="deactivate-point-btn" id="deactivate-point-btn"
                   data-delete-id="<?php echo $point->ID; ?>">
                    <span class="ua">Деактивувати</span>
                    <span class="en">Deactivate</span>
                </a>
            <?php endif; ?>
        </div>

    </div>
<?php endforeach; ?>