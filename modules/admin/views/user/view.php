<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\User */
/* @var $modules app\models\Module[] */
/* @var $qaBaseModel app\models\Module|app\models\Theme */

$this->title = $model->fio;
$this->params['breadcrumbs'][] = ['label' => 'Панель администратора', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удалить пользователя ' . $model->fio . '?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fio',
            'city',
            'birthdate',
            'job',
            'job_position',
            'groups',
            'created_at:datetime',
            'updated_at:datetime',
            [
                'attribute' => 'review',
                'value' => $this->render('_review-label', ['model' => $model]),
                'format' => 'raw',
            ]
        ],
    ]) ?>

    <?= $this->render('_stats', [
        'modules' => $modules,
        'model' => $qaBaseModel,
    ]) ?>

</div>
