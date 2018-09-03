<?php

namespace app\modules\admin\components;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Created by PhpStorm.
 * User: User
 * Date: 26.07.2017
 * Time: 18:46
 */
class BaseController extends Controller
{
    public $layout = '@app/modules/admin/views/layouts/main.php';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }
}