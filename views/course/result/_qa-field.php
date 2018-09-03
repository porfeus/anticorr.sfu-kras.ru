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
Html::addCssClass($options, 'form-group well');
if ($qa->hasErrors($attribute)) {
    Html::addCssClass($options, 'has-error');
    $error = $qa->getFirstError($attribute);
}

$this->registerCss(<<<CSS
.well {
    margin-bottom: 20px !important;
}
.question-title {
    margin-top: 0;
}
.has-error .question-title  {
    color: #a94442;
}
legend {
    font-size: 16px; 
    font-weight: bold;
    margin-bottom: 10px;
    margin-top: 20px;
}
.radio, .checkbox {
    margin-top: 0;
    margin-bottom: 0;
}
.form-group {
    margin-bottom: 0;
}
.radio .help-block, .checkbox .help-block {
    display: none;
}
CSS
    ,[],'qa-error');
?>
<?= Html::beginTag('div', $options); ?>
    <h4 class="question-title"><?= $label ?></h4>
    <?php if ($question->multipleTrueAnswers()): ?>
        <?php //= $form->field($qa, $attribute)->checkboxList($items)->label($label)->hint($error) ?>
        <fieldset>
            <?php foreach ($question->answers as $i => $answer): ?>
                <?php if ($answer->isDelimiter()): ?>
                    <legend style="font-size: 16px; font-weight: bold;"><?= $answer->title ?></legend>
                <?php else: ?>
					<?php if (isset($qa->answers[$question->id])) { ?>
						<?= $form->field($qa, $attribute . '[' . $i . ']')->checkbox(['value' => $answer->id, 'uncheck' => false, 'checked ' => in_array($answer->id, $qa->answers[$question->id])])->label($answer->title, ['for' => false]) ?>
					<?php } ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </fieldset>
    <?php else: ?>
        <?php //= $form->field($qa, $attribute)->radioList($items)->label($label)->hint($error) ?>
        <fieldset>
            <?php foreach ($question->answers as $i => $answer): ?>
                <?php if ($answer->isDelimiter()): ?>
                    <legend style="font-size: 16px; font-weight: bold;"><?= $answer->title ?></legend>
                <?php else: ?>
                    <?= $form->field($qa, $attribute)->radio(['value' => $answer->id, 'uncheck' => false])->label($answer->title, ['for' => false])?>
                <?php endif; ?>
            <?php endforeach; ?>
        </fieldset>
    <?php endif; ?>
    <p class="text-primary"><?= $question->comment_after ?></p>
<?= Html::endTag('div'); ?>