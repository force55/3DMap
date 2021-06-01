<?php
/*
 * Template Name: 3D page
 */
$model_obj = $_GET['3model_obj'];
$model_mtl = $_GET['3model_mtl'];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Завантаження моделі</title>
    <script src="<?php echo get_template_directory_uri()?>/html_templates/build/js/vendors/aframe-master.min.js"></script>
    <script src="<?php echo get_template_directory_uri()?>/html_templates/build/js/vendors/aframe-orbit-controls-component.min.js"></script>
</head>
<body>
<a-scene vr-mode-ui="enabled: false">
    <a-assets>
        <a-asset-item id="tree-obj" src="<?php echo $model_obj; ?>"></a-asset-item>
        <a-asset-item id="tree-mtl" src="<?php echo $model_mtl; ?>"></a-asset-item>
    </a-assets>

    <a-entity
            id="camera"
            camera
            position="0 0 5"
            orbit-controls="
                autoRotate: false;
                target: #model;
                enableDamping: true;
                dampingFactor: 0.25;
                rotateSpeed:0.14;
                minDistance:1;
                maxDistance:15;"
            mouse-cursor="">
    </a-entity>

    <a-entity id="model" position="0 0 0" scale="0.005 0.005 0.005" rotation="-90 0 0">
        <a-entity obj-model="obj: #tree-obj; mtl: #tree-mtl"></a-entity>
    </a-entity>

</a-scene>
</body>
</html>
