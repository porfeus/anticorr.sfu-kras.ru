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
    <?php if ($parent = $model->parent): ?>
        <h3><?= $parent->title ?></h3>
        <h4><?= $model->title ?></h4>
    <?php else: ?>
        <h3><?= $model->title ?></h3>
    <?php endif; ?>
<?php endif; ?>
<?php if (!empty($model->test_title)): ?>
    <h3 class="text-primary"><?= $model->test_title ?></h3>
<?php endif ?>
<div>
    <?php
    $form = ActiveForm::begin(['method' => 'post',
        'options' => [
            'class' => 'get_form'
        ]
    ]); ?>
        <?php $first = true;  ?>
        <?php foreach ($model->questions as $question): ?>
            <?= $this->render('_qa-field', ['question' => $question, 'qa' => $qa, 'form' => $form,'current'=>$first]); ?>
            <?php $first = false; ?>
        <?php endforeach; ?>
        <?= Html::submitButton('Закончить тестирование', ['class' => 'send_answer btn btn-primary btn-lg btn-block']) ?>

    <?php ActiveForm::end(); ?>
    <div class="pagination">
        <button class="prev ">Назад</button>
        <button class="first_questions_button next">Вперед</button>
    </div>
</div>

<!--<a href="--><?//= Url::to(['qa']) ?><!--" class="btn btn-primary btn-lg btn-block">Закончить тестирование</a>-->
