<?php

use app\models\User;
use yii\helpers\Html;

/** @var \app\models\User $user */
/** @var boolean $show */

$user = Yii::$app->user->identity;
$show = ($show || $user->review === User::REVIEW_AWAIT) && $user->review != User::REVIEW_SEND;

?>

<?php if ($show): ?>
<div class="alert alert-info">
    <h5>Оставьте пожалуйста отзыв о курсе!</h5>
    <?= Html::a('Оставить отзыв', ['/site/review'], ['class' => 'btn btn-success btn-fill']) ?>
</div>
<?php endif; ?>
