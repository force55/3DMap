<?php
/*
 * Template Name: Panorama
 */
$link_panorama = $_GET['link_panorama'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panorama</title>
    <script src="<?php echo get_template_directory_uri()?>/html_templates/build/js/vendors/aframe-master.min.js"></script>
    <script src="<?php echo get_template_directory_uri()?>/html_templates/build/js/vendors/aframe-orbit-controls-component.min.js"></script>
</head>
<body>
<a-scene vr-mode-ui="enabled: false">
    <a-sky src="<?php echo $link_panorama;?>"></a-sky>
</a-scene>
</body>
</html>
