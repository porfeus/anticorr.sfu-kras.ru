<?php

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use app\models\Module;
use app\models\Theme;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model Module|Theme */
/* @var $qa \app\models\Qa */

$giveTryUrlParams = ['view', 'id' => Module::$userId, 'give_try' => 1];
if ($model instanceof Theme) {
    $giveTryUrlParams['theme_id'] = $model->id;
}
if ($model instanceof Module) {
    $giveTryUrlParams['module_id'] = $model->id;
}

?>
<div>
    <div class="pull-right">
        <?php if ($model->getResult() < 100 && !$model->hasTry()): ?>
            <a href="<?= Url::to(['index']) ?>" class="btn btn-info">К списку курсов</a>
        <?php endif; ?>
    </div>
<?php if ($model instanceof Module): ?>
    <h2><?= $model->title ?></h2>
<?php elseif ($model instanceof Theme): ?>
    <h2><?= $model->module->title ?></h2>
    <?php if ($parent = $model->parent): ?>
        <h3><?= $parent->title ?></h3>
        <h4><?= $model->title ?></h4>
    <?php else: ?>
        <h3><?= $model->title ?></h3>
    <?php endif; ?>
<?php endif; ?>
<?php if (!empty($model->test_title)): ?>
    <h4 class="text-primary"><?= $model->test_title ?></h4>
<?php endif ?>
</div>
<div class="">
    <?php $form = ActiveForm::begin(['method' => 'post']); ?>
        <?php foreach ($model->questions as $question): ?>
            <?= $this->render('_qa-field', ['question' => $question, 'qa' => $qa, 'form' => $form]); ?>
        <?php endforeach; ?>
        <?php
        $resultsJson = \yii\helpers\Json::encode($qa->results);
        $this->registerJs(<<<JS
        var results = $resultsJson;
        $("input[type='checkbox'], input[type='radio']").each(function () {
          var el = \$(this);
          el.prop('disabled', true);
          var isTrue = results[el.val()] == 1;
          if (el.prop('checked')) {
            if (isTrue) {
              el.parent().addClass('text-success');
            } else {
              el.parent().addClass('text-danger');
            }
          } else {
            /*if (isTrue) {
              el.parent().addClass('text-warning');
            }*/
          }
        });
JS
    ) ?>
    <?php ActiveForm::end(); ?>
</div>
<div class="alert">
    <div class="text-success">Правильный ответ</div>
    <div class="text-danger">Неправильный ответ</div>
</div>