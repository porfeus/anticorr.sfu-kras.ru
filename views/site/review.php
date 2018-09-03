<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this \yii\web\View */
/* @var $model \app\models\ReviewForm */
/* @var $form ActiveForm */

?>
<h2>Ваш отзыв о курсе</h2>
<?php $form = ActiveForm::begin(['method' => 'post']) ?>
    <?= $form->field($model, 'comment')->textarea(); ?>
    <?= Html::submitButton('Оставить отзыв', ['class' => 'btn btn-success btn-fill']) ?>
<?php ActiveForm::end(); ?>
