<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Menu;
use app\models\ThemeFile;

/* @var $this yii\web\View */
/* @var $theme \app\models\Theme */
/* @var $readOnly null|boolean */

$readOnly = isset($readOnly) ? $readOnly : false;

//$this->title = $theme->module->title . '/' . $theme->title;
//$this->params['breadcrumbs'][] = ['label' => 'Курс', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $theme->module->title;
//$this->params['breadcrumbs'][] = $theme->title;

?>
<?php

foreach (Yii::$app->session->getAllFlashes() as $status => $message) {
	if ($status == 'success') {
		echo '<div class="alert alert-success" role="alert">' . $message . '</div>';
	}
}

?>

<h1><?= $theme->module->title ?></h1>
<?php if ($parent = $theme->parent): ?>
	<h3><?= $parent->title ?></h3>
	<h4><?= $theme->title ?></h4>
<?php else: ?>
	<h3><?= $theme->title ?></h3>
<?php endif; ?>
<div>
	<?= $theme->getVideo() ?>
</div>
<div class="well">
	<?= $theme->description ?>
</div>
<div>
	<?php foreach (ThemeFile::typeList() as $type => $label): ?>
		<?php if ($files = $theme->filesByType($type)): ?>
            <h3><?= $label ?></h3>
			<?= Menu::widget([
				'items' => array_map(function ($file) {
					/** @var \app\models\ThemeFile $file */
					return [
						'label' => $file->name,
						'url' => $file->file->url,
					];
				}, $theme->filesByType($type)),
			]); ?>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
<?php if ($readOnly !== true): ?>
	<?php $form = ActiveForm::begin(['method' => 'post']); ?>
	<?php if (count($theme->questions) > 0): ?>
		<?= Html::submitButton('Пройти тестирование', ['class' => 'btn btn-primary btn-lg btn-block']) ?>
	<?php else: ?>
		<?= Html::submitButton('Перейти к следующей теме', ['class' => 'btn btn-primary btn-lg btn-block']) ?>
	<?php endif; ?>
	<?php ActiveForm::end(); ?>
<?php elseif ($theme->getResult() < 100 && $theme->hasTry()): ?>
    <a href="<?= Url::to(['course/try', 'module_id' => $theme->module_id, 'theme_id' => $theme->id]); ?>" class="btn btn-success btn-block btn-lg">Пройти тест еще раз</a>
<?php endif; ?>

<?= \yii\widgets\ListView::widget([
	'dataProvider' => new \yii\data\ActiveDataProvider([
		'query' => \app\models\Faq::find()->where(['theme_id'=>$theme->id]),
	]),
	'layout' => "{summary}\n{items}",
	'itemView' => '_item',
	'options' => ['class'=>'col-md-12 col-sm-12', 'tag' => 'div',],
	'itemOptions' => ['class'=>'qq-item'],
	'summary' => '',
	'pager' => [
		'options'=>['class'=>'pager'],   // set clas name used in ui list of pagination
		'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
		'nextPageLabel' => 'Next',   // Set the label for the "next" page button
		'firstPageLabel'=>'First',   // Set the label for the "first" page button
		'lastPageLabel'=>'Last',    // Set the label for the "last" page button
		'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
		'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
		'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
		'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
		'maxButtonCount'=>10,    // Set maximum number of page buttons that can be displayed
	],
]);
?>

<?= $this->render('_faq_form', [
	'model' => new \app\models\Faq(),
	'theme_id' => $theme->id
]); ?>
