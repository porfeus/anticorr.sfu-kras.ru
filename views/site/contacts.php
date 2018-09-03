<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\modules\admin\models\settings\Main as MainSettings;

$this->title = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <?= MainSettings::value('contactsContent'); ?>
</div>
