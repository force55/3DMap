<?php
function subscribe_email_callback()
{
    check_ajax_referer('myajax-nonce', 'nonce_code');

    $name = $_POST['data']['name'];
    $email = $_POST['data']['email'];
    $html = '';
    ob_start();
    ?>
    <p>Им'я: - <?php echo $name; ?></p>
    <p>Email - <?php echo $email; ?></p>
    <?php
    $html = ob_get_clean();
    ob_end_clean();

    $mail_to = get_option('admin_email');

    add_filter('wp_mail_content_type', function ($content_type) {
        return "text/html";
    });


    $mail = wp_mail($mail_to, 'Пiдписався на оновлення - ' . $name, $html);

    wp_send_json_success(
        array(
            'success' => true,
            'mail_status' => $mail
        )
    );
}

add_action('wp_ajax_nopriv_report_email', 'report_email_callback');
add_action('wp_ajax_report_email', 'report_email_callback');

function report_email_callback()
{
    check_ajax_referer('myajax-nonce', 'nonce_code');

    $name = $_POST['data']['name'];
    $email = $_POST['data']['email'];
    $text = $_POST['data']['text'];
    $html = '';
    ob_start();
    ?>
    <p>Им'я: - <?php echo $name; ?></p>
    <p>Email - <?php echo $email; ?></p>
    <p>Текст:</p>
    <p><?php echo $text; ?></p>
    <?php
    $html = ob_get_clean();
    ob_end_clean();

    $mail_to = get_option('admin_email');

    add_filter('wp_mail_content_type', function ($content_type) {
        return "text/html";
    });

    $mail = wp_mail($mail_to, 'Вiдправив повiдомлення - ' . $name, $html);

    if ($mail) {
        $post_title = 'Запит вiд '.$name.', дата - '. current_time('d-m-Y H:i');
        $args = array(
            'post_type' => 'report',
            'post_title' => $post_title,
            'post_status'   => 'publish',

        );

        $report_id = wp_insert_post(wp_slash($args));
        update_field('ymya_zapit',$name,$report_id);
        update_field('email_zapit',$email,$report_id);
        update_field('tekst_zapit',$text,$report_id);
    }

    wp_send_json_success(
        array(
            'success' => true,
            'mail_status' => $mail,
            'report_id' => $report_id
        )
    );
}

add_action('wp_ajax_nopriv_subscribe_email', 'report_email_callback');
add_action('wp_ajax_subscribe_email', 'report_email_callback');