<?php
//use Yii;
use yii\helpers\Url;
use app\assets\CourseAsset;
use app\models\Module;
use app\models\Qa;
use yii\widgets\Pjax;
use app\models\Answer;
use app\models\UserAnswer;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $modules Module[] */
/* @var $model app\models\Module|app\models\Theme */

CourseAsset::register($this);

?>

<!-- <div class="row">
    <div class="col-md-12">
		<?php if ($model): ?>
			<?= $this->render('result/_qa', ['qa' => Qa::loadPrevResults($model), 'model' => $model]); ?>
		<?php endif; ?>
		<?php if (!empty($model->test_comment_after)): ?>
			<div class="well">
				<?= $model->test_comment_after; ?>
			</div>
		<?php endif; ?>
    </div>

</div> -->
<div class="row">
	<div class="col-md-12">
		<h1><?=$model->title?></h1>
		<h3></h3>
		<?php if (isset($questions)) { ?>
        <?php foreach ($questions as $question):?>
            <h3><?= $question->title; ?></h3>
            <?php

                $isMultiple=$question->multipleTrueAnswers();
                $trueAnswers=$question->getTrueAnswers();
                $userAnswers=UserAnswer::findAll([
                    'user_id'=>Yii::$app->user->id,
                    'module_id'=>$question->module_id,
                    'theme_id'=>$question->theme_id,
                    'question_id'=>$question->id,
                ]);
                $userAnsws=array();
            foreach ($userAnswers as $userAnswer) {
                    array_push($userAnsws,$userAnswer->answer_id);
                }

                $isTrue=UserAnswer::isTrue($userAnsws,$trueAnswers);

                if($isTrue==1) {
                    $class = "alert alert-success";
                    $phrase="Вы правильно ответили на вопрос";
                }
                elseif($isTrue==0){
                    $class = "alert alert-danger";
                    $phrase="Вы не правильно ответили на вопрос";
                }
                elseif($isTrue==2){
                    $class = "alert alert-info";
                    $phrase="Вы ответили на вопрос правильно частично";
                }
                if(!$isMultiple):
                    $id=$trueAnswers[0];
                    $ans=Answer::findOne($id);
					if (isset($userAnsws[0])) {
						$id=$userAnsws[0];
						$usAnsw=Answer::findOne($id);
						//echo $ans->title;
						?>
						<div class="<?= $class ?>">
							<p>Правильный ответ: <?= $ans->title ?></p>
							<p>Вы ответили <?=$usAnsw->title ?></p>
							<p><?= $phrase ?></p>
							<div class="addition">
								<p><?=$question->comment_after?></p>
								<?php if($question->file != ""): ?><a href="<?= $question->file; ?>">Скачать файл к тесту</a><?php endif; ?>
							</div>
						</div>
					<?php } ?>
                <?php else: ?>
                    <?php $count=count($question->answers); ?>
                    <?php $metka=0; ?>
                    <?php $ansArray=array();?>
                    <?php $blockAllAnswers=array();?>
                    <?php $blockUserAnswers=array();?>
                    <?php foreach ($question->answers as $answer):?>
                        <?php
                            $metka++;
                            if(($answer->is_true==-1)&&(count($ansArray)==0)){?>
                                <p><?= $answer->title ?></p>

                            <?php
                                continue;
                            }
                            if($answer->is_true==-1){
                                echo UserAnswer::generateMultipleBlock($userAnsws,$ansArray,$question,$blockUserAnswers);
                                ?><p><?= $answer->title ?></p><?php
                                $ansArray=array();
                                $blockAllAnswers=array();
                                $blockUserAnswers=array();
                                continue;
                            }
                            array_push($blockAllAnswers,$answer->id);
                            $blockUserAnswers=array_intersect($userAnsws,$blockAllAnswers);
                            if($answer->is_true==1){
                                array_push($ansArray, $answer->id);
                            }

                            if($metka==$count) {

                                echo UserAnswer::generateMultipleBlock($userAnsws,$ansArray,$question,$blockUserAnswers);
                                $string= '<div class="addition-delimiter">';
                                $string.=$question->comment_after;
                                $string.=($question->file!="")? '<a href="'.$question->file.'">Скачать файл к тесту</a>': '';
                                $string.= '</div><br>';
                                echo $string;
                                continue;
                            }

                        ?>
                    <?php endforeach;?>
                <?php endif; ?>
        <?php  endforeach; ?>



		<?php /*foreach ($questions as $question):*/?><!--
			<?php
/*				$isMultiple=0;
				$answers=Answer::findAll([
					'question_id'=>$question->id,
					'is_true'=>[-1,1],
				]);
				$correctAnswers=array();
				$usAnswers=array();
				$isCorrect=0;
				$userAnswers=UserAnswer::findAll([
					'user_id'=>Yii::$app->user->id,
					'module_id'=>$question->module_id,
					'theme_id'=>$question->theme_id,
					'question_id'=>$question->id,
				]);
				if(count($answers)>1){
					$isMultiple=1;
				}
				if($isMultiple){
					foreach ($answers as $answ) {
					# code...
					array_push($correctAnswers, $answ->id);
					}
					foreach ($userAnswers as $usansw) {
					# code...
					array_push($usAnswers, $usansw->answer_id);
					}
					$result=array_intersect($usAnswers, $correctAnswers);

					if (count($result)==count($usAnswers)){
						$isCorrect=1;
					}
					elseif (count($result)==0) {
						# code...
						$isCorrect=0;
					}
					else{
						$isCorrect=2;
					}
				}
				else{
					if($userAnswers[0]->answer_id==$answers[0]->id){
						$isCorrect=1;
					}
				}

			*/?>
			<p><?/*=$question->title*/?></p>
			<?php /*if($isCorrect==0):*/?>
				<div class="alert alert-danger">
					<?php /*if($isMultiple): */?>
						<p>Правильные ответы:<br>
						<?php /*foreach ($answers as $ans):*/?>
							<?/*=$ans->title*/?><br>
						<?php /*endforeach;*/?>

						</p>
					<?php /*else: */?>
						<p>
							Правильный ответ: <?/*=$answers[0]->title*/?>
						</p>
					<?php /*endif;*/?>
					<p></p>
					<p>Вы не правильно ответили на вопрос</p>
					<p><?/*=$question->comment_after*/?></p>
				</div>
			<?php /*elseif($isCorrect==1):*/?>
				<div class="alert alert-success">
					<?php /*if($isMultiple): */?>
						<p>Правильные ответы:<br>
						<?php /*foreach ($answers as $ans):*/?>
							<?/*=$ans->title*/?><br>
						<?php /*endforeach;*/?>

						</p>
					<?php /*else: */?>
						<p>
							Правильный ответ: <?/*=$answers[0]->title*/?>
						</p>
					<?php /*endif;*/?>
					<p></p>
					<p>Вы правильно ответили на вопрос</p>
					<p><?/*=$question->comment_after*/?></p>
				</div>
			<?php /*elseif($isCorrect==2):*/?>
				<div class="alert alert-info">
					<?php /*if($isMultiple): */?>
						<p>Правильные ответы:<br>
						<?php /*foreach ($answers as $ans):*/?>
							<?/*=$ans->title*/?><br>
						<?php /*endforeach;*/?>

						</p>
					<?php /*else: */?>
						<p>
							Правильный ответ: <?/*=$answers[0]->title*/?>
						</p>
					<?php /*endif;*/?>
					<p></p>
					<p>Вы ответили на вопрос правильно частично</p>
					<p><?/*=$question->comment_after*/?></p>
				</div>
			<?php /*endif;*/?>
		--><?php /*endforeach; */?>
		<?php } ?>
	</div>
</div>
<div class="row">
	<div class="col-lg-12" style="text-align: center">
		<?= Html::a('Продолжить обучение',['/course/index'],['class'=>'btn btn-primary']) ?>
	</div>
</div>
