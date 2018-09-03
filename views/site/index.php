<?php

use app\modules\admin\models\settings\Main as MainSettings;
/* @var $this yii\web\View */

$this->title = MainSettings::value('siteName');

$isGuest = Yii::$app->user->isGuest;

?>
<div class="site-index">

    <div class="row">
        <div class="<?= $isGuest ? 'col-md-8' : 'col-md-12' ?>">
            <?= MainSettings::value('homeContent') ?>
        </div>
        		 <?php if(!Yii::$app->user->isGuest) 
					echo '<p style="text-align: center;"><a class="btn btn-primary btn-lg" href="https://drive.google.com/open?id=1OtlHjWim1slb9--znrEv3LCXbvQvCjWQ" role="button">Программа курса</a></p>
					
					<p style="text-align: center;"><a class="btn btn-primary btn-lg" href="/course/index" role="button">Перейти к прохождению курсов</a></p>';?>
        <?php if ($isGuest): ?>
        <div class="col-md-4">
            <?= $this->render('_login', ['model' => new \app\models\LoginForm()]) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
