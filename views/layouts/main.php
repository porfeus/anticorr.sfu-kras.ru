<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\modules\admin\models\settings\Main as MainSettings;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => MainSettings::value('siteName'),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-ct-blue navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => [
            ['label' => 'Курсы', 'url' => ['/course/index'], 'visible' => !Yii::$app->user->isGuest],
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            //['label' => 'Home', 'url' => ['/site/index']],
            //['label' => 'About', 'url' => ['/site/about']],
            ['label' => 'Контакты', 'url' => ['/site/contacts']],
            ['label' => 'Перейти в панель администратора', 'url' => ['/admin/default/index'], 'visible' => Yii::$app->user->can('admin')],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login'], 'visible' => false]
            ) : (
                ['label' => 'Выйти (' . Yii::$app->user->identity->username . ')', 'url' => ['/site/logout'], 'visible' => true]
            )
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <div class="font-size-bar">
            <span id="small" class="glyphicon glyphicon-font"></span>
            <span id="medium" class="glyphicon glyphicon-font"></span>
            <span id="big" class="glyphicon glyphicon-font"></span>
        </div>
        <?= $content;?>
        
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= MainSettings::value('siteName') ?> Сибирский федеральный университет <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
