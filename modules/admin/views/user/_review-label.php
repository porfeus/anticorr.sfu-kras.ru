<?php

use yii\helpers\Html;
use app\modules\admin\models\User;

/* @var $this yii\web\View */
/* @var $model User */

?>

<?php if ($model->review == User::REVIEW_SEND): ?>
    <div>Отправил отзыв</div>
    <?= Html::a('Запросить отзыв еще раз', ['re-review', 'id' => $model->id], ['class' => 'btn btn-danger btn-xs']) ?>
<?php elseif ($model->review == User::REVIEW_AWAIT): ?>
    <div>Еще не оставил отзыв</div>
<?php else: ?>
    <div>Еще не оставил отзыв</div>
    <?= Html::a('Запросить отзыв принудительно', ['re-review', 'id' => $model->id], ['class' => 'btn btn-danger btn-xs']) ?>
<?php endif; ?>
