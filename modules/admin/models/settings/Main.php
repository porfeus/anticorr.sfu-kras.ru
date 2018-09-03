<?php

namespace app\modules\admin\models\settings;
use yii\base\Model;

/**
 * Created by PhpStorm.
 * User: User
 * Date: 02.08.2017
 * Time: 5:39
 */
class Main extends Model
{
    public $siteName;
    public $homeContent;
    public $contactsContent;
    public $adminEmail;

    public function rules()
    {
        return [
            [['siteName', 'homeContent', 'contactsContent', 'adminEmail'], 'string'],
            ['adminEmail', 'email'],
            [['siteName', 'homeContent', 'contactsContent', 'adminEmail'], 'default', 'value' => ''],
        ];
    }

    public function attributeLabels()
    {
        return [
            'siteName' => 'Название сайта',
            'homeContent' => 'Текст на главной странице',
            'contactsContent' => 'Текст на странице контактов',
            'adminEmail' => 'E-mail администратора (для уведомлений)',
        ];
    }

    public function fields()
    {
        return ['siteName', 'homeContent', 'contactsContent', 'adminEmail'];
    }

    public function attributes()
    {
        return ['siteName', 'homeContent', 'contactsContent', 'adminEmail'];
    }

    public static function value($attribute)
    {
        return \Yii::$app->settings->get($attribute, 'Main');
    }
}