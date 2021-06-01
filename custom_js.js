(function ($) {

    function showInformationWindow(text) {
        $('.inform-window h4').text(text);
        $('.inform-window').fadeIn(300);
    }

    $('#subscribe_form_submit').on('click', function (event) {
        event.preventDefault();
        var $name = $(this).parent().find('#user_name_subscribe').val();
        var $email = $(this).parent().find('#user_email_subscribe').val();
        console.log($name + ' + ' + $email);

        jQuery.ajax({
            type: "POST",
            url: myajax.url,
            data: {
                action: "subscribe_email",
                data: {
                    'name': $name,
                    'email': $email
                },
                nonce_code: myajax.nonce
            },
            success: function (data) {
                console.log('success_subscribe');
                $('.content-popup--subscribe').fadeOut(300);
                showInformationWindow('Ви пiдписалися');
            },
            error: function (errorThrown) {
                console.log('error');
                console.log(errorThrown);
            }

        });
    })

    $('#report_form_submit').on('click', function (event) {
        event.preventDefault();
        var $name = $(this).parent().find('#user_name_report').val();
        var $email = $(this).parent().find('#user_email_report').val();
        var $text = $(this).parent().find('#text_email_report').val();
        console.log($name + ' + ' + $email);

        jQuery.ajax({
            type: "POST",
            url: myajax.url,
            data: {
                action: "report_email",
                data: {
                    'name': $name,
                    'email': $email,
                    'text': $text
                },
                nonce_code: myajax.nonce
            },
            success: function (data) {
                console.log('success_report');
                $('.content-popup--write-to-us').fadeOut(300);
                showInformationWindow('Ваш запит вдало вiдправився');
            },
            error: function (errorThrown) {
                console.log('error');
                console.log(errorThrown);
            }
        });
    })
    var addedPoints = [];

    $('.purpose-route-form').on('change', 'select', function (e) {
        addedPoints[$(this).parent().index()] = e.target.value;
        if (addedPoints.length) $('.add-more-point').css('display', 'block');
    });

    $('.purpose-route-form .selects').on('click', '.remove-point-btn', function (e) {
        e.preventDefault();
        var i = $(this).parent().index();
        addedPoints.splice(i, 1);
        $(this).parent().remove();
    });

    $('#submit_route').on('click', function (event) {
        event.preventDefault();
        if (!$(this).parent().parent().find('input[type="text"]').val() || $(this).parent().parent().find('input[type="text"]').val().length < 3) {
            showInformationWindow('Введіть назву для маршруту. Не менше трьох символів');
            return false;
        }
        if (addedPoints.length <= 1) {
            showInformationWindow('Маршрут може складатись лише з двох або більше точок');
            return false;
        }
        for (var i = 0; i < addedPoints.length - 1; i++) {
            if (addedPoints[i] === addedPoints[i + 1]) {
                showInformationWindow('Маршрут не може складатись з двох однакових точок підряд');
                return false;
            }
        }


        var $selects = $(document).find('.purpose-route-form .selects select');
        var $points = [];

        $.each($selects, function (index, value) {
            var select = $($selects[index]).children("option:selected").val();
            $points.push(select);
        });

        var $propose_to_public = $(this).parent().find('#purpose-to-publication');
        if (($propose_to_public).is(":checked")) {
            $propose_to_public = true;
        } else {
            $propose_to_public = false;
        }
        var $name = $(this).parent().find('#route-name').val();

        var route_description_ua = $(this).parent().find('textarea[name="route-description-ua"]').val();
        var route_description_en = $(this).parent().find('textarea[name="route-description-en"]').val();

        jQuery.ajax({
            type: "POST",
            url: myajax.url,
            data: {
                action: "create_route",
                data: {
                    'name': $name,
                    'points': $points,
                    'propose_to_public': $propose_to_public,
                    'route-description-ua': route_description_ua,
                    'route-description-en': route_description_en,
                },
                nonce_code: myajax.nonce
            },
            success: function (data) {
                console.log('success_create');
                showInformationWindow(data.data.message);
                if (data.data.url != '') {
                    setTimeout(function () {
                        window.location.href = data.data.url;
                    }, 1000);
                }
            },
            error: function (errorThrown) {
                console.log('error');
                console.log(errorThrown);
            }
        });
    })

    $(document).on('click', '.deactivate-point-btn', function (event) {
        var $id_delete = $(this).attr('data-delete-id');
        console.log('click deactivate');
        jQuery.ajax({
            type: "POST",
            url: myajax.url,
            data: {
                action: "deactivate_point",
                data: {
                    'id': $id_delete
                },
                nonce_code: myajax.nonce
            },
            success: function (data) {
                console.log('sucess_deactivate_point');
                showInformationWindow(data.data.message);
                if (data.data.url != '') {
                    setTimeout(function () {
                        window.location.href = data.data.url;
                    }, 1000);
                }
            },
            error: function (errorThrown) {
                console.log('error');
                console.log(errorThrown);
            }
        });
    });

    $('#create-new-point').on('click', function (event) {
        event.preventDefault();
        var $id_point = $(document).find(this).parent().parent().find('input[name="id-of-marker"]').val();
        console.log($id_point);


        var $form_html = $(document).find('.marker-popup--editable form')[0];
        var $form = new FormData($form_html);

        var $publications = $(document).find('.marker-popup--editable form .publication');
        var $photos_gallery = $(document).find('.marker-popup--editable form .upload-gallery-photo-item');
        var $videos_gallery = $(document).find('.marker-popup--editable form .link-to-video-item');
        var $other_resources = $(document).find('.marker-popup--editable form .other-resources-upload-item');

        // console.log($publications.length);
        if ($id_point) {
            $form.append('action', 'edit_point');
        } else {
            $form.append('action', 'create_point');
        }

        $form.append('nonce_code', myajax.nonce);
        $form.append('publications', $publications.length);
        $form.append('photos_gallery', $photos_gallery.length);
        $form.append('videos_gallery', $videos_gallery.length);
        $form.append('other_resources', $other_resources.length);

        showInformationWindow('Завантаження файлів для нової точки. Будь ласка зачекайте…');
        jQuery.ajax({
            type: "POST",
            url: myajax.url,
            processData: false,
            contentType: false,
            data: $form,
            success: function (data) {
                showInformationWindow(data.data.message);
                if (data.data.url != '') {
                    setTimeout(function () {
                        window.location.href = data.data.url;
                    }, 1000);
                }
            },
            error: function (errorThrown) {
                console.log('error');
                console.log(errorThrown);
            }
        });

    });

    $('.enter-btn--email').on('click', function () {
        $('.login-form-container .hidden-buttons .gmail a').trigger('click');
    });

    $('.enter-btn--facebook').on('click', function () {
        $('.login-form-container .hidden-buttons .fb a').trigger('click');
    });

    $('#add-route-to-favorite').on('click', function () {
        var $id = $(this).attr('data-way-id');
        jQuery.ajax({
            type: "POST",
            url: myajax.url,
            data: {
                action: 'add_favorite_route',
                data: {
                    'id_route': $id
                },
                nonce_code: myajax.nonce
            },
            nonce_code: myajax.nonce,
            success: function (data) {
                showInformationWindow(data.data.message);
                if(data.data.url != '') {
                    setTimeout(function () {
                        window.location.href = data.data.url;
                    }, 1000);
                }
            },
            error: function (errorThrown) {
                console.log('error');
                console.log(errorThrown);
            }
        });
    });

    $('#rm-route-from-favorite').on('click', function () {
        var $id = $(this).attr('data-way-id');
        jQuery.ajax({
            type: "POST",
            url: myajax.url,
            data: {
                action: 'delete_favorite_route',
                data: {
                    'id_route': $id
                },
                nonce_code: myajax.nonce
            },
            nonce_code: myajax.nonce,
            success: function (data) {
                console.log('success_delete_route_to_favorite');
                showInformationWindow(data.data.message);
                setTimeout(function () {
                    window.location.href = data.data.url;
                }, 1000);
            },
            error: function (errorThrown) {
                console.log('error');
                console.log(errorThrown);
            }
        });
    });
    // Submit edit route
    $(document).on('click', '#edit-route-submit', function (e) {
        e.preventDefault();
        console.log('Edit route submit');
        var $form_html = $(document).find('.edit-route-popup form')[0];
        var $form = new FormData($form_html);

        var points_route = $(document).find('.edit-route-popup form .routes-list select');
        var points_route_send = [];
        $.each(points_route,function (index,value){
            points_route_send.push($(points_route[index]).val());
        })

        $form.append('points_route',points_route_send);

        $form.append('nonce_code', myajax.nonce);
        $form.append('action', 'edit_route');

        var length = $(document).find('.edit-route-popup form .routes-list select').length;
        $form.append('length_points', length);

        if (length < 2) {
            showInformationWindow('Маршрут може складатись лише з двох або більше точок');
            return false;
        }

        if (!$(document).find('.edit-route-popup form #route-title-field').val() || $(document).find('.edit-route-popup form #route-title-field').val().length < 3) {
            showInformationWindow('Введіть назву для маршруту. Не менше трьох символів');
            return false;
        }

        jQuery.ajax({
            type: "POST",
            url: myajax.url,
            processData: false,
            contentType: false,
            data: $form,
            success: function (data) {
                console.log('success_create_point');
                showInformationWindow(data.data.message);
                if (data.data.url != '') {
                    setTimeout(function () {
                       window.location.href = data.data.url;
                    }, 1000);
                }
            },
            error: function (errorThrown) {
                console.log('error');
                console.log(errorThrown);
            }
        });
    });

    // Share button facebook
    $(document).on('click', '.share--fb', function (e) {
        e.preventDefault();
        var wpId = $(this).attr('data-share-id');

        var title = $('.marker-popup').find('.title.ua h2').text();
        var description = $('.marker-popup').find('.marker-popup__description span').text();

        console.log(wpId);
        var url_share = 'https://www.facebook.com/sharer/sharer.php?' +
            'u=' + window.location.origin + '?visitid=' + wpId +
            '&title=' + title +
            '&picture=' + picturefb +
            '&quote=' + description;
        window.open(url_share, '_blank');
    });

    // Share button rest socials
    $(document).on('click', '.share--rest', function (e) {
        e.preventDefault();
        var wpId = $(this).attr('data-share-id');
    });

    $(document).on('click', '#remove-route', function (e) {
        e.preventDefault();
        var removeRouteId = $(document).find('#route-id-field').val()
        jQuery.ajax({
            type: "POST",
            url: myajax.url,
            data: {
                action: 'delete_route',
                data: {
                    'id': removeRouteId
                },
                nonce_code: myajax.nonce
            },
            nonce_code: myajax.nonce,
            success: function (data) {
                showInformationWindow(data.data.message);
                setTimeout(function () {
                    window.location.href = data.data.url;
                }, 1000);
            },
            error: function (errorThrown) {
                console.log('error');
                console.log(errorThrown);
            }
        });
        console.log(removeRouteId);
    })

})(jQuery);
