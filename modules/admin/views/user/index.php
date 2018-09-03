<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $adminsDataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = ['label' => 'Панель администратора', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
	<?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
		<?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <fieldset>
        <legend>Администраторы</legend>
		<?= GridView::widget([
			'dataProvider' => $adminsDataProvider,
			//'filterModel' => $searchModel,
			'summary' => false,
			'columns' => [
				//'id',
				'fio',
				'doneModules',
				'doneThemes',
				'doneTests',
				//'created_at:date',
				['class' => 'yii\grid\ActionColumn'],
			],
		]); ?>
    </fieldset>
    <fieldset>
        <legend>Студенты</legend>
		<?php Pjax::begin(['scrollTo' => 550]); ?>
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'summary' => false,
			'rowOptions' => function ($model) {
				/** @var $model \app\modules\admin\models\User */
				return [
					'class' => $model->getFailedModuleTests() ? 'danger' : '',
				];
			},
			'columns' => [
				//'id',
				'fio',
				//'failedModuleTests:boolean',
				'doneModules',
				'doneThemes',
				'doneTests',
				[
					'attribute' => 'groups',
					'filter' => $searchModel->filterGroups(),
					'filterInputOptions' => [
						'prompt' => 'Все',
						'class' => 'form-control',
					],
				],
				['class' => 'yii\grid\ActionColumn'],
			],
		]); ?>
		<?php Pjax::end(); ?>
    </fieldset>
</div>