<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Faq */
/* @var $form yii\widgets\ActiveForm */
?>


<?php $form = ActiveForm::begin([
	'id' => 'faq-form',
	'action' => '/faq',
	//'options' => ['class' => 'form-horizontal'],
	'fieldConfig' => [
		//'template' => "<div class='field-faq-question required'>{input}</div><div class=\"col-md-12 col-sm-12 col-sm-offset-2 col-md-offset-2\">{error}</div>",
		'template' => "{input}\n<div class='help-block text-center'>{error}</div>",
	],
]); ?>

<?= $form->field($model, "theme_id")
	->dropDownList(\yii\helpers\ArrayHelper::map(\app\models\Theme::find()->all(), 'id', 'title'), ['prompt' => '---']); ?>
<?= $form->field($model, 'question')->textarea(['rows' => 6, 'placeholder' => 'Задать вопрос по теме']) ?>

<div class="form-group text-center">
	<?= Html::submitButton('Задать вопрос', ['class' => 'btn btn-warning btn-lg load-more']) ?>
</div>
<?php ActiveForm::end(); ?>

