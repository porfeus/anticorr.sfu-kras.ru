<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="qq-title"><?= $model->question ?></div>
<?php if (!empty($model->answer)): ?>
    <div class="qq-dd well">
        <p><?= $model->answer ?></p>
    </div>
<?php endif; ?>
<br/>