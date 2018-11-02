<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php loadCSS('default') ?>
    <?php if(isset($styles)) loadTemplateStyles($styles); ?>
    <title><?= $title ?? 'Title' ?></title>
</head>
<body>

    <?php view('components/header') ?>
    
    <?=$content?>

    <?php view('components/footer') ?>

    <?php if (isset($scripts)) loadTemplateScripts($scripts); ?>

</body>
</html>