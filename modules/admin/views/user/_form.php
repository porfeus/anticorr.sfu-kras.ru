<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'birthdate')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'job')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'job_position')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, "groups")->dropDownList([1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5'], ['prompt' => '---']); ?>

	<?= $form->field($model, 'password')->passwordInput() ?>
	<?= $form->field($model, 'roles')->checkboxList($model->rolesList()) ?>
    <div class="form-group">
		<?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить изменения', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

	<?php ActiveForm::end(); ?>

</div>
