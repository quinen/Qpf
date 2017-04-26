<!doctype html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <base href="<?= QPF_WWW_ROOT ?>" >
        <?php echo $this->Html->title($this->title); ?>
        <?php echo $this->Html->css(array(
            "bootstrap.min"
            ,"bootstrap-theme.min"
        )); ?>
        <?php echo $this->Html->script("jquery-1.12.4.min"); ?>
    </head>
    <body>
        <div class="container">
            <?= $this->content ?>
        </div>
        <?php echo $this->Html->script("bootstrap.min"); ?>
    </body>
</html>