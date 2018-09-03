<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use app\models\Module;
use app\models\Theme;

/* @var $this yii\web\View */
/* @var $model Module|Theme */
/* @var $qa \app\models\Qa */

?>


<h1>Тестирование</h1>
<?php if ($model instanceof Module): ?>
    <h2><?= $model->title ?></h2>
<?php elseif ($model instanceof Theme): ?>
    <h2><?= $model->module->title ?></h2>
    <h3><?= $model->title ?></h3>
<?php endif; ?>
<?php if (!empty($model->test_title)): ?>
    <h3 class="text-primary"><?= $model->test_title ?></h3>
<?php endif ?>
<div class="">
    <hr>
    <?php $form = ActiveForm::begin(['method' => 'post']); ?>
        <?php foreach ($model->questions as $question): ?>
            <?= $this->render('_qa-field', ['question' => $question, 'qa' => $qa, 'form' => $form]); ?>
            <hr>
        <?php endforeach; ?>
        <?= Html::submitButton('Закончить тестирование', ['class' => 'btn btn-primary btn-lg btn-block']) ?>
    <?php ActiveForm::end(); ?>
</div>

<!--<a href="--><?//= Url::to(['qa']) ?><!--" class="btn btn-primary btn-lg btn-block">Закончить тестирование</a>-->
