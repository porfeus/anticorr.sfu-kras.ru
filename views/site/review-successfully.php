<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this \yii\web\View */
/* @var $model \app\models\ReviewForm */
/* @var $form ActiveForm */

?>
<br>
<br>
<div class="alert alert-success">
    <h5>Отзыв успешно отправлен!</h5>
    <?= Html::a('Вернуться к списку курсов', ['/course/index'], ['class' => 'btn btn-primary btn-fill']) ?>
</div>
