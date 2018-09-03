<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Theme;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Faq */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

	<?php $form = ActiveForm::begin(); ?>

    <div class="form-group field-faq-subject">
        <label class="control-label" for="faq-subject">Пользователь</label>
        <input id="faq-subject" class="form-control" disabled="disabled" value="<?= $model->author->fio ?>" type="text">
    </div>

	<? $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, "theme_id")
		->dropDownList(ArrayHelper::map(Theme::find()->all(), 'id', 'title'), ['prompt' => '---']); ?>
	<?= $form->field($model, 'question')->textArea(['maxlength' => true]) ?>
	<?= $form->field($model, 'answer')->textArea(['maxlength' => true]) ?>
    <div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить изменения', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>
