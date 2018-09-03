<?php

use yii\bootstrap\Html;

/* @var $question \app\models\Question */
/* @var $qa \app\models\Qa */
/* @var $form \yii\bootstrap\ActiveForm */

$attribute = "answers[$question->id]";
$items = $question->answersList;
$label = $question->title;
$options = [];
$error = '';
Html::addCssClass($options, 'form-group');
if ($qa->hasErrors($attribute)) {
    Html::addCssClass($options, 'has-error');
    $error = $qa->getFirstError($attribute);
}
?>
<?= Html::beginTag('div', $options); ?>
    <?php if ($question->multipleTrueAnswers()): ?>
        <?= $form->field($qa, $attribute)->checkboxList($items, ['disabled'=>true])->label($label)->hint($error) ?>
    <?php else: ?>
        <?= $form->field($qa, $attribute)->radioList($items)->label($label)->hint($error) ?>
    <?php endif; ?>
<?= Html::endTag('div'); ?>