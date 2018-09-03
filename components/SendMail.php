<?php
namespace app\components;

use yii;
use yii\base\Component;
use yii\helpers\Html;

/**
 * Компонент для отправки письма
 * @package app\components
 */
class SendMail extends Component
{
	/**
	 * Отправляем письмо
	 * @param $employees
	 * @param $myproducts
	 * @return bool
	 * @throws yii\db\Exception
     */
	public static function Send($to, $subject, $params, $html ='mail')
    {		
		$from = "no-reply@sfu-kras.ru";
		
		try
		{
			Yii::$app->mailer->compose(['html' => $html], $params)
				->setFrom([$from => "sfu-kras.ru"])
				->setTo($to)
				->setSubject($subject)
				->send();
		} catch (\Exception $e) {
			Yii::$app->getSession()->setFlash('error', $e->getMessage());
		}
    }
}