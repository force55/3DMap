<?php
$page = get_field('stranycza_privacy_policy', 'options');
$top_content = get_field('subtitle_page', $page);
$bot_content = get_the_content(null, false, $page);
?>

<div class="top-part">
    <div class="icon"></div>
    <h2>Умови конфіденційності</h2>
    <div class="short-description">
        <?php echo $top_content; ?>
    </div>
</div>
<div class="bottom-part">
    <?php echo $bot_content; ?>
</div>