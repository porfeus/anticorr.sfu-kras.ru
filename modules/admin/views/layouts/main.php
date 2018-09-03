<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
//use app\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <script src="https://cdn.ckeditor.com/4.7.3/standard/ckeditor.js"></script>
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
        'brandLabel' => 'Панель администратора',
        'brandUrl' => ['/admin'],
        'options' => [
            'class' => 'navbar-ct-blue navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => [
            [
                'label' => 'Пользователи',
                'url' => ['/admin/user/index'],
                'visible' => Yii::$app->user->can('admin'),
            ],
            [
                'label' => 'Вопросы/ответы',
                'url' => ['/admin/faq/index'],
                'visible' => Yii::$app->user->can('admin'),
            ],
            [
                'label' => 'Курс',
                'url' => ['/admin/default/course'],
                'visible' => Yii::$app->user->can('admin'),
            ],
            [
                'label' => 'Настройки',
                'url' => ['/admin/settings/main'],
                'visible' => Yii::$app->user->can('admin'),
                //'items' => [
                //    [
                //        'label' => 'Основные',
                //        'url' => ['/admin/settings/main'],
                //    ],
                //    [
                //        'label' => 'Контакты',
                //        'url' => ['/admin/settings/contacts'],
                //    ]
                //],
            ],
        ],
    ]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            [
                'label' => 'Вернуться на сайт',
                'url' => ['/site/index'],
                'visible' => Yii::$app->user->can('admin'),
            ],
            Yii::$app->user->isGuest ? (
                ['label' => 'Login', 'url' => ['/site/login'], 'visible' => false,]
            ) : (
                ['label' => 'Выйти (' . Yii::$app->user->identity->username . ')', 'url' => ['/site/logout'], 'visible' => true]
            )
        ]
    ]);

    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'homeLink' => [
                'label' => 'Админ-панель',
                'url' => '/admin/default/index',
            ],
        ]) ?>
        <?php /* Alert::widget() */ ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Yii::$app->name ?> <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
