<?php

use yii\helpers\Url;
use app\assets\CourseAsset;

/* @var $this yii\web\View */
/* @var $modules \app\models\Module[] */

$this->title = 'Вопросы/ответы';
$this->params['breadcrumbs'][] = $this->title;

CourseAsset::register($this);

?>
<?php

foreach (Yii::$app->session->getAllFlashes() as $status => $message) {
	if ($status == 'success') {
		echo '<div class="alert alert-success" role="alert">' . $message . '</div>';
	}
}

?>
<div class="main_content ans_questions_page">
	<div class="container-fluid">
		<div class="row qq-items">
			<?= \yii\widgets\ListView::widget([
				'dataProvider' => $dataProvider,
				'layout' => "{summary}\n{items}",
				'itemView' => '_item',
				'options' => ['class'=>'col-md-12 col-sm-12', 'tag' => 'div',],
				'itemOptions' => ['class'=>'qq-item'],
				'summary' => '',
				   'pager' => [
						'options'=>['class'=>'pager'],   // set clas name used in ui list of pagination
						'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
						'nextPageLabel' => 'Next',   // Set the label for the "next" page button
						'firstPageLabel'=>'First',   // Set the label for the "first" page button
						'lastPageLabel'=>'Last',    // Set the label for the "last" page button
						'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
						'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
						'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
						'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
						'maxButtonCount'=>10,    // Set maximum number of page buttons that can be displayed
					],
			]);
			?>
		</div>
	</div>
	<div class="ask-question">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 ">
					<?= $this->render('_form', [
						'model' => $model,
					]);?>
				</div>
			</div>
		</div>
	</div>
</div>