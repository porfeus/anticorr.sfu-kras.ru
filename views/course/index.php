<?php

use yii\helpers\Url;
use app\assets\CourseAsset;

/* @var $this yii\web\View */
/* @var $modules \app\models\Module[] */

$this->title = 'Курс';
$this->params['breadcrumbs'][] = $this->title;

CourseAsset::register($this);

$showReviewAlert = true;

foreach ($modules as $module) {
    if ($module->isCurrent()) {
        $showReviewAlert = false;
    }
}

?>
<h1><?= $this->title ?></h1>


<div class="row">
    <div class="col-md-12">
        <?= $this->render('_review-alert', ['show' => $showReviewAlert]); ?>
        <div class="list-group list-group-root well">
            <?php $this->registerCss(<<<CSS
            .list-group-item-gray {
                background-color: #eee;
            }
            .pull-right-fixed-margin {
                margin: -2px -5px 0 0;
            }
CSS
            ); ?>
            <?php foreach ($modules as $module): ?>
                <?php $filesAttachedId = "filesAttached_" . $module->id; ?>
                <div class="list-group-item-wrapper item">
                    <?php
                    $moduleClass = 'list-group-item-gray';
                    if ($module->isCurrent()) {
                        $moduleClass = 'active';
                        if ($module->getResult() !== null && $module->getResult() < 51 && !$module->canRepeatTesting()) {
                            $moduleClass = 'list-group-item-danger';
                        }
                    } elseif($module->isChecked()) {
                        $moduleClass = 'list-group-item-info';
                    } ?>
                    <div class="list-group-item <?= $moduleClass ?>">
                        <div class="pull-right pull-right-fixed-margin">
                            <?php if (($module->isCurrent() || $module->getResult() !== null || $module->canGodPermission()) && count($module->filesAttached) > 0): ?>
                                <button data-target="#<?= $filesAttachedId ?>" data-toggle="modal" type="button" class="btn btn-info btn-fill btn-xs">Файлы к модулю</button>
                            <?php endif; ?>
                            <?php if ($module->hasQuestions() && $module->canGodPermission()): ?>
                                <a href="<?= Url::to(['course/try', 'module_id' => $module->id]); ?>" class="btn btn-success btn-fill btn-xs"><?= $module->button_name ?></a>
                            <?php else: ?>
                                <?php if ($module->canTesting()): ?>
                                    <a href="<?= Url::to(['course/qa']) ?>" class="btn btn-success btn-fill btn-xs">Пройти тест</a>
                                <?php elseif (($pr_task==true || $module->id==27) && $module->canRepeatTesting()): ?>
                                    <a href="<?= Url::to(['course/try', 'module_id' => $module->id]); ?>" class="btn btn-success btn-fill btn-xs">Пройти тест</a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <div data-toggle="collapse" href="#collapse_<?= $module->id ?>" aria-expanded="false" aria-controls="collapseExample">
                            <?php if (!$module->hasAwait()): ?>
                                <!--<span class="glyphicon glyphicon-check" aria-hidden="true"></span>-->
                                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                            <?php else: ?>
                                <span class="glyphicon glyphicon-record" aria-hidden="true"></span>
                            <?php endif; ?>
                            <strong><?= $module->fullTitle ?></strong>
                            <?php if (($result = $module->getResult()) !== null): ?>
                                <?php if ($result < 51): ?>
                                    <?php if ($module->canRepeatTesting()): ?>
                                        <!--<span id="module_tooltip_<?= $module->id ?>" class="label label-warning label-fill" style="float: none;" data-toggle="tooltip" data-placement="top" title="Для того что бы перейти к изучению следующего модуля, необходимо набрать в тесте модуля минимум 51%. Попробуйте пройти тест модуля еще раз!"><?= $result ?>%</span>-->
                                    <?php else: ?>
                                        <!--<span id="module_tooltip_<?= $module->id ?>" class="label label-danger label-fill" style="float: none;" data-toggle="tooltip" data-placement="top" title="Вы исчерпали все попытки пройти тест модуля минимум на 51%. Перейти к обучению следующего модуля невозможно."><?= $result ?>%</span>-->
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="label label-success label-fill" style="float: none;"><?= $result ?>%</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                    </div>

                    <div id="collapse_<?= $module->id ?>" class="list-group collapse<?= $module->isCurrent() ? ' in' : '' ?>">
                        <?php foreach ($module->themes as $theme): ?>
                            <div class="list-group-item-wrapper2">
                                <div class="list-group-item<?= $theme->isChecked() ? ' list-group-item-success' : ''?><?= $module->isCurrent() && $theme->isCurrent() ? ' list-group-item-warning' : ''?>">

                                    <div class="pull-right pull-right-fixed-margin">
                                        <?php if ($theme->canGodPermission()): ?>
                                            <?php if ($theme->hasQuestions()): ?>
                                                <a href="<?= Url::to(['course/try', 'module_id' => $module->id, 'theme_id' => $theme->id]); ?>" class="btn btn-success btn-xs"><?= $theme->button_name ?></a>
                                            <?php endif; ?>
                                            <a href="<?= Url::to(['course/remind', 'theme_id' => $theme->id]); ?>" class="btn btn-default btn-xs">Изучить</a>
                                        <?php else: ?>
                                            <?php if ($module->isCurrent() && $theme->isCurrent()): ?>
                                                <?php if ($theme->hasAwaitingTesting()): ?>
                                                    <a href="<?= Url::to(['course/qa']); ?>" class="btn btn-danger btn-fill btn-xs">Пройти тест</a>
                                                    <a href="<?= Url::to(['course/study', 'theme_id' => $theme->id]); ?>" class="btn btn-default btn-xs">Освежить в памяти</a>
                                                <?php elseif ($theme->hasAwaitingStudy()): ?>
                                                    <a href="<?= Url::to(['course/study']); ?>" class="btn btn-warning btn-fill btn-xs">Изучить</a>
                                                <?php endif; ?>
                                            <?php elseif($theme->getResult() !== null && $theme->hasTry()): ?>
                                                <a href="<?= Url::to(['course/try', 'module_id' => $module->id, 'theme_id' => $theme->id]); ?>" class="btn btn-success btn-xs">Пройти тест</a>
                                                <a href="<?= Url::to(['course/remind', 'theme_id' => $theme->id]); ?>" class="btn btn-default btn-xs">Освежить в памяти</a>
                                                <a href="<?= Url::to(['course/result', 'module_id' => $module->id, 'theme_id' => $theme->id]); ?>" class="btn btn-warning btn-fill btn-xs">Результаты</a>
                                            <?php elseif($theme->isChecked()): ?>
                                                <a href="<?= Url::to(['course/remind', 'theme_id' => $theme->id]); ?>" class="btn btn-default btn-xs">Освежить в памяти</a>
                                                <a href="<?= Url::to(['course/result', 'module_id' => $module->id, 'theme_id' => $theme->id]); ?>" class="btn btn-warning btn-fill btn-xs">Результаты</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div data-toggle="collapse" href="#collapse_<?= $module->id ?>_<?= $theme->id ?>" aria-expanded="false" aria-controls="collapseExample">
                                        <?php if ($theme->isChecked()): ?>
                                            <!--<span class="glyphicon glyphicon-check" aria-hidden="true"></span>-->
                                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                        <?php else: ?>
                                            <span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span>
                                        <?php endif; ?>
                                        <?= $theme->title ?>
                                        <?php if (($result = $theme->getResult()) !== null): ?>
                                            <span class="label label-success label-fill" style="float: none;" data-toggle="tooltip" data-original-title="Верных ответов" title="Верных ответов"><?= $result ?>%</span>
                                        <?php endif; ?>
                                    </div>

                                </div>
                                <div id="collapse_<?= $module->id ?>_<?= $theme->id ?>" class="list-group collapse<?= $module->isCurrent() && $theme->isCurrent() ? ' in' : '' ?>">
                                    <?php foreach ($theme->themes as $t): ?>
                                        <div class="list-group-item<?= $t->isChecked() ? ' list-group-item-success' : ''?><?= $module->isCurrent() && $theme->isCurrent() && $t->isCurrent() ? ' list-group-item-warning' : ''?>">
                                            <?php if ($t->isChecked()): ?>
                                                <!--<span class="glyphicon glyphicon-check" aria-hidden="true"></span>-->
                                                <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                            <?php else: ?>
                                                <span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span>
                                            <?php endif; ?>
                                            <div class="pull-right pull-right-fixed-margin">
                                                <?php if ($t->canGodPermission()): ?>
                                                    <?php if ($t->hasQuestions()): ?>
                                                        <a href="<?= Url::to(['course/try', 'module_id' => $module->id, 'theme_id' => $t->id]); ?>" class="btn btn-success btn-xs"><?= $t->button_name ?></a>
                                                    <?php endif; ?>
                                                    <a href="<?= Url::to(['course/remind', 'theme_id' => $t->id]); ?>" class="btn btn-default btn-xs">Изучить</a>
                                                <?php else: ?>
                                                    <?php if ($module->isCurrent() && $theme->isCurrent() && $t->isCurrent()): ?>
                                                        <?php if ($t->hasAwaitingTesting()): ?>
                                                            <a href="<?= Url::to(['course/qa']); ?>" class="btn btn-danger btn-fill btn-xs">Пройти тест</a>
                                                            <a href="<?= Url::to(['course/study', 'theme_id' => $t->id]); ?>" class="btn btn-default btn-xs">Освежить в памяти</a>
                                                        <?php elseif ($t->hasAwaitingStudy()): ?>
                                                            <a href="<?= Url::to(['course/study']); ?>" class="btn btn-warning btn-fill btn-xs">Изучить</a>
                                                        <?php endif; ?>
                                                    <?php elseif($t->getResult() !== null && $t->hasTry()): ?>
                                                        <a href="<?= Url::to(['course/try', 'module_id' => $module->id, 'theme_id' => $t->id]); ?>" class="btn btn-success btn-xs">Пройти тест</a>
                                                        <a href="<?= Url::to(['course/remind', 'theme_id' => $t->id]); ?>" class="btn btn-default btn-xs">Освежить в памяти</a>
                                                        <a href="<?= Url::to(['course/result', 'module_id' => $module->id, 'theme_id' => $t->id]); ?>" class="btn btn-warning btn-fill btn-xs">Результаты</a>
                                                    <?php elseif($t->isChecked()): ?>
                                                        <a href="<?= Url::to(['course/remind', 'theme_id' => $t->id]); ?>" class="btn btn-default btn-xs">Освежить в памяти</a>
                                                        <a href="<?= Url::to(['course/result', 'module_id' => $module->id, 'theme_id' => $t->id]); ?>" class="btn btn-warning btn-fill btn-xs">Результаты</a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            <?= $t->title ?>
                                            <?php if (($result = $t->getResult()) !== null): ?>
                                                <span class="label label-success label-fill" style="float: none;" data-toggle="tooltip" data-original-title="Верных ответов" title="Верных ответов"><?= $result ?>%</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div id="<?= $filesAttachedId ?>" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title"><?= $module->title ?></h4>
                            </div>
                            <div class="modal-body">
                                <?= \yii\grid\GridView::widget([
                                    'dataProvider' => new \yii\data\ArrayDataProvider([
                                        'models' => $module->filesAttached,
                                    ]),
                                    'summary' => false,
                                    'showHeader' => false,
                                    'columns' => [
                                        [
                                            'attribute' => 'name',
                                            'format' => 'raw',
                                            'value' => function ($model) {
                                                /** @var $model \app\models\ModuleFile */
                                                return \yii\helpers\Html::a($model->name, $model->file->url, ['target' => '_blank']);
                                            }
                                        ]
                                    ]
                                ]) ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            <?php endforeach; ?>
        </div>
    </div>
</div>
