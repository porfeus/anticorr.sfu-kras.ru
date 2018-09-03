<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\FaqSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Вопросы/Ответы';
$this->params['breadcrumbs'][] = ['label' => 'Панель администратора', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <fieldset>
		<?php Pjax::begin(); ?>
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'summary' => false,
			'rowOptions' => function ($model) {
				return [
					'class' => !$model->answer ? 'danger' : 'success',
				];
			},
			'columns' => [
				[
					'attribute' => 'id',
					'options' => ['width' => 25],
				],
				[
					'attribute' => 'user_id',
					'value' => function ($model, $key, $index, $column) {
						if (!empty($model->author->fio)) {
							return $model->author->fio;
						}
					},
				],
				[
					'attribute' => 'theme_id',
					'filter' => $searchModel->filterTheme(),
					'filterInputOptions' => [
						'prompt' => 'Все',
						'class' => 'form-control',
					],
					'value' => function ($model, $key, $index, $column) {
						if (!empty($model->theme->title)) {
							return $model->theme->title;
						}
					},
				],
				'question',
				['class' => 'yii\grid\ActionColumn'],
			],
		]); ?>
		<?php Pjax::end(); ?>
    </fieldset>
</div>