<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 27.07.2017
 * Time: 22:13
 */

namespace app\modules\api\controllers;

use app\modules\api\models\Theme;
use yii\rest\ActiveController;

class ThemeController extends ActiveController
{
    public $modelClass = 'app\modules\api\models\Theme';

    public function actionOrder()
    {
        if ($data = \Yii::$app->request->bodyParams) {
            if (is_array($data)) {

                foreach ($data as $key => $item) {
                    if (!isset($item['id']) /*|| !isset($item['module_id'])*/ || !is_numeric($key)) {
                        continue;
                    }
                    $set = [
                        'sort' => $key,
                        //'module_id' => $item['module_id']
                    ];
                    $condition = [
                        'id' => $item['id'],
                        //'module_id' => $item['module_id']
                    ];
                    Theme::updateAll($set, $condition);
                }
            }
        }
        return \Yii::$app->request->bodyParams;
    }
}