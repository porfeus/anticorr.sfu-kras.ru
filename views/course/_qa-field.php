<?php

use yii\bootstrap\Html;

/* @var $this \yii\web\View */
/* @var $question \app\models\Question */
/* @var $qa \app\models\Qa */
/* @var $form \yii\bootstrap\ActiveForm */


$attribute = "answers[$question->id]";
$items = $question->answersList;
$label = nl2br($question->title);
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
    , [], 'qa-error');

?>

<?= Html::beginTag('div', $options); ?>
<h4 class="question-title"><?= $label ?></h4>
<?php if ($question->multipleTrueAnswers()): ?>
    <?php //= $form->field($qa, $attribute)->checkboxList($items)->label($label)->hint($error) ?>
    <?= Html::activeHiddenInput($qa, $attribute); ?>
    <fieldset class="<?= $current ? 'current' : '' ?>">

        <?php
        $j = 0;
        $len = count($question->answers);
        $g = 1;
        ?>
        <?php foreach ($question->answers as $i => $answer): ?>

            <?php if ($answer->isDelimiter()): ?>
                <?php
                if ($j != 0) {
                    echo '</span>';
                    $g = 1;
                }
                if ($g == 1) {
                    $g = 0;
                    echo '<span class="sub_question">';
                }
                ?>
                <legend class='legend_<?= $i ?>'
                        style="font-size: 16px; font-weight: bold;"><?= $answer->title ?></legend>
            <?php else: ?>
                <?php

                if ($g == 1) {
                    $g = 0;
                    echo '<span class="sub_question">';
                }
                ?>
                <?= $form->field($qa, $attribute . '[]')->checkbox(['class' => 'input_checkbox', 'value' => $answer->id, 'uncheck' => true])->label($answer->title, ['for' => false]) ?>
            <?php endif; ?>

            <?php
            if ($j == $len - 1) {
                echo '</span>';
            }
            $j++;

            ?>

        <?php endforeach; ?>
    </fieldset>
<?php else: ?>
    <?php //= $form->field($qa, $attribute)->radioList($items)->label($label)->hint($error) ?>
    <fieldset class="<?= $current ? 'current' : '' ?>">
        <?= Html::activeHiddenInput($qa, $attribute); ?>
        <?php foreach ($question->answers as $i => $answer): ?>
            <?php if ($answer->isDelimiter()): ?>
                <legend style="font-size: 16px; font-weight: bold;"><?= $answer->title ?></legend>
            <?php else: ?>
                <?= $form->field($qa, $attribute)->radio(['class' => 'input_radio', 'value' => $answer->id, 'uncheck' => false])->label($answer->title, ['for' => false]) ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </fieldset>
<?php endif; ?>

<?php if ($error): ?>
    <p class="help-block"><?= $error ?></p>
<?php endif; ?>
<?= Html::endTag('div'); ?>

