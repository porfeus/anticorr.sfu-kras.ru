<?php

use yii\helpers\Url;
use app\assets\CourseAsset;
use app\models\Module;
use app\models\Qa;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $modules Module[] */
/* @var $model app\models\Module|app\models\Theme */

CourseAsset::register($this);

?>




<div class="row">
    <div class="col-md-4">
        <h2>Статистика</h2>
        <div class="list-group list-group-root well" style="overflow-y: scroll; height: 50vh;">
            <?php $this->registerCss(<<<CSS
            .list-group-item-gray {
                background-color: #eee;
            }
CSS
            ); ?>
            <?php foreach ($modules as $module): ?>
                <div class="list-group-item-wrapper item">
                    <a data-pjax-link href="<?= Url::to(['view', 'id' => Module::$userId, 'module_id' => $module->id]) ?>" class="list-group-item <?= $module->isCurrent() ? 'active' : ($module->isChecked() ? 'list-group-item-info' : 'list-group-item-gray')?>">
                        <?php if (!$module->hasAwait()): ?>
                            <!--<span class="glyphicon glyphicon-check" aria-hidden="true"></span>-->
                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                        <?php else: ?>
                            <span class="glyphicon glyphicon-record" aria-hidden="true"></span>
                        <?php endif; ?>
                        <div class="pull-right">
                            <small><?= $module->getDoneAt() ?></small>
                        </div>
                        <strong><?= $module->title ?></strong>
                        <?php if (($result = $module->getResult()) !== null): ?>
                            <span class="badge" style="float: none;"><?= $result ?>%</span>
                        <?php endif; ?>

                    </a>
                    <div class="list-group">
                        <?php foreach ($module->themes as $theme): ?>
                            <a data-pjax-link href="<?= Url::to(['view', 'id' => Module::$userId, 'theme_id' => $theme->id]) ?>" class="list-group-item<?= $theme->isChecked() ? ' list-group-item-success' : ''?><?= $module->isCurrent() && $theme->isCurrent() ? ' list-group-item-warning' : ''?>">
                                <?php if ($theme->isChecked()): ?>
                                    <!--<span class="glyphicon glyphicon-check" aria-hidden="true"></span>-->
                                    <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                <?php else: ?>
                                    <span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span>
                                <?php endif; ?>
                                <div class="pull-right">
                                    <small><?= $theme->getDoneAt() ?></small>
                                </div>
                                <?= $theme->title ?>


                                <?php if (($result = $theme->getResult()) !== null): ?>
                                    <span class="badge" style="float: none;" data-toggle="tooltip" data-original-title="Верных ответов" title="Верных ответов"><?= $result ?>%</span>
                                <?php endif; ?>
                            </a>
                            <div class="list-group list-group-item-wrapper3">
                                <?php foreach ($theme->themes as $t): ?>
                                    <a data-pjax-link href="<?= Url::to(['view', 'id' => Module::$userId, 'theme_id' => $t->id]) ?>" class="list-group-item<?= $t->isChecked() ? ' list-group-item-success' : ''?><?= $module->isCurrent() && $theme->isCurrent() && $t->isCurrent() ? ' list-group-item-warning' : ''?>">
                                        <?php if ($t->isChecked()): ?>
                                            <!--<span class="glyphicon glyphicon-check" aria-hidden="true"></span>-->
                                            <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
                                        <?php else: ?>
                                            <span class="glyphicon glyphicon-play-circle" aria-hidden="true"></span>
                                        <?php endif; ?>
                                        <div class="pull-right">
                                            <small><?= $t->getDoneAt() ?></small>
                                        </div>
                                        <?= $t->title ?>


                                        <?php if (($result = $t->getResult()) !== null): ?>
                                            <span class="badge" style="float: none;" data-toggle="tooltip" data-original-title="Верных ответов" title="Верных ответов"><?= $result ?>%</span>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php $this->registerJs(<<<JS
    $(document).on('click', '[data-pjax-link]', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        console.log(url)
        $.pjax({url: url, container: '#pjax-container', scrollTo: false, timeout: 25000});
    });
    $(document).on('pjax:send', function() {
      $('#loading').show()
    })
    $(document).on('pjax:complete', function() {
      $('#loading').hide()
    })
JS
    ); ?>
    <?php $this->registerCss(<<<CSS
    #loading {
        background: url(/loading.gif);
        background-repeat: no-repeat;
        background-position: center 100px;
        position: absolute;
        top: 0;
        left:0;
        right :0;
        bottom: 0;
        z-index: 5;
        background-color: rgba(255, 255, 255, 0.8);
        display: none;
    }
CSS
    ); ?>
    <div class="col-md-8" style="position: relative;">
        <div id="loading">

        </div>
        <?php Pjax::begin(['timeout' => 25000, 'id' => 'pjax-container', 'scrollTo' => false]); ?>
        <?php if ($model): ?>
            <?= $this->render('_qa', ['qa' => Qa::loadPrevResults($model), 'model' => $model]); ?>
        <?php endif; ?>
        <?php Pjax::end(); ?>
    </div>
</div>
