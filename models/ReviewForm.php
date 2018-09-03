<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 17.11.2017
 * Time: 14:58
 */

namespace app\models;

use app\modules\admin\models\settings\Main;
use pheme\settings\components\Settings;
use Yii;
use yii\base\Model;

class ReviewForm extends Model
{
    public $comment;
    public function rules()
    {
        return [
            ['comment', 'required'],
        ];
    }

    public function send()
    {
        if ($this->validate()) {
            $this->sendMail();
            return true;
        }
        return false;

    }

    public function attributeLabels()
    {
        return [
            'comment' => 'Отзыв'
        ];
    }

    protected function sendMail()
    {
        $setting = Yii::$app->settings; /** @var Settings $setting */
        $user = Yii::$app->user->identity; /** @var User $user */
        $user->review = User::REVIEW_SEND;
        $user->save(false);
        $username = !empty($user->fio) ? $user->fio : $user->username;
        if ($adminMail = Main::value('adminEmail')) {
            try {
                Yii::$app->mailer->compose()
                    ->setTo($adminMail)
                    ->setFrom([Yii::$app->params['adminEmail'] => Yii::$app->name])
                    ->setSubject($username . ' оставил(а) отзыв о курсе')
                    ->setTextBody($this->comment)
                    ->send();
            } catch (\Exception $e) {
                // i'm feeling lucky
            }
        }
    }
}