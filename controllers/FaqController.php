<?php

namespace app\controllers;

use app\components\SendMail;
use Yii;
use app\models\Faq;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Контроллер для вопросов-ответов
 */
class FaqController extends \yii\web\Controller
{
	/**
	 * Lists all Faq models.
	 * @return mixed
	 */
	public function actionIndex()
	{
		$model = new Faq();
		$model->scenario = Faq::SCENARIO_CREATE;
		$model->user_id = Yii::$app->user->id;
		$model->theme_id = Yii::$app->request->post('theme_id');

		$dataProvider = new ActiveDataProvider([
			'query' => Faq::find(),
			//'sort' => ['defaultOrder' => ['order_num' => SORT_DESC]]
		]);

		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', 'Ваш вопрос отправлен администрации');
			$emailFaq = 'wdb@mail.ru';

			$text = 'Задан вопрос:<br/>' . $model->question;

			/*if ($email = Main::value('adminEmail')) {
				Yii::$app->mailer->compose()
					->setTo($email)
					->setFrom([Yii::$app->params['adminEmail'] => Main::value('siteName')])
					->setSubject(sprintf('%s не прошел тесты по модулю', $userDone->user->username))
					->setTextBody(
						sprintf('%s не прошел тесты по %s', $userDone->user->username, $this->title) .
						sprintf("\nПодробнее: %s", Url::toRoute(['/admin/user/view', 'id' => $userDone->user_id, 'module_id' => $this->id], true))
					)
					->send();
			}*/


			SendMail::send($emailFaq, 'subject', ['text' => $text, 'email' => $emailFaq, 'question' => $model->question]);
			return $this->redirect([Yii::$app->request->post('url')]);
		}
		return $this->render('index', [
			'dataProvider' => $dataProvider,
			'model' => $model,
		]);
	}

	/**
	 * Finds the Faq model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * @param string $id
	 * @return Faq the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel($id)
	{
		if (($model = Faq::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
